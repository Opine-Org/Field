var path = require("path");
var webpack = require("webpack");
var bower = require("bower-webpack-plugin");

var resolveBowerPath = function(componentPath) {
    return path.join(__dirname, '../bower_components', componentPath);
};

module.exports = {
    console: true,
    entry: "./entry.js",
    output: {
        path: __dirname + "/../public/build",
        filename: "bundle.js",
        publicPath: "/field/build/"
    },
    module: {
        loaders: [
            { test: /\.css$/,    loader: 'style!css' },
            { test: /\.woff$/,   loader: 'url-loader?mimetype=application/font-woff' },
            { test: /\.ttf$/,    loader: 'url-loader?mimetype=application/font-ttf' },
            { test: /\.eot$/,    loader: 'url-loader?mimetype=application/font-eot' },
            { test: /\.svg$/,    loader: 'url-loader?mimetype=iamge/svg' },
            { test: /\.png$/,    loader: 'url-loader?mimetype=image/png' },
            { test: /\.jpg$/,    loader: 'url-loader?mimetype=image/jpg' },
            { test: /\.gif$/,    loader: 'url-loader?mimetype=image/gif' }
        ]
    },
    resolve: {
        root: [path.join(__dirname, "bower_components")],
        alias: {
            'jquery.ui.widget': resolveBowerPath('jquery-file-upload/js/vendor/jquery.ui.widget.js')
        }
    },
    plugins: [
        new bower()
    ]
};
