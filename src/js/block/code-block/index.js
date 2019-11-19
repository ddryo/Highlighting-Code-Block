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
        preClass: {
            type: 'string',
        },
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
        const blockClass = classnames('hcb_wrap', className);

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
        let preClass = 'prism ' + attributes.isLineShow + '-numbers lang-' + attributes.langType;
        setAttributes({ preClass: preClass });

        return (
            <Fragment>
                <InspectorControls>
                    <HcbSidePanels {...props} />
                </InspectorControls>
                <div className={blockClass}>
                    <textarea
                        className='hcb_textarea'
                        placeholder='Your Code...'
                        value={attributes.code}
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
                            value={attributes.dataLineNum}
                            placeholder='data-line属性の値'
                            onChange={(e) => {
                                setAttributes({ dataLineNum: e.target.value });
                            }}
                        />
                        <input
                            type='text'
                            className='filename_input'
                            value={attributes.fileName}
                            placeholder='ファイル名'
                            onChange={(e) => {
                                setAttributes({ fileName: e.target.value });
                            }}
                        />
                    </div>
                </div>
            </Fragment>
        );
    },
    save: (props) => {
        const { attributes } = props;
        let preData = {
            className: attributes.preClass,
        };

        //ファイル名の指定があるかどうか。
        if ('' !== attributes.fileName) {
            preData['data-file'] = attributes.fileName;
        }
        //言語の指定があるかどうか。
        if ('' !== attributes.langName) {
            preData['data-lang'] = attributes.langName;
        }
        //ハイライト行の指定があるかどうか
        if ('' !== attributes.dataLineNum) {
            preData['data-line'] = attributes.dataLineNum;
        }

        return (
            <div className='hcb_wrap'>
                <pre {...preData}>
                    <RichText.Content tagName='code' value={sanitizeCodeblock(attributes.code)} />
                </pre>
            </div>
        );
    },
});
