/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, BlockControls } from '@wordpress/block-editor';
import {
	SelectControl,
	TextControl,
	// RadioControl,
	PanelBody,
	ToolbarGroup,
} from '@wordpress/components';
import { useState, useEffect, useRef } from '@wordpress/element';

/**
 * @Inner dependencies
 */
// import { setHeightCodeBlocks } from './_utils';

/**
 * @Other dependencies
 */
import classnames from 'classnames';

/**
 * 設定情報をグローバル変数から受け取る。
 */
const hcbShowLang = window.hcbVars?.showLang;
const hcbShowLinenum = window.hcbVars?.showLinenum;

let HCB_LANGS = window?.hcbLangs || null;
if (null) {
	HCB_LANGS = {
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

// HCB_LANGS を配列化する
// const HCB_LANG_ARRAY = Object.entries(HCB_LANGS);
const HCB_LANG_ARRAY = [['plain', 'Plain'], ...Object.entries(HCB_LANGS)];

const LANG_SELECT_OPTIONS = [
	{
		value: '',
		label: 'Plain',
	},
	...Object.entries(HCB_LANGS).map(([key, value]) => ({
		value: key,
		label: value,
	})),
];

const getLineNum = (code = '') => {
	const lineNums = code.match(/\r\n|\n/g);
	if (null !== lineNums) {
		return lineNums.length + 1;
	}
	return 1;
};

// edit
export default ({ attributes, setAttributes, clientId }) => {
	const ref = useRef();

	const {
		className,
		code = '',
		langType,
		fileName,
		langName,
		dataLineNum,
		dataStart,
		isShowLang,
		isLineShow,
	} = attributes;
	// コード行数
	const [codeLineNum, setCodeLineNums] = useState(getLineNum(code));

	const blockClass = classnames('hcb-block', 'hcb_wrap');

	// クラスの重複バグの修正
	useEffect(() => {
		const nowClass = className || '';
		if (!nowClass) return;

		const nowClasses = nowClass.split(' ');
		const newClasses = [...new Set(nowClasses)]; // 重複削除
		setAttributes({ className: classnames(newClasses) });
	}, [className, setAttributes]);

	// コードの textarea 高さセット
	useEffect(() => {
		const { ownerDocument } = ref.current;

		if (ownerDocument) {
			const hcbBlock = ownerDocument.querySelector(`#block-${clientId}`);
			const hcbTextarea = hcbBlock.querySelector(`.hcb_textarea`);
			hcbBlock.style.setProperty('--hcb--code-linenum', codeLineNum);

			// offsetXXX: padding + border + scrollbar
			// scrollXXX: padding. スクロール可能なとき、見えていない部分のサイズも含む。
			// clientXXX: padding. スクロール可能なとき、見えている部分のサイズ。
			const isScrollableX = hcbTextarea.scrollWidth > hcbTextarea.offsetWidth;
			if (isScrollableX) {
				const scbarH = hcbTextarea.offsetHeight - hcbTextarea.clientHeight;
				hcbBlock.style.setProperty('--hcb--scbarH', scbarH + 'px');
			}
		}
	}, [clientId, codeLineNum]);

	// preタグにつけるクラス名を生成して保存
	// let preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;
	// setAttributes({ preClass: preClass });

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
		ref,
		className: blockClass,
		'data-file': fileName || null,
		'data-lang': langName || null,
		'data-show-lang': dataShowLang,
		'data-show-linenum': dataShowLinenum,
		// 'data-line': dataLineNum || null, // edit側にはつけない
	});

	// 新規配列を作成
	const langArray = [...HCB_LANG_ARRAY];

	// lang未設定の時に 'Plain' は表示しない
	if (!langType) {
		langArray.shift();
	}

	return (
		<>
			<BlockControls>
				<ToolbarGroup
					isCollapsed={true}
					icon={<>{langName || __('Language', 'loos-hcb')}</>}
					title='Lnaguage'
					controls={langArray.map(([_langKey, _langName]) => {
						return {
							key: _langKey,
							isActive: _langKey === langType,
							title: _langName,
							// icon: null,
							onClick: () => {
								if (_langKey === 'plain') {
									setAttributes({ langType: '', langName: '' });
								} else {
									setAttributes({ langType: _langKey, langName: _langName });
								}
							},
						};
					})}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={__('HCB settings', 'loos-hcb')} initialOpen={true}>
					<SelectControl
						label={__('Language', 'loos-hcb')}
						value={attributes.langType}
						options={LANG_SELECT_OPTIONS}
						onChange={(langKey) => {
							if ('' === langKey) {
								setAttributes({ langType: '', langName: '' });
							} else {
								setAttributes({ langType: langKey, langName: HCB_LANGS[langKey] });
							}
						}}
					/>
					<SelectControl
						label={__('Display line numbers', 'loos-hcb')} // 行数の表示
						value={isLineShow}
						options={[
							{
								label: __('Do not set individually', 'loos-hcb'), //個別で設定はしない
								value: 'undefined',
							},
							{
								label: __('Display row count', 'loos-hcb'), //行数を表示する
								value: 'on',
							},
							{
								label: __('Do not display row count', 'loos-hcb'), //行数を表示しない
								value: 'off',
							},
						]}
						onChange={(val) => {
							setAttributes({ isLineShow: val });
						}}
					/>
					<SelectControl
						label={__('Display language name', 'loos-hcb')} // 言語名の表示に関する設定
						value={isShowLang}
						options={[
							{
								label: __('Do not set individually', 'loos-hcb'), //個別で設定はしない
								value: '',
							},
							{
								label: __('Display language', 'loos-hcb'), //言語を表示する
								value: '1',
							},
							{
								label: __('Do not display language', 'loos-hcb'), //言語を表示しない
								value: '0',
							},
						]}
						onChange={(val) => {
							setAttributes({ isShowLang: val });
						}}
					/>
					<TextControl
						label={__('File name', 'loos-hcb')} // ファイル名
						value={fileName}
						onChange={(val) => {
							setAttributes({ fileName: val });
						}}
					/>
					<TextControl
						// ハイライトする行番号
						label={
							<>
								{__('Highlight Number', 'loos-hcb') + ' ( '}
								<code className='hcb-code-in-label'>[data-line]</code>
								{' )'}
							</>
						}
						value={dataLineNum}
						onChange={(val) => {
							setAttributes({ dataLineNum: val });
						}}
					/>
					<TextControl
						type='number'
						// 開始行番号
						label={
							<>
								{__('First line number', 'loos-hcb') + ' ( '}
								<code className='hcb-code-in-label'>[data-start]</code>
								{' )'}
							</>
						}
						value={dataStart || 1}
						onChange={(val) => {
							setAttributes({ dataStart: parseInt(val || 1) });
						}}
						style={{ opacity: dataShowLinenum ? 1 : 0.5 }}
						disabled={dataShowLinenum ? false : true}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<div className='hcb_codewrap'>
					{dataShowLinenum && (
						<div className='hcb_linenum'>
							<div className='hcb-startNum'>{dataStart}</div>
						</div>
					)}
					{/* <TextareaControl */}
					<textarea
						className='hcb_textarea'
						placeholder='Your Code...'
						value={code}
						onChange={(e) => {
							const val = e.target.value;
							setAttributes({ code: val });
							// setHeightCodeBlocks(e.target);
							const newCodeLineNum = getLineNum(val);
							if (codeLineNum !== newCodeLineNum) {
								setCodeLineNums(newCodeLineNum);
							}
						}}
					></textarea>
					{dataLineNum && (
						<div className='hcb-datapreview'>
							<div className='hcb-datapreview__items'>{`{${dataLineNum}}`}</div>
						</div>
					)}
				</div>
			</div>
		</>
	);
};
