<?php
/**
 * Registers the Scittle Block with it's assets so it's available in editor.
 * Provides server-side (PHP) rendering function for the block HTML.
 */

use Phel\Phel;

$projectRootDir = __DIR__ . '/';
require $projectRootDir . 'vendor/autoload.php';

add_action('init', function (){
	wp_register_script(
		'phel-scittle-block-editor',
		plugins_url('block.js', __FILE__),
		array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
		filemtime(plugin_dir_path(__FILE__) . 'block.js')
	);
	wp_enqueue_script('phel-scittle-block-editor');
});

Phel::run($projectRootDir, 'phel-scittle-block\main');
