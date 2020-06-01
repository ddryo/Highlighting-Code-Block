import classnames from 'classnames';

/**
 * wp objects
 */
import { __, RichText, InspectorControls, registerBlockType, Fragment, createBlock } from '../@wp';

/**
 * アイコンS
 */
import { hcbIcon } from './_icon';

/**
 * 使用する関数たち
 */
import { setHeightCodeBlocks, sanitizeCodeblock } from './_utils';

/**
 * InspectorControls
 */
import HcbSidePanels from './_panels';

/**
 * テキストドメイン
 */
const textDomain = 'loos-hcb';

/**
 * 言語情報をグローバル変数から受け取る。object になっていなければアラート。
 * Receive language information from global variables. Alert if not object.
 */
let hcbLangs = hcbLangArray;
if ('object' != typeof hcbLangs) {
    hcbLangs = {};
    alert(
        'エラー：「Highlighting Code Block」プラグインの「使用する言語設定」を見直してください。'
    );
}

/**
 * Register Highlighting Code Block
 */
registerBlockType('loos-hcb/code-block', {
    title: 'Highlighing Code Block',
    icon: hcbIcon,
    category: 'formatting',
    keywords: ['hcb', 'code'],
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
            type: 'text',
        },
        isLineShow: {
            type: 'string',
            default: 'undefined', // -> undefinedはthe_contentにて書き換える
        },
        isShowLang: {
            type: 'string',
            default: '',
        },
        // preClass: {
        //     type: 'string',
        // },
    },
    supports: {
        className: false, //ブロック要素を作成した際に付く .wp-block-[ブロック名] で自動生成されるクラス名の設定。
    },
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
        const { attributes, setAttributes, clientId, className } = props;
        const { code, langType, fileName, langName, dataLineNum, isShowLang } = attributes;
        const blockClass = classnames('hcb_wrap', 'hcb-block', className);

        // コードの textarea 高さセット
        setTimeout(() => {
            // ちょっと遅らせないと null になる
            const hcbBlock = document.getElementById('block-' + clientId);
            const hcbTextarea = hcbBlock.querySelector('.hcb_textarea');
            setHeightCodeBlocks(hcbTextarea);
        }, 10);

        let optionsList = [<option value=''>- Lang Select -</option>];

        Object.keys(hcbLangs).forEach((key) => {
            optionsList.push(<option value={key}>{hcbLangs[key]}</option>);
        });

        //preタグにつけるクラス名を生成して保存
        // let preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;
        // setAttributes({ preClass: preClass });

        return (
            <Fragment>
                <InspectorControls>
                    <HcbSidePanels {...props} />
                </InspectorControls>
                <div 
                    className={blockClass}
                    data-file={fileName || null}
                    data-lang={langName || null}
                    // data-line={dataLineNum || null}
                    data-show-lang={isShowLang || null}
                >
                    <textarea
                        className='hcb_textarea'
                        placeholder='Your Code...'
                        value={code}
                        onChange={(e) => {
                            setAttributes({ code: e.target.value });
                            setHeightCodeBlocks(e.target);
                        }}
                    ></textarea>
                    <div className='select_wrap'>
                        <select
                            value={attributes.langType}
                            onChange={(e) => {
                                const selected = e.target.querySelector('option:checked');
                                let selectedLangName = selected.text;
                                if ('- Lang Select -' === selectedLangName) {
                                    selectedLangName = '';
                                }
                                setAttributes({ langType: selected.value });
                                setAttributes({ langName: selectedLangName });
                            }}
                        >
                            {optionsList}
                        </select>
                        <input
                            type='text'
                            className='num_input'
                            value={dataLineNum}
                            placeholder={__('"data-line" value', textDomain)} //data-line属性値
                            onChange={(e) => {
                                setAttributes({ dataLineNum: e.target.value });
                            }}
                        />
                        <input
                            type='text'
                            className='filename_input'
                            value={fileName}
                            placeholder={__('file name', textDomain)} //ファイル名
                            onChange={(e) => {
                                setAttributes({ fileName: e.target.value });
                            }}
                        />
                    </div>
                </div>
            </Fragment>
        );
    },
    save: ({attributes}) => {
        const {code, langType, fileName, langName, dataLineNum, isLineShow, isShowLang } = attributes;

        // preタグにつけるクラス
        let preClass = 'prism ' + isLineShow + '-numbers lang-' + langType;

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
});
