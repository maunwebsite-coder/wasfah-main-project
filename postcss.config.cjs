const tailwindcss = require('@tailwindcss/postcss');
const autoprefixer = require('autoprefixer');
const cssnano = require('cssnano');

const plugins = [
    tailwindcss(),
    autoprefixer(),
];

if (process.env.NODE_ENV === 'production') {
    plugins.push(
        cssnano({
            preset: ['default', { discardComments: { removeAll: true } }],
        }),
    );
}

module.exports = { plugins };
