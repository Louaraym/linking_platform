/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
let $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');