const sass = require('node-sass');
const babel = require("rollup-plugin-babel");
const vue = require("rollup-plugin-vue");
const alias = require('rollup-plugin-alias');
const resolve = require("rollup-plugin-node-resolve");
const nodeGlobals = require("rollup-plugin-node-globals");
const commonjs = require("rollup-plugin-commonjs");
const replace = require("rollup-plugin-replace");
const uglify = require("rollup-plugin-uglify");


module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        rollup: {
            options: {
                format: "iife",
                interop: null,
                plugins: [
//                    alias({
//                        'vue': '../../node_modules/vue/dist/vue.runtime.esm.js'
//                    }),
                    replace({
                        'process.env.NODE_ENV': JSON.stringify('developer')
                    }),
                    babel({compact: false}),
                    resolve({
                        jsnext: true,
                        main: true,
                        browser: true
                    }),
                    vue.default({
                        compileTemplate: true,
                    }),
                    nodeGlobals(),
                    commonjs({
                        include: [],
                    }),
                ]
            },
            default: {
                options: {
                    moduleName: "NS"
                },
                files: {
                    "runtime/script.js": "assets/js/entry.js"
                }
            }
        },
        concat: {
            options: {
                separator: ";",
            },
            dist: {
                src: [
                    "runtime/script.js"
                ],
                dest: "web/js/app.js",
            },
        }
    });

    grunt.loadNpmTasks("grunt-rollup");
    grunt.loadNpmTasks("grunt-contrib-concat");

    grunt.registerTask("default", ["rollup", "concat"]);
    grunt.registerTask("dev", ["default"]);
};
