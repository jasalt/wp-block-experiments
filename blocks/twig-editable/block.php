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
            ],
            'boundValue' => [
                'type' => 'string',
                'default' => ''
            ],
            'metadata' => [
                'type' => 'object',
                'default' => []
            ]
        ],
        'supports' => [
            'align' => true,
            'html' => false
        ],
        'uses_context' => ['postId', 'postType']
    ]);
}
add_action('init', 'register_twig_editable_block');

// Server-side rendering with Twig
function render_twig_editable_block($attributes, $content, $block) {
    $text = $attributes['textContent'] ?? 'Default text';
    $is_bold = $attributes['isBold'] ?? false;
    $bound_value = '';
    $template_content = $attributes['twigTemplate'] ?? '<div>{{ attributes.text }}</div>';

    // Process block bindings if they exist
    if (isset($block->parsed_block['attrs']['metadata']['bindings'])) {
        $bindings = $block->parsed_block['attrs']['metadata']['bindings'];
        
        // Handle boundValue binding
        if (isset($bindings['boundValue'])) {
            $binding_source = $bindings['boundValue']['source'];
            $binding_args = isset($bindings['boundValue']['args']) ? $bindings['boundValue']['args'] : [];
            
            $registry = WP_Block_Bindings_Registry::get_instance();
            $source = $registry->get_registered($binding_source);
            
            if ($source) {
                $binding_value = $source->get_value($binding_args, $block, 'boundValue');
                if (is_array($binding_value) && isset($binding_value['content'])) {
                    $bound_value = $binding_value['content'];
                } elseif (is_string($binding_value)) {
                    $bound_value = $binding_value;
                }
            }
        }
    }

    // Fallback: if no binding worked, try direct post meta access for debugging
    if (empty($bound_value) && isset($block->parsed_block['attrs']['metadata']['bindings']['boundValue']['args']['property'])) {
        $meta_key = $block->parsed_block['attrs']['metadata']['bindings']['boundValue']['args']['property'];
        $post_id = $block->context['postId'] ?? get_the_ID();
        if ($post_id && $meta_key) {
            $meta_value = get_post_meta($post_id, $meta_key, true);
            if (!empty($meta_value)) {
                $bound_value = $meta_value;
            }
        }
    }

    $loader = new \Twig\Loader\ArrayLoader([
        'template' => $template_content
    ]);

    $twig = new \Twig\Environment($loader);

    $context = [
        'attributes' => [
            'text' => $text,
            'is_bold' => $is_bold
        ],
        'boundValue' => $bound_value
    ];

    return $twig->render('template', $context);
}
