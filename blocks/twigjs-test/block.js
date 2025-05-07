(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;
    
    // Template URL is passed from PHP
    var templateUrl = twigjsTestBlockData.templateUrl;
    var template = null;
    
    // Fetch the Twig template
    fetch(templateUrl)
        .then(function(response) {
            return response.text();
        })
        .then(function(templateContent) {
            template = Twig.twig({
                data: templateContent
            });
        })
        .catch(function(error) {
            console.error('Error loading Twig template:', error);
        });

    blocks.registerBlockType('my-plugin/twigjs-test-block', {
        title: 'TwigJS Test Block',
        icon: 'text',
        category: 'text',

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps = useBlockProps();
            
            // Render the preview using Twig.js
            var preview = el('div', { className: 'loading-template' }, 'Loading template...');
            
            if (template) {
                try {
                    var renderedHtml = template.render({
                        attributes: {
                            text: attributes.textContent,
                            is_bold: attributes.isBold
                        }
                    });
                    
                    preview = el('div', { 
                        className: 'twigjs-preview',
                        dangerouslySetInnerHTML: { __html: renderedHtml }
                    });
                } catch (error) {
                    console.error('Error rendering Twig template:', error);
                    preview = el('div', { className: 'template-error' }, 'Error rendering template');
                }
            }

            return el('div', blockProps, [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: 'Text Settings' },
                        el(ToggleControl, {
                            label: 'Bold Text',
                            checked: attributes.isBold,
                            onChange: function(value) {
                                setAttributes({ isBold: value });
                            }
                        })
                    )
                ),
                el(TextControl, {
                    label: 'Text Content',
                    value: attributes.textContent,
                    onChange: function(value) {
                        setAttributes({ textContent: value });
                    }
                }),
                
                // Client-side preview using Twig.js
                preview
            ]);
        },

        save: function() {
            return null; // Use server-side rendering
        }
    });
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor,
    window.wp.components
));
