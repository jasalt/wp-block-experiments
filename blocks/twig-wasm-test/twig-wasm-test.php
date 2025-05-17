<?php
/**
 * Plugin Name: TwigJS Test Block
 */

use Timber\Timber;

// Register the block
function register_twig_wasm_test_block() {
    // Register Twig.js script from local static directory
    wp_register_script_module(
        'php-wasm-library',
        plugins_url('node_modules/php-wasm/PhpWeb.mjs',
					dirname(plugin_dir_path(__FILE__))),
        [],
        false  // (version)
    );


	wp_enqueue_script_module(
		'php-wasm-library',
		'', [], false);

	// HACK: Register inline JavaScript which block editor executes by convention.
	//       It simply runs Scittle eval for our main ClojureScript script tag element.
	// wp_register_script_module(
    //     'php-wasm-block-editor-initializer',
	// 	plugins_url('blocks/twig-wasm-test/wasmInitializer.js',
	// 				dirname(plugin_dir_path(__FILE__))),
    //     [],
	// 	filemtime(__FILE__));

	// wp_enqueue_script_module(
	// 	'php-wasm-block-editor-initializer',
	// 	'', [], false);

	/* wp_add_inline_script('php-wasm-block-editor-initializer',
	   '(function(){alert("foo"); console.log("ASDF");})();');
	 */

    wp_register_script(
        'twig-wasm-test-block-editor',
        plugins_url('block.js', __FILE__),
        ['php-wasm-block-editor-initializer', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );

    // Register the template for client-side rendering
    wp_localize_script(
        'twig-wasm-test-block-editor',
        'twigWasmTestBlockData',
        [
        'templateUrl' => plugins_url('template.twig', __FILE__),
        ]
    );





    register_block_type('my-plugin/twig-wasm-test-block', [
    'editor_script' => 'twig-wasm-test-block-editor',
    'render_callback' => 'render_twig_wasm_test_block',
    'attributes' => [
    'textContent' => [
    'type' => 'string',
    'default' => 'Your text here'
            ],
    'isBold' => [
    'type' => 'boolean',
    'default' => false
            ]
        ]
    ]);
}
add_action('init', 'register_twig_wasm_test_block');

// Server-side rendering with Timber
function render_twig_wasm_test_block($attributes) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;

	// ArrayLoader can work better if template comes as fn argument
	//$loader = new \Twig\Loader\ArrayLoader([
    //    'index' => 'Hello {{ name }}!'
    //]);
	//return $twig->render('index', ['name' => $text]);

	$loader = new \Twig\Loader\FilesystemLoader([
        __DIR__
    ]);

	$twig = new \Twig\Environment($loader);

	$context['attributes'] = [
    'text' => $text,
    'is_bold' => $is_bold
    ];

	return $twig->render('template.twig', $context);
		}
