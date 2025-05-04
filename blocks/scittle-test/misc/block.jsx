// Just example, not tested
import { registerBlockType } from '@wordpress/blocks';
import { TextControl, ToggleControl, Panel, PanelBody } from '@wordpress/components';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

registerBlockType('my-plugin/simple-text', {
    edit: ({ attributes, setAttributes }) => {
        const { textContent, isBold } = attributes;
        const blockProps = useBlockProps();

        return (
            <>
                <InspectorControls>
                    <Panel>
                        <PanelBody title="Text Settings">
                            <ToggleControl
                                label="Bold Text"
                                checked={isBold}
                                onChange={(value) => setAttributes({ isBold: value })}
                            />
                        </PanelBody>
                    </Panel>
                </InspectorControls>

                <div {...blockProps}>
                    <TextControl
                        label="Text Content"
                        value={textContent}
                        onChange={(value) => setAttributes({ textContent: value })}
                    />
                    <div className="preview">
                        {isBold ? <strong>{textContent}</strong> : <p>{textContent}</p>}
                    </div>
                </div>
            </>
        );
    },
    save: () => null, // Use server-side rendering
});
