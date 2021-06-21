'use strict';

import cloneDeep from 'lodash.clonedeep';

import { series, parallel, dest, src, watch, task } from 'gulp';
import rollup from '@rollup/stream';
import sourcemaps from 'gulp-sourcemaps';
import source from 'vinyl-source-stream';
import buffer from 'vinyl-buffer';
import commonjs from '@rollup/plugin-commonjs';
import resolve from '@rollup/plugin-node-resolve';
import eslint from '@rollup/plugin-eslint';
import rollupCss from 'rollup-plugin-css-porter';
import gulpif from 'gulp-if';
import uglify from 'gulp-uglify';
import gulpsass from 'gulp-dart-sass'; // TODO: move to gulp-sass once they removed the node-sass depenency
import postcss from 'gulp-postcss';
import zip from 'gulp-zip';
import glob from 'glob';
import path from 'path';
import subprocess from 'child_process';
import fs from 'fs';

// css post-processing
import cssnano from 'cssnano';
import autoprefixer from 'autoprefixer';
import concatCss from 'gulp-concat-css';

var minify = false;

var postcss_plugins = [
	// always add autoprefixer
	autoprefixer(),
];
/*
	eslint Configuration
*/
var eslint_config = {
	"envs": ["es6"],
	"globals": [
		"window",
		"console",
		"document",
		"setInterval",
		"clearInterval",
		"setTimeout",
		"clearTimeout",
		"XMLHttpRequest",
		"btoa",
		"atob",
		"Audio",
		"MutationObserver",
		"URLSearchParams",
		// form vendor.js:
		"NoSleep",
		"$",
		"moment",
		"toastr",
		"bootbox",
	],
	"parserOptions": {
		"sourceType": "module",
		"ecmaVersion": 2018
	},
	baseConfig: {
		"extends": ["eslint:recommended"]
	},
	rules: {
		"no-unused-vars": ["error", { vars: "all", args: "none" }]
	}
};

var view_eslint_config = cloneDeep(eslint_config);
view_eslint_config.globals = eslint_config.globals.concat([
	"Grocy",
	"__t",
	"GrocyClass",
	"__n",
	"U",
	"RefreshContextualTimeago",
	"RefreshLocaleNumberDisplay",
	"RefreshLocaleNumberInput",
	"LoadImagesLazy",
	"GetUriParam",
	"UpdateUriParam",
	"RemoveUriParam",
	"EmptyElementWhenMatches",
	"animateCSS"
]);


// viewjs handling
var files = glob.sync('./js/viewjs/*.js');
var components = glob.sync('./js/viewjs/components/*.js');

var viewJStasks = [];

files.forEach(function(target)
{
	task(target, cb => rollup({
		input: target,
		output: {
			format: 'umd',
			name: path.basename(target),
			sourcemap: 'inline',
		},
		plugins: [resolve(), rollupCss({
			dest: path.resolve('./public/css/viewcss/' + path.basename(target).replace(".js", ".css")),
		}), commonjs(), eslint(view_eslint_config)],

	})
		.pipe(source(path.basename(target), "./js/viewjs"))
		.pipe(gulpif(minify, uglify()))
		.pipe(buffer())
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('./public/viewjs')));
	viewJStasks.push(target);
});
components.forEach(function(target)
{
	task(target, cb => rollup({
		input: target,
		output: {
			format: 'umd',
			name: path.basename(target),
			sourcemap: 'inline',
		},
		plugins: [resolve(), rollupCss({
			dest: path.resolve('./public/css/viewcss/' + path.basename(target).replace(".js", ".css")),
		}), commonjs(), eslint(view_eslint_config)],
	})
		.pipe(source(path.basename(target), "./js/viewjs/components"))
		.pipe(gulpif(minify, uglify()))
		.pipe(buffer())
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('./public/viewjs/components')));
	viewJStasks.push(target);
});

// The `clean` function is not exported so it can be considered a private task.
// It can still be used within the `series()` composition.
function clean(cb)
{
	// body omitted
	cb();
}

// The `build` function is exported so it is public and can be run with the `gulp` command.
// It can also be used within the `series()` composition.
function build(cb)
{
	// body omitted
	return parallel(
		js,
		css,
		vendor,
		viewjs,
		resourceFileCopy,
		copyLocales,
		makeLocales,
		done => { done(); cb(); })();
}

function publish(cb)
{
	minify = true;
	postcss_plugins.push(cssnano())
	return build();
}

function js(cb)
{
	return rollup({
		input: './js/grocy.js',
		output: {
			format: 'umd',
			name: 'grocy.js',
			sourcemap: 'inline',
		},
		plugins: [resolve(), commonjs(), eslint(eslint_config)],

	})
		.pipe(source('grocy.js', "./js"))
		.pipe(gulpif(minify, uglify()))
		.pipe(buffer())
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('./public/js'));
}

function viewjs(cb)
{
	return parallel(viewJStasks, done => { done(); cb(); })();
}


function vendor(cb)
{
	return rollup({
		input: './js/vendor.js',
		output: {
			format: 'umd',
			name: 'grocy.js',
			sourcemap: 'inline',
		},
		plugins: [resolve(), commonjs()],
	})
		.pipe(source('vendor.js', "./js"))
		.pipe(gulpif(minify, uglify()))
		.pipe(buffer())
		.pipe(sourcemaps.init({ loadMaps: true }))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('./public/js'));
}

function css(cb)
{
	return src('./scss/grocy.scss')
		.pipe(sourcemaps.init())
		.pipe(gulpsass({ includePaths: ['./node_modules'], quietDeps: true }).on('error', gulpsass.logError))
		.pipe(concatCss('grocy.css', { includePaths: ['./node_modules'], rebaseUrls: false }))
		.pipe(postcss(postcss_plugins))
		.pipe(sourcemaps.write('.'))
		.pipe(dest('./public/css'))
}

function resourceFileCopy(cb)
{
	return parallel(
		cb => src([
			'./node_modules/@fortawesome/fontawesome-free/webfonts/*'
		]).pipe(dest('./public/webfonts')),
		cb => src('./node_modules/summernote/dist/font/*').pipe(dest('./public/css/font')),
		done => { done(); cb(); }
	)();
}

async function makeLocales()
{
	return subprocess.exec("php buildfiles/generate-locales.php");
}

function copyLocales(cb)
{
	return parallel(
		cb => src('./node_modules/timeago/locales/*.js').pipe(dest('./public/js/locales/timeago')),
		cb => src('./node_modules/summernote/dist/lang/*.js').pipe(dest('./public/js/locales/summernote')),
		cb => src('./node_modules/bootstrap-select/dist/js/i18n/*').pipe(dest('./public/js/locales/bootstrap-select')),
		cb => src('./node_modules/fullcalendar/dist/locale/*').pipe(dest('./public/js/locales/fullcalendar')),
		cb => src('./node_modules/@fullcalendar/core/locales/*').pipe(dest('./public/js/locales/fullcalendar-core')),
		done => { done(); cb(); }
	)();
}

function live(cb)
{
	watch('./scss/**/*.scss', css);
	watch(['./js/**/*.js', '!./js/viewjs/**/*.js'], js);
	watch('./js/vendor.js', vendor);
	//watch('./js/viewjs/**/*.js', viewjs);
	viewJStasks.forEach(elem => watch(elem, series([elem])));
}

function release(cb)
{
	return series(publish, bundle, done => { done(); cb(); })();
}

function bundle(cb)
{
	var version = subprocess.spawnSync('git', ['describe', '--tags'], {
		cwd: process.cwd(),
		stdio: 'pipe',
	});
	var today = new Date();

	var versionObject = {
		Version: version.output[1].toString().substring(1).replace('\n', ''),
		ReleaseDate: today.getFullYear().toString() + "-" + today.getMonth().toString().padStart(2, "0") + "-" + today.getDay().toString().padStart(2, "0"),
	};

	fs.writeFileSync("version.json", JSON.stringify(versionObject, null, 2));

	return src([
		'**/*',
		'!yarn.lock',
		'!package.json',
		'!postcss.config.js',
		'!gulpfile.babel.js',
		'!composer.json',
		'!composer.lock',
		'!node_modules/**',
		'!js/',
		'!scss/',
		'!data/config.php',
		'!data/storage/**',
		'!data/grocy.db',
		'data/.htaccess',
		'public/.htaccess'
	]).pipe(zip('grocy-' + versionObject.Version + '.zip'))
		.pipe(dest('.release'))
}

export default publish;
export
{
	build,
	js,
	vendor,
	viewjs,
	css,
	live,
	clean,
	resourceFileCopy,
	copyLocales,
	publish,
	release,
	bundle,
	makeLocales
}