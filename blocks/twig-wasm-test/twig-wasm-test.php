<?php
/**
 * Plugin Name: TwigJS Test Block
 */

use Timber\Timber;

// Register the block
function register_twig_wasm_test_block() {
    // Register Twig.js script from local static directory
    wp_register_script(
        'twigjs-library',
        plugins_url('static/twig_1.17.1.min.js', dirname(plugin_dir_path(__FILE__))),
        [],
        '1.17.1'
    );

    wp_register_script(
        'twig-wasm-test-block-editor',
        plugins_url('block.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'twigjs-library'],
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

    $context = Timber::context();

    $context['attributes'] = [
        'text' => $text,
        'is_bold' => $is_bold
    ];

    return Timber::render('template.twig', $context);  // TODO compile to avoid prints before editor renders?
}
