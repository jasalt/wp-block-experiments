<?php
/**
 * Registers the Scittle Block with it's assets so it's available in editor.
 * Provides server-side (PHP) rendering function for the block HTML.
 */

add_action('init', 'register_scittle_block');
function register_scittle_block() {

	//error_log("Load Scittle Block");  // for debugging

	// Dependencies

	// DOC: https://developer.wordpress.org/reference/functions/wp_register_script/

    wp_register_script(
        'scittle-core', // handle
        'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.js', // src // TODO serve local
        [],             // deps
		'0.6.22',       // ver
		true            // args  // TODO should be false?
    );

    // Register Reagent for UI components
    wp_register_script(
        'scittle-reagent',
        'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.reagent.js',
        ['scittle-core'], '0.6.22', true
    );

    // Register the main ClojureScript file
    wp_register_script(
        'scittle-block-editor-cljs',
        plugins_url('block.cljs', __FILE__),
        ['scittle-core', 'scittle-reagent'],
        filemtime(plugin_dir_path(__FILE__) . 'block.cljs')
    );

	// Add content-type header to the script tag
    wp_script_add_data('scittle-block-editor-cljs', 'type', 'application/x-scittle');


	// TODO nREPL
	// https://github.com/babashka/scittle/tree/main/doc/nrepl
	// wp_register_script('scittle-nrepl-port-set', '');
	// wp_add_inline_script('scittle-nrepl-port-set','var SCITTLE_NREPL_WEBSOCKET_PORT = 1340;');
	// wp_register_script('scittle-nrepl',
	// 	'https://cdn.jsdelivr.net/npm/scittle@0.6.22/dist/scittle.nrepl.js', ['scittle-nrepl-port-set']);
	// wp_enqueue_script('scittle-nrepl');


	// HACK: Register inline JavaScript which block editor executes by convention.
	//       It simply runs Scittle eval for our main ClojureScript script tag element.
	wp_register_script(
        'scittle-block-editor-runner', '',
        ['scittle-block-editor-cljs', 'scittle-core', 'scittle-reagent', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],  // TODO move general deps to CLJS file
		filemtime(__FILE__));

	wp_add_inline_script('scittle-block-editor-runner',
		'(function(){window.scittle.core.eval_script_tags([document.getElementById("scittle-block-editor-cljs-js")]);})();');

	// DOC: https://developer.wordpress.org/reference/functions/register_block_type/

    register_block_type('my-plugin/scittle-block', [
        'editor_script' => 'scittle-block-editor-runner',
        'render_callback' => 'render_scittle_block',
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


/*
 * Server-side PHP rendering function for the block which results to HTML that
 * visitor sees on the site. It could be also written e.g. with Phel for
 * full-stack lisp experience.
 */
function render_scittle_block($attributes) {
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
