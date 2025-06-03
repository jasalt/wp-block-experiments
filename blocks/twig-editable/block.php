<?php
/**
 * Plugin Name: Twig Editable Block
 */


// Register the block
function register_twig_editable_block() {
    // Register Twig.js script from local static directory
    wp_register_script(
        'twigjs-library',
        plugins_url('static/twig_1.17.1.min.js', dirname(plugin_dir_path(__FILE__))),
        [],
        '1.17.1'
    );

    wp_register_script(
        'twig-editable-block-editor',
        plugins_url('block.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'twigjs-library'],
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );


    register_block_type('my-plugin/twig-editable-block', [
        'editor_script' => 'twig-editable-block-editor',
        'render_callback' => 'render_twig_editable_block',
        'attributes' => [
            'textContent' => [
                'type' => 'string',
                'default' => 'Your text here'
            ],
            'isBold' => [
                'type' => 'boolean',
                'default' => false
            ],
            'twigTemplate' => [
                'type' => 'string',
                'default' => '<div class="wp-block-twig-editable-text">
    {% if attributes.is_bold %}
        <strong>{{ attributes.text }}</strong>
    {% else %}
        <p>{{ attributes.text }}</p>
    {% endif %}
</div>'
            ]
        ]
    ]);
}
add_action('init', 'register_twig_editable_block');

// Server-side rendering with Twig
function render_twig_editable_block($attributes) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;
    $template_content = $attributes['twigTemplate'] ?? '<div>{{ attributes.text }}</div>';

    $loader = new \Twig\Loader\ArrayLoader([
        'template' => $template_content
    ]);

    $twig = new \Twig\Environment($loader);

    $context['attributes'] = [
        'text' => $text,
        'is_bold' => $is_bold
    ];

    return $twig->render('template', $context);
}
