<?php
/*
Plugin Name: WP Block Experiments
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
require $projectRootDir . 'blocks/twigjs-test/twigjs-test.php';
require $projectRootDir . 'blocks/twig-wasm-test/twig-wasm-test.php';

require $projectRootDir . 'blocks/twig-editable/block.php';
