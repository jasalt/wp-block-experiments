(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;


    blocks.registerBlockType('my-plugin/twig-editable-block', {
        title: 'Twig Editable Block',
        icon: 'embed-generic',
        category: 'text',

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps = useBlockProps();

            // Render the preview using Twig.js
            var preview = el('div', { className: 'loading-template' }, 'Loading template...');

            if (attributes.twigTemplate) {
                try {
                    var template = Twig.twig({
                        data: attributes.twigTemplate
                    });

                    var renderedHtml = template.render({
                        attributes: {
                            text: attributes.textContent,
                            is_bold: attributes.isBold
                        }
                    });

                    preview = el('div', {
                        className: 'twigjs-preview',
                        dangerouslySetInnerHTML: { __html: renderedHtml }  // NOTE: no auto-escape
                    });
                } catch (error) {
                    console.error('Error rendering Twig template:', error);
                    preview = el('div', { className: 'template-error' }, 'Error rendering template: ' + error.message);
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
                    ),
                    el(PanelBody, { title: 'Template Settings' },
                        el(TextareaControl, {
                            label: 'Twig Template',
                            value: attributes.twigTemplate,
                            onChange: function(value) {
                                setAttributes({ twigTemplate: value });
                            },
                            rows: 10
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
