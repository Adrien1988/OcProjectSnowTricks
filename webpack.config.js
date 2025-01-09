const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Répertoire de sortie des fichiers compilés
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Point d'entrée principal
    .addEntry('app', './assets/app.js')

    // Optimisations et configurations
    .splitEntryChunks()
    .configureSplitChunks(function(splitChunks) {
        splitChunks.chunks = 'all';
        splitChunks.cacheGroups = {
            vendors: {
                test: /[\\/]node_modules[\\/]/,
                name: 'vendors',
                chunks: 'all',
            },
        };
    })
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())

    // Configuration de Babel
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // Activer PostCSS pour le traitement des fichiers CSS
    .enablePostCssLoader()

    // Support de SCSS (si nécessaire à l'avenir)
    //.enableSassLoader();
;

module.exports = Encore.getWebpackConfig();
