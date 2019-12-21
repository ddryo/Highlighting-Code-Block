// @wp
import { __, PanelBody, RadioControl } from '../@wp';

/**
 * テキストドメイン
 */
const textDomain = 'loos-hcb';

// コンポーネントを export
export default (props) => {
    const { attributes, setAttributes } = props;
    let isLineShow = attributes.isLineShow;

    return (
        <PanelBody title={__('HCB settings', textDomain)} initialOpen={true}>
            <RadioControl
                label={__('Display number of lines in this block', textDomain)} //
                // help=''
                selected={isLineShow}
                options={[
                    {
                        label: __('Do not set individually', textDomain), //個別で設定はしない
                        value: 'undefined',
                    },
                    {
                        label: __('Display row count', textDomain), //行数を表示する
                        value: 'on',
                    },
                    {
                        label: __('Do not display row count', textDomain), //行数を表示しない
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
