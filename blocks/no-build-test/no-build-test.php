<?php
/**
 * Plugin Name: No Build Block
 */

error_log("Load No Build Block");

// Register the block
function register_no_build_block() {
    wp_register_script(
        'no-build-block-editor',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );

    register_block_type('my-plugin/no-build-block', array(
        'editor_script' => 'no-build-block-editor',
        'render_callback' => 'render_no_build_block',
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
add_action('init', 'register_no_build_block');

// Server-side rendering
function render_no_build_block($attributes) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;

    $html = '<div class="wp-block-no-build-text">';
    if ($is_bold) {
        $html .= '<strong>' . esc_html($text) . '</strong>';
    } else {
        $html .= '<p>' . esc_html($text) . '</p>';
    }
    $html .= '</div>';

    return $html;
}
