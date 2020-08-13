/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, RadioControl } from '@wordpress/components';

/**
 * テキストドメイン
 */
const textDomain = 'loos-hcb';

// コンポーネントを export
export default (props) => {
	const { attributes, setAttributes } = props;
	const { isLineShow, isShowLang } = attributes;

	return (
		<PanelBody title={__('HCB settings', textDomain)} initialOpen={true}>
			<RadioControl
				label={__(
					'Settings for displaying the number of lines',
					textDomain
				)} // 行数の表示に関する設定
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

			<RadioControl
				label={__('Settings for displaying language name', textDomain)} // 言語名の表示に関する設定
				selected={isShowLang}
				options={[
					{
						label: __('Do not set individually', textDomain), //個別で設定はしない
						value: '',
					},
					{
						label: __('Display language', textDomain), //言語を表示する
						value: '1',
					},
					{
						label: __('Do not display language', textDomain), //言語を表示しない
						value: '0',
					},
				]}
				onChange={(val) => {
					setAttributes({ isShowLang: val });
				}}
			/>
		</PanelBody>
	);
};
