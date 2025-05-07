<?php
/*
Plugin Name: Scittle WP Block
Description: Demo block made with ClojureScript
Version: 0.1
Author: Jarkko Saltiola
Author URI: https://codeberg.org/jasalt
*/


$projectRootDir = __DIR__ . '/';
require $projectRootDir . 'vendor/autoload.php';

require $projectRootDir . 'blocks/scittle-test/scittle-test.php';
require $projectRootDir . 'blocks/no-build-test/no-build-test.php';
require $projectRootDir . 'blocks/phel-scittle-test/phel-scittle-test.php';
require $projectRootDir . 'blocks/timber-test/timber-test.php';
