import { createReadStream, createWriteStream } from 'node:fs';
import fs from 'node:fs/promises';
import path from 'node:path';
import { pipeline } from 'node:stream/promises';
import { createBrotliCompress, createGzip, constants as zlibConstants } from 'node:zlib';

const DEFAULT_ROOT = path.resolve(process.cwd(), 'public');
const rootDir = path.resolve(process.cwd(), process.argv[2] ?? DEFAULT_ROOT);
const MIN_SIZE = 512;
const MATCH_EXT = /\.(?:js|mjs|cjs|css|html?|json|svg|xml|txt|webmanifest|wasm)$/i;

const strategies = [
    {
        ext: '.br',
        factory: () =>
            createBrotliCompress({
                params: {
                    [zlibConstants.BROTLI_PARAM_MODE]: zlibConstants.BROTLI_MODE_TEXT,
                    [zlibConstants.BROTLI_PARAM_QUALITY]: 11,
                },
            }),
    },
    {
        ext: '.gz',
        factory: () => createGzip({ level: zlibConstants.Z_BEST_COMPRESSION }),
    },
];

const walk = async (dir) => {
    const entries = await fs.readdir(dir, { withFileTypes: true });
    const files = await Promise.all(
        entries.map(async (entry) => {
            const fullPath = path.join(dir, entry.name);
            if (entry.isDirectory()) {
                return walk(fullPath);
            }
            return fullPath;
        }),
    );
    return files.flat();
};

const shouldProcess = (file, size) =>
    !file.endsWith('.gz') &&
    !file.endsWith('.br') &&
    MATCH_EXT.test(file) &&
    size >= MIN_SIZE;

const isFresh = async (source, target) => {
    try {
        const [srcStat, targetStat] = await Promise.all([fs.stat(source), fs.stat(target)]);
        return targetStat.mtimeMs >= srcStat.mtimeMs && targetStat.size > 0;
    } catch {
        return false;
    }
};

const compressWithStrategy = async (file, strategy) => {
    const target = `${file}${strategy.ext}`;
    if (await isFresh(file, target)) {
        return null;
    }

    await fs.mkdir(path.dirname(target), { recursive: true });
    await pipeline(createReadStream(file), strategy.factory(), createWriteStream(target));

    const [srcStat, targetStat] = await Promise.all([fs.stat(file), fs.stat(target)]);
    const ratio = targetStat.size / srcStat.size;
    return { file, ext: strategy.ext, ratio };
};

const run = async () => {
    const files = (await walk(rootDir)).filter(Boolean);
    const tasks = [];

    for (const file of files) {
        const stat = await fs.stat(file);
        if (!shouldProcess(file, stat.size)) {
            continue;
        }

        for (const strategy of strategies) {
            tasks.push(compressWithStrategy(file, strategy));
        }
    }

    const results = (await Promise.all(tasks)).filter(Boolean);
    if (results.length === 0) {
        console.log(`No assets required compression under ${rootDir}.`);
        return;
    }

    const summary = results
        .map(
            ({ file, ext, ratio }) =>
                `${path.relative(rootDir, file)}${ext} (${(ratio * 100).toFixed(1)}% of original)`,
        )
        .join('\n');

    console.log(`Compressed ${results.length} asset variants under ${rootDir}:\n${summary}`);
};

run().catch((error) => {
    console.error('Failed to compress assets:', error);
    process.exitCode = 1;
});
