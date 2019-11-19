// @wp
import { PanelBody, RadioControl } from '../@wp';

// コンポーネントを export
export default (props) => {
    const { attributes, setAttributes, clientId, className } = props;
    let isLineShow = attributes.isLineShow;

    return (
        <PanelBody title='HCB設定' initialOpen={true}>
            <RadioControl
                label='このブロックの行数表示'
                // help=''
                selected={isLineShow}
                options={[
                    {
                        label: '個別設定はしない',
                        value: 'undefined',
                    },
                    {
                        label: '行数を表示する',
                        value: 'on',
                    },
                    {
                        label: '行数を表示しない',
                        value: 'off',
                    },
                ]}
                onChange={(val) => {
                    setAttributes({ isLineShow: val });
                }}
            />
        </PanelBody>
    );
};
