/**
 * Sanitize function
 */
function sanitizeCodeblock(str) {
    if (str) {
        return str.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    } else {
        return str;
    }
}

(function () {
    //グローバル変数の受け取り
    var global_hcb_langs = hcbLangArray;
    if (typeof global_hcb_langs != "object") {
        global_hcb_langs = {};
        alert('エラー：「Highlighting Code Block」プラグインの「使用する言語設定」を見直してください。');
    }

    var varluesArr = [
        { text: 'Plane Text', value: 'plane' }
    ];

    Object.keys(global_hcb_langs).forEach(function (key) {
        varluesArr.push({ text: global_hcb_langs[key], value: key } );
    });

    tinymce.create('tinymce.plugins.hcb_external_script', {
        init: function (editor, url) {
            editor.addButton('hcb_select', {
                title: 'Highlighting Code Block',
                text: 'コードブロック',
                type: 'listbox',
                values: varluesArr,
                onselect: function (e) {
                    var thisV = this.value();
                    var thisT = this.text();
                    if (thisV === '' ) return;
                    var selected_text = editor.selection.getContent({ format: 'text' });
                    if ( selected_text ) {
                        var return_text = sanitizeCodeblock( selected_text );
                    } else {
                        var return_text = '/* Your code... */';
                    }
                    return_text = '<div class="hcb_wrap"><pre class="prism undefined-numbers lang-' + thisV + '" data-lang="' + thisT +'"><code>' + return_text + '</code></pre></div>';
                    editor.execCommand('mceInsertContent', false, return_text);
                },
            });

        },
        createControl: function (n, cm) {
            return null;
        },
    });

    tinymce.PluginManager.add('hcb_external_script', tinymce.plugins.hcb_external_script);

})();