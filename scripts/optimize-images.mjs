import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import sharp from 'sharp';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const projectRoot = path.resolve(__dirname, '..');

const targets = [
    {
        name: 'logo',
        source: path.join(projectRoot, 'public', 'image', 'logo.png'),
        widths: [96, 192, 384],
        quality: { webp: 90, avif: 65 },
    },
    {
        name: 'Brownies',
        source: path.join(projectRoot, 'public', 'image', 'Brownies.png'),
        widths: [400, 800, 1200],
        quality: { webp: 82, avif: 60 },
    },
    {
        name: 'works',
        source: path.join(projectRoot, 'public', 'image', 'works.png'),
        widths: [768, 1280, 1920],
    },
    {
        name: 'mworks',
        source: path.join(projectRoot, 'public', 'image', 'mworks.png'),
        widths: [480, 768, 1080],
    },
    {
        name: 'term',
        source: path.join(projectRoot, 'public', 'image', 'term.png'),
        widths: [768, 1280, 1920],
    },
    {
        name: 'wterm',
        source: path.join(projectRoot, 'public', 'image', 'wterm.png'),
        widths: [768, 1280, 1920],
    },
];

const defaultQuality = {
    webp: { quality: 85, effort: 5 },
    avif: { quality: 60, effort: 4 },
};

const formats = [
    { ext: 'avif', optionsKey: 'avif' },
    { ext: 'webp', optionsKey: 'webp' },
];

const ensureFile = async (filePath) => {
    try {
        await fs.access(filePath);
        return true;
    } catch {
        console.warn(`Skipping ${filePath} because it does not exist.`);
        return false;
    }
};

const optimizeImage = async (target) => {
    if (!(await ensureFile(target.source))) {
        return;
    }

    const image = sharp(target.source, { limitInputPixels: false });
    const meta = await image.metadata();
    const maxAvailableWidth = meta.width ?? Math.max(...target.widths);

    const effectiveWidths = target.widths
        .filter((width) => width <= maxAvailableWidth)
        .sort((a, b) => a - b);

    const outputDir = path.dirname(target.source);

    await Promise.all(
        effectiveWidths.map(async (width) => {
            const resized = image.clone().resize({
                width,
                withoutEnlargement: true,
                fit: sharp.fit.cover,
            });

            await Promise.all(
                formats.map(async ({ ext }) => {
                    const outputPath = path.join(outputDir, `${target.name}-${width}.${ext}`);
                    const formatOptions =
                        target.quality?.[ext] ?? defaultQuality[ext] ?? {};

                    await resized.clone().toFormat(ext, formatOptions).toFile(outputPath);
                    return outputPath;
                }),
            );
        }),
    );

    // Create canonical default files pointing to the largest rendition.
    const largestWidth = Math.max(...effectiveWidths);
    await Promise.all(
        formats.map(async ({ ext }) => {
            const sourceFile = path.join(outputDir, `${target.name}-${largestWidth}.${ext}`);
            const canonicalFile = path.join(outputDir, `${target.name}.${ext}`);
            await fs.rm(canonicalFile, { force: true });
            await fs.copyFile(sourceFile, canonicalFile);
        }),
    );

    console.log(`Optimized ${target.name} (${effectiveWidths.join(', ')} widths).`);
};

const run = async () => {
    await Promise.all(targets.map((target) => optimizeImage(target)));
    console.log('Image optimization complete.');
};

run().catch((error) => {
    console.error('Failed to optimize images:', error);
    process.exitCode = 1;
});




