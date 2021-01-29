const webpack = require("webpack");

module.exports = {
    entry: "./src/index.jsx",
    output: {
        path: __dirname,
        filename: "./assets/js/settings-field-boxes.js"
    },
    module: {
        loaders: [
            {
                test: /.jsx$/,
                loader: "babel-loader",
                exclude: /node_modules/,
                options: {
                    presets: [["env", "react"]],
                    plugins: ["transform-class-properties"]
                }
            }
        ]
    },
    plugins: [
        new webpack.optimize.UglifyJsPlugin({
            include: /\.min\.js$/,
            minimize: true
        })
    ]};
