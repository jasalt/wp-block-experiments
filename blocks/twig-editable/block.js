(function(blocks, element, blockEditor, components) {
    var el = element.createElement;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var ToggleControl = components.ToggleControl;
    var ComboboxControl = components.ComboboxControl;
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

            // Get available binding sources
            var getBindingSources = function() {
                try {
                    if (wp.blocks && wp.blocks.getBlockBindingsSources) {
                        var bindingSources = wp.blocks.getBlockBindingsSources();
                        return Object.keys(bindingSources).map(function(sourceKey) {
                            var source = bindingSources[sourceKey];
                            return {
                                value: sourceKey,
                                label: source.label || sourceKey
                            };
                        });
                    }
                } catch (e) {
                    console.warn('Could not access block bindings sources:', e);
                }
                return [];
            };


            // Helper function for updating bindings
            var updateBinding = function(key, value) {
                // Create a deep copy to avoid mutation issues
                var newMetadata = JSON.parse(JSON.stringify(attributes.metadata || {}));
                newMetadata.bindings = newMetadata.bindings || {};
                newMetadata.bindings.boundValue = newMetadata.bindings.boundValue || {};

                if (key === 'source') {
                    newMetadata.bindings.boundValue.source = value;
                } else if (key === 'property') {
                    if (value) {
                        newMetadata.bindings.boundValue.args = newMetadata.bindings.boundValue.args || {};
                        newMetadata.bindings.boundValue.args.property = value;
                    } else {
                        // Remove args if property is empty
                        if (newMetadata.bindings.boundValue.args) {
                            delete newMetadata.bindings.boundValue.args.property;
                            if (Object.keys(newMetadata.bindings.boundValue.args).length === 0) {
                                delete newMetadata.bindings.boundValue.args;
                            }
                        }
                    }
                }

                setAttributes({ metadata: newMetadata });
            };

            var getBindingValue = function(key) {
                if (!attributes.metadata || !attributes.metadata.bindings || !attributes.metadata.bindings.boundValue) {
                    return '';
                }
                if (key === 'source') {
                    return attributes.metadata.bindings.boundValue.source || '';
                }
                if (key === 'property') {
                    return (attributes.metadata.bindings.boundValue.args && attributes.metadata.bindings.boundValue.args.property) || '';
                }
                return '';
            };

            var bindingSources = getBindingSources();
            var currentSource = getBindingValue('source');

            // Render the preview using Twig.js
            var preview = el('div', { className: 'loading-template' }, 'Loading template...');

            if (attributes.twigTemplate) {
                try {
                    var template = Twig.twig({
                        data: attributes.twigTemplate
                    });

                    // Determine boundValue for preview
                    var previewBoundValue = '';
                    if (attributes.metadata && attributes.metadata.bindings && attributes.metadata.bindings.boundValue) {
                        var binding = attributes.metadata.bindings.boundValue;
                        var source = binding.source || '';
                        var property = (binding.args && binding.args.property) || '';
                        
                        if (source && property) {
                            previewBoundValue = '[Preview: ' + source + ' → ' + property + ']';
                        } else if (source) {
                            previewBoundValue = '[Preview: ' + source + ']';
                        }
                    }

                    var renderedHtml = template.render({
                        attributes: {
                            text: attributes.textContent,
                            is_bold: attributes.isBold
                        },
                        boundValue: previewBoundValue
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
                    el(PanelBody, { title: 'Block Bindings' },
                        el(ComboboxControl, {
                            label: 'Binding Source',
                            help: 'Binding source for boundValue variable',
                            value: currentSource,
                            options: bindingSources,
                            onChange: function(value) {
                                updateBinding('source', value);
                            },
                            allowReset: true
                        }),

                        el(TextControl, {
                            label: 'Binding Property',
                            help: 'Property for binding (e.g., content, title, excerpt)',
                            value: getBindingValue('property'),
                            onChange: function(value) {
                                updateBinding('property', value);
                            }
                        })
                    ),
                    el(PanelBody, { title: 'Template Settings' },
                        el(TextareaControl, {
                            label: 'Twig Template',
                            help: 'Use {{ boundValue }} to access the bound data',
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
