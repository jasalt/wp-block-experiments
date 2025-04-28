<?php
/**
 * Scittle Block
 */

function register_scittle_block() {
	//error_log("Load Scittle Block");

	// Dependencies

    // Register Scittle core
    wp_register_script(
        'scittle-core',
        'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.js',
        [], '0.6.22', true
    );

    // Register Reagent for UI components
    wp_register_script(
        'scittle-reagent',
        'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.reagent.js',
        ['scittle-core'], '0.6.22', true
    );

    // Register our ClojureScript block code
    wp_register_script(
        'scittle-block-editor-cljs',
        plugins_url('block.cljs', __FILE__),
        ['scittle-core', 'scittle-reagent'],
        filemtime(plugin_dir_path(__FILE__) . 'block.cljs')
    );

    wp_script_add_data('scittle-block-editor-cljs', 'type', 'application/x-scittle');

	// TODO nREPL
	// https://github.com/babashka/scittle/tree/main/doc/nrepl
	// wp_register_script('scittle-nrepl-port-set', '');
	// wp_add_inline_script('scittle-nrepl-port-set','var SCITTLE_NREPL_WEBSOCKET_PORT = 1340;');
	// wp_register_script('scittle-nrepl',
	// 	'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.nrepl.js', ['scittle-nrepl-port-set']);
	// wp_enqueue_script('scittle-nrepl');

	// Register js that gets run by block editor executing scittle tag
	wp_register_script(
        'scittle-block-editor-runner', '',
        ['scittle-block-editor-cljs', 'scittle-core', 'scittle-reagent', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        filemtime(__FILE__));

	wp_add_inline_script('scittle-block-editor-runner',
		'(function(){window.scittle.core.eval_script_tags([document.getElementById("scittle-block-editor-cljs-js")]);})();');

    register_block_type('my-plugin/scittle-block', [
        'editor_script' => 'scittle-block-editor-runner',
        'render_callback' => 'scittle_block',
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
add_action('init', 'register_scittle_block');

// Server-side rendering
function scittle_block($attributes) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;

    $html = '<div class="wp-block-scittle-text">';
    if ($is_bold) {
        $html .= '<strong>' . esc_html($text) . '</strong>';
    } else {
        $html .= '<p>' . esc_html($text) . '</p>';
    }
    $html .= '</div>';

    return $html;
}
