/* ---
  Docs: https://www.npmjs.com/package/mati-mix/
--- */
const mix = require('mati-mix');

mix.js([
    'assets-src/rules-settings/js/index.jsx',
], 'assets/js/rules-settings.js');
mix.sass(
    'assets-src/rules-settings/scss/style.scss'
    , 'assets/css/rules-settings.css');

//Onboarding
mix.js( ['assets-src/onboarding/js/index.jsx',], 'assets/js/onboarding.js' );
mix.sass( 'assets-src/onboarding/scss/style.scss', 'assets/css/onboarding.css' );

mix.mix.babelConfig({
    "presets": [
        "@babel/preset-env",
        "@babel/preset-react",
    ],
    "plugins": ["@babel/plugin-proposal-class-properties"]
});

