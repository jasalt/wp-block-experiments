(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;
	var ServerSideRender = wp.serverSideRender;

    // Template URL is passed from PHP
    var templateUrl = twigWasmTestBlockData.templateUrl;
    var template = null;

    // Fetch the Twig template
    fetch(templateUrl)
        .then(function(response) {
            return response.text();
        })
        .then(function(templateContent) {
			// TODO call function published from WASM
            template = Twig.twig({
                data: templateContent
            });
        })
        .catch(function(error) {
            console.error('Error loading Twig template:', error);
        });

    blocks.registerBlockType('my-plugin/twig-wasm-test-block', {
        title: 'Twig Wasm Test Block',
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
					// TODO render via Twig in WASM
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
                // preview
				// Render preview with PHP
				el(ServerSideRender, {
                    block: 'my-plugin/twig-wasm-test-block',
					attributes: attributes
                })
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
