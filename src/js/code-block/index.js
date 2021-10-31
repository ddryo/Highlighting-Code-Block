/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType, createBlock } from '@wordpress/blocks';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
// import { addFilter, applyFilters, hasFilter } from '@wordpress/hooks';
// import { useSelect } from '@wordpress/data';

/**
 * @Inner dependencies
 */
import metadata from './block.json';
import hcbIcon from './_icon';
import HcbSidebar from './_sidebar';
import deprecated from './_deprecated';
import { setHeightCodeBlocks, sanitizeCodeblock } from './_utils';

/**
 * @Other dependencies
 */
import classnames from 'classnames';

/**
 * 言語情報をグローバル変数から受け取る。
 */
let hcbLangs = window.hcbLangs;
if ('object' !== typeof hcbLangs) {
	hcbLangs = {
		html: 'HTML',
		css: 'CSS',
		scss: 'SCSS',
		js: 'JavaScript',
		ts: 'TypeScript',
		php: 'PHP',
		ruby: 'Ruby',
		python: 'Python',
		swift: 'Swift',
		c: 'C',
		csharp: 'C#',
		cpp: 'C++',
		objectivec: 'Objective-C',
		sql: 'SQL',
		json: 'JSON',
		bash: 'Bash',
		git: 'Git',
	};
}

const langList = [
	{
		value: '',
		label: '- Lang Select -',
	},
];

Object.keys(hcbLangs).forEach((key) => {
	langList.push({
		value: key,
		label: hcbLangs[key],
	});
});

/**
 * Register Highlighting Code Block
 */
registerBlockType(metadata.name, {
	icon: hcbIcon,
	attributes: metadata.attributes,
	transforms: {
		from: [
			//どのブロックタイプから変更できるようにするか
			{
				type: 'block',
				blocks: ['core/code'], //整形済みブロック : 'core/preformatted',
				transform: (attributes) => {
					return createBlock('loos-hcb/code-block', {
						code: attributes.content,
					});
				},
			},
		],
		to: [
			//どのブロックタイプへ変更できるようにするか
			{
				type: 'block',
				blocks: ['core/code'],
				transform: (attributes) => {
					return createBlock('core/code', {
						content: attributes.code,
					});
				},
			},
		],
	},

	edit: (props) => {
		const { attributes, setAttributes, clientId } = props;
		const {
			code,
			// langType,
			fileName,
			langName,
			dataLineNum,
			isShowLang,
			isLineShow,
		} = attributes;
		const blockClass = classnames('hcb_wrap', 'hcb-block');

		// クラスの重複バグの修正

		useEffect(() => {
			const nowClass = attributes.className;
			if (!nowClass) return;

			const nowClasses = nowClass.split(' ');
			const newClasses = [...new Set(nowClasses)]; // 重複削除
			setAttributes({ className: classnames(newClasses) });
		}, []);

		// コードの textarea 高さセット
		useEffect(() => {
			// console.log('useEffect');
			setTimeout(() => {
				// ちょっと遅らせないと null になる
				const hcbBlock = document.getElementById('block-' + clientId);
				const hcbTextarea = hcbBlock.querySelector('.hcb_textarea');
				setHeightCodeBlocks(hcbTextarea);
			}, 10);
		}, [clientId, code]);

		// preタグにつけるクラス名を生成して保存
		// let preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;
		// setAttributes({ preClass: preClass });

		const hcbShowLang = window.hcbVars?.showLang;
		const hcbShowLinenum = window.hcbVars?.showLinenum;

		let dataShowLang = '0';
		if ('1' === isShowLang) {
			dataShowLang = '1';
		} else if ('' === isShowLang && 'on' === hcbShowLang) {
			dataShowLang = '1';
		}

		let dataShowLinenum = null;
		if ('on' === isLineShow) {
			dataShowLinenum = '1';
		} else if ('undefined' === isLineShow && 'on' === hcbShowLinenum) {
			dataShowLinenum = '1';
		}

		const blockProps = useBlockProps({
			className: blockClass,
			'data-file': fileName || null,
			'data-lang': langName || null,
			'data-show-lang': dataShowLang,
			'data-show-linenum': dataShowLinenum,
			// 'data-line': dataLineNum || null,
		});

		return (
			<>
				<HcbSidebar {...{ attributes, setAttributes }} />
				<div {...blockProps}>
					<div className='hcb_codewrap'>
						<div className='hcb_linenum'></div>
						<textarea
							className='hcb_textarea'
							placeholder='Your Code...'
							value={code}
							onChange={(e) => {
								setAttributes({ code: e.target.value });
								setHeightCodeBlocks(e.target);
							}}
						></textarea>
					</div>
					<div className='hcb_controls'>
						<SelectControl
							value={attributes.langType}
							options={langList}
							onChange={(langKey) => {
								if ('' === langKey) {
									setAttributes({
										langType: '',
										langName: '',
									});
								} else {
									setAttributes({
										langType: langKey,
										langName: hcbLangs[langKey],
									});
								}
							}}
						/>
						<input
							type='text'
							className='filename_input'
							value={fileName}
							placeholder={__('file name', 'loos-hcb')} //ファイル名
							onChange={(e) => {
								setAttributes({ fileName: e.target.value });
							}}
						/>
						<input
							type='text'
							className='num_input'
							value={dataLineNum}
							placeholder={__('"data-line" value', 'loos-hcb')} //data-line属性値
							onChange={(e) => {
								setAttributes({ dataLineNum: e.target.value });
							}}
						/>
					</div>
				</div>
			</>
		);
	},
	save: ({ attributes }) => {
		const { code, fileName, langName, dataLineNum, isLineShow, isShowLang } = attributes;
		const langType = attributes.langType || 'plain';
		const preClass = classnames('prism', `${isLineShow}-numbers`, `lang-${langType}`);

		const blockProps = useBlockProps.save({
			className: 'hcb_wrap',
		});
		return (
			<div {...blockProps}>
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
	deprecated,
});
