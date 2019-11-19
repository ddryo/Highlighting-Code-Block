/**
 * wpオブジェクトの読み込み
 */
const { components, element } = wp,
    { InspectorControls } = wp.editor,
    { Fragment, createElement: el } = element,
    { addFilter } = wp.hooks,
    { createHigherOrderComponent } = wp.compose,
    { PanelBody, RadioControl } = components;


/**
 * Add HigherOrderComponent
 */
const addHcbControls = createHigherOrderComponent( (BlockEdit) => 
{
    return (props) => {

        let isLineShow = props.attributes.isLineShow;
        // if (typeof isLineShow === 'undefined') {
        //     isLineShow = 'undefined';
        // }

        const hcbComponent = (
            <PanelBody
                title='HCB設定'
                initialOpen={true}
            >
                <RadioControl
                    label='このブロックの行数表示'
                    // help=''
                    selected={isLineShow}
                    options={[
                        {
                            label: '個別設定はしない',
                            value: 'undefined'
                        }, {
                            label: '行数を表示する',
                            value: 'on'
                        }, {
                            label: '行数を表示しない',
                            value: 'off'
                        }
                    ]}
                    onChange={(val) => {
                        props.setAttributes({ isLineShow: val });
                    }}
                />
            </PanelBody>
        );

        if (props.name === 'loos-hcb/code-block' && props.isSelected) {
            return (<Fragment>
                <BlockEdit {...props} />
                <InspectorControls>
                    {hcbComponent}
                </InspectorControls>
            </Fragment>);
        }
        return <BlockEdit {...props} />

    }
}, 'addHcbControls');
addFilter('editor.BlockEdit', 'loos-hcb/hcb-control', addHcbControls);