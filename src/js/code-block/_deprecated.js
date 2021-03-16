/**
 * @WordPress dependencies
 */
import { RichText } from '@wordpress/block-editor';

/**
 * @Inner dependencies
 */
import { sanitizeCodeblock } from './_utils';

/**
 * @Other dependencies
 */
import classnames from 'classnames';

export default [
	{
		supports: {
			className: false,
		},
		attributes: {
			code: {
				type: 'string',
				source: 'text',
				selector: 'code',
			},
			className: {
				type: 'string',
				default: '',
			},
			langType: {
				type: 'string',
				default: '',
			},
			langName: {
				type: 'string',
				default: '',
			},
			fileName: {
				type: 'string',
				default: '',
			},
			dataLineNum: {
				type: 'string',
				default: '',
			},
			isLineShow: {
				type: 'string',
				default: 'undefined',
			},
			isShowLang: {
				type: 'string',
				default: '',
			},
		},
		save: ({ attributes }) => {
			const { code, fileName, langName, dataLineNum, isLineShow, isShowLang } = attributes;
			const langType = attributes.langType || 'plain';

			let preClass = classnames('prism', `lang-${langType}`);
			if ('undefined' !== isLineShow) {
				preClass = classnames(preClass, `${isLineShow}-numbers`);
			}

			return (
				<div className='hcb_wrap'>
					<pre
						className={preClass}
						data-file={fileName || null}
						data-lang={langName || null}
						data-line={dataLineNum || null}
						data-show-lang={isShowLang || null}
					>
						<RichText.Content tagName='code' value={sanitizeCodeblock(code)} />
					</pre>
				</div>
			);
		},
	},
	{
		supports: {
			className: false,
		},
		attributes: {
			code: {
				type: 'string',
				source: 'text',
				selector: 'code',
			},
			className: {
				type: 'string',
				default: '',
			},
			langType: {
				type: 'string',
				default: 'plane',
			},
			langName: {
				type: 'string',
				default: '',
			},
			fileName: {
				type: 'string',
				default: '',
			},
			dataLineNum: {
				type: 'string',
				default: '',
			},
			isLineShow: {
				type: 'string',
				default: 'undefined',
			},
			isShowLang: {
				type: 'string',
				default: '',
			},
		},

		migrate: (attributes) => {
			if ('plane' === attributes.langType) {
				attributes.langType = '';
			}
			return attributes;
		},

		save: ({ attributes }) => {
			const {
				code,
				langType,
				fileName,
				langName,
				dataLineNum,
				isLineShow,
				isShowLang,
			} = attributes;

			// preタグにつけるクラス
			const preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;

			return (
				<div className='hcb_wrap'>
					<pre
						className={preClass}
						data-file={fileName || null}
						data-lang={langName || null}
						data-line={dataLineNum || null}
						data-show-lang={isShowLang || null}
					>
						<RichText.Content tagName='code' value={sanitizeCodeblock(code)} />
					</pre>
				</div>
			);
		},
	},
];
