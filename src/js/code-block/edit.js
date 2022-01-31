/**
 * @WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { SelectControl } from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';

/**
 * @Inner dependencies
 */
import HcbSidebar from './_sidebar';
import { setHeightCodeBlocks } from './_utils';

/**
 * @Other dependencies
 */
import classnames from 'classnames';

/**
 * 言語情報をグローバル変数から受け取る。
 */
let hcbLangs = window.hcbLangs;
if ( 'object' !== typeof hcbLangs ) {
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

Object.keys( hcbLangs ).forEach( ( key ) => {
	langList.push( {
		value: key,
		label: hcbLangs[ key ],
	} );
} );

// edit
export default ( { attributes, setAttributes, clientId } ) => {
	const ref = useRef();

	const {
		code,
		// langType,
		fileName,
		langName,
		dataLineNum,
		isShowLang,
		isLineShow,
	} = attributes;

	const blockClass = classnames( 'hcb_wrap', 'hcb-block' );

	// クラスの重複バグの修正
	useEffect( () => {
		const nowClass = attributes.className;
		if ( ! nowClass ) return;

		const nowClasses = nowClass.split( ' ' );
		const newClasses = [ ...new Set( nowClasses ) ]; // 重複削除
		setAttributes( { className: classnames( newClasses ) } );
	}, [] );

	// コードの textarea 高さセット
	useEffect( () => {
		const { ownerDocument } = ref.current;

		if ( ownerDocument ) {
			const hcbTextarea = ownerDocument.querySelector( `#block-${ clientId } .hcb_textarea` );
			setHeightCodeBlocks( hcbTextarea );
		}
	}, [ clientId, code ] );

	// preタグにつけるクラス名を生成して保存
	// let preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;
	// setAttributes({ preClass: preClass });

	const hcbShowLang = window.hcbVars?.showLang;
	const hcbShowLinenum = window.hcbVars?.showLinenum;

	let dataShowLang = '0';
	if ( '1' === isShowLang ) {
		dataShowLang = '1';
	} else if ( '' === isShowLang && 'on' === hcbShowLang ) {
		dataShowLang = '1';
	}

	let dataShowLinenum = null;
	if ( 'on' === isLineShow ) {
		dataShowLinenum = '1';
	} else if ( 'undefined' === isLineShow && 'on' === hcbShowLinenum ) {
		dataShowLinenum = '1';
	}

	const blockProps = useBlockProps( {
		ref,
		className: blockClass,
		'data-file': fileName || null,
		'data-lang': langName || null,
		'data-show-lang': dataShowLang,
		'data-show-linenum': dataShowLinenum,
		// 'data-line': dataLineNum || null,
	} );

	return (
		<>
			<HcbSidebar { ...{ attributes, setAttributes } } />
			<div { ...blockProps }>
				<div className='hcb_codewrap'>
					<div className='hcb_linenum'></div>
					<textarea
						className='hcb_textarea'
						placeholder='Your Code...'
						value={ code }
						onChange={ ( e ) => {
							setAttributes( { code: e.target.value } );
							setHeightCodeBlocks( e.target );
						} }
					></textarea>
				</div>
				<div className='hcb_controls'>
					<SelectControl
						value={ attributes.langType }
						options={ langList }
						onChange={ ( langKey ) => {
							if ( '' === langKey ) {
								setAttributes( {
									langType: '',
									langName: '',
								} );
							} else {
								setAttributes( {
									langType: langKey,
									langName: hcbLangs[ langKey ],
								} );
							}
						} }
					/>
					<input
						type='text'
						className='filename_input'
						value={ fileName }
						placeholder={ __( 'file name', 'loos-hcb' ) } //ファイル名
						onChange={ ( e ) => {
							setAttributes( { fileName: e.target.value } );
						} }
					/>
					<input
						type='text'
						className='num_input'
						value={ dataLineNum }
						placeholder={ __( '"data-line" value', 'loos-hcb' ) } //data-line属性値
						onChange={ ( e ) => {
							setAttributes( { dataLineNum: e.target.value } );
						} }
					/>
				</div>
			</div>
		</>
	);
};
