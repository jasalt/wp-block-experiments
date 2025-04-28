(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;

    blocks.registerBlockType('my-plugin/no-build-block', {
        title: 'No Build Block',
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
                el('div', { className: 'preview' },
                    attributes.isBold
                        ? el('strong', {}, attributes.textContent)
                        : el('p', {}, attributes.textContent)
                )
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
