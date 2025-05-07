(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;
    var ServerSideRender = wp.serverSideRender;

    blocks.registerBlockType('my-plugin/timber-test-block', {
        title: 'Timber Test Block',
        icon: 'text',
        category: 'text',

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps = useBlockProps();

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

                // Render preview with PHP/Timber
                el(ServerSideRender, {
                    block: 'my-plugin/timber-test-block',
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
