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
			template = templateContent;
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

					if (typeof window.PhpWeb === 'undefined'){
						console.log("WASM module not loaded");

						var renderedHtml = template;
						renderedHtml += attributes.textContent;
						renderedHtml += attributes.isBold;

					} else {
						console.log("WASM module loaded");
						// TODO Init Wasm if not inited

						if (typeof window.php == 'undefined') {
							console.log("Initialize PhpWeb");
							window.php = new PhpWeb({});
							php.addEventListener('output',
								(event) => console.log(event.detail));
							php.addEventListener('error',
								(event) => console.log(event.detail));
							php.run("HELLO FROM PHP");
						}


						// if (window.php) {
						// } else {
						//
						// }
						// .render({
						//     attributes: {
						//         text: attributes.textContent,
						//         is_bold: attributes.isBold
						//     }
						// });


						var renderedHtml = template;
						renderedHtml += attributes.textContent;
						renderedHtml += attributes.isBold;

					}


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
                preview

				// Render preview with PHP
				// el(ServerSideRender, {
                //     block: 'my-plugin/twig-wasm-test-block',
				// 	attributes: attributes
                // })

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
