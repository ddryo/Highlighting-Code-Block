const defaultConfig = require('@wordpress/scripts/config/.eslintrc.js');

module.exports = {
    ...defaultConfig, //@wordpress/scriptを引き継ぐ
    // extends: [
    //     ...defaultConfig.extends,
    //     "plugin:prettier/recommended"
    // ],
    rules: {
        // 'prettier/prettier': 0,
        // quotes: ['warn', 'single'],
        'jsx-quotes': ['warn', 'prefer-single'], // JSXでもシングルクォートを使う
        'array-callback-return': 0, //mapでreturnなくても怒らない
        '@wordpress/i18n-text-domain': 0, //__()に変数使用しても怒らない
        'react-hooks/rules-of-hooks': 0, // edit: で useSelect 使えるように
        // jsdoc関連
        'require-jsdoc': 0, //Docコメントなくてもエラーにしない
        'valid-jsdoc': 0, //Docコメントの書き方についてとやかくいわせない
        'jsdoc/require-param': 0, //Docコメントなくてもエラーにしない
        'jsdoc/require-param-type': 0, //Docコメントの書き方についてとやかくいわせない
        "jsdoc/check-access": 0,
        'jsdoc/check-property-names': 0,
        'jsdoc/empty-tags': 0,
        'jsdoc/require-property': 0,
        'jsdoc/require-property-description': 0,
        'jsdoc/require-property-name': 0,
        'jsdoc/require-property-type': 0,
        'jsdoc/check-tag-names': 0,

    },
};
