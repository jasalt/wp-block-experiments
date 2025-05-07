<?php
/**
 * Plugin Name: Timber Test Block
 */

// Create a new block based on `no-build-test` called `timber-test`.
// It should have same functionality but use Timber / Twig rendering engine.
// In render_callback fn use `Timber::render(timber-test.twig', $context);` to render the .twig template.

use Timber\Timber;

// Register the block
function register_timber_test_block() {
    wp_register_script(
        'timber-test-block-editor',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );

    register_block_type('my-plugin/timber-test-block', array(
        'editor_script' => 'timber-test-block-editor',
        'render_callback' => 'render_timber_test_block',
        'attributes' => array(
            'textContent' => array(
                'type' => 'string',
                'default' => 'Your text here'
            ),
            'isBold' => array(
                'type' => 'boolean',
                'default' => false
            )
        )
    ));
}
add_action('init', 'register_timber_test_block');

// Server-side rendering with Timber
function render_timber_test_block($attributes) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;

	$context = Timber::context();

    $context["attributes"] = [
        'text' => $text,
        'is_bold' => $is_bold
    ];

	$html = Timber::compile('timber-test.twig', $context);
    return $html;
}
