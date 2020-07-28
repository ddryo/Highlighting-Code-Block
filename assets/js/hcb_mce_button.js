/**
 * Sanitize function
 */
function sanitizeCodeblock(str) {
	if (str) {
		return str
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}
	return str;
}

(function () {
	//グローバル変数の受け取り
	let globalHcbLangs = window.hcbLangArray;
	if (typeof globalHcbLangs !== 'object') {
		globalHcbLangs = {};
		/* eslint no-alert: 0 */
		alert(
			'エラー：「Highlighting Code Block」プラグインの「使用する言語設定」を見直してください。'
		);
	}

	const varluesArr = [{ text: 'Plane Text', value: 'plane' }];

	Object.keys(globalHcbLangs).forEach(function (key) {
		varluesArr.push({ text: globalHcbLangs[key], value: key });
	});

	tinymce.create('tinymce.plugins.hcb_external_script', {
		init(editor, url) {
			editor.addButton('hcb_select', {
				title: 'Highlighting Code Block',
				text: 'コードブロック',
				type: 'listbox',
				values: varluesArr,
				onselect(e) {
					const thisV = this.value();
					const thisT = this.text();
					if (thisV === '') return;
					const selected_text = editor.selection.getContent({
						format: 'text',
					});
					if (selected_text) {
						var return_text = sanitizeCodeblock(selected_text);
					} else {
						var return_text = '/* Your code... */';
					}
					return_text =
						'<div class="hcb_wrap"><pre class="prism undefined-numbers lang-' +
						thisV +
						'" data-lang="' +
						thisT +
						'"><code>' +
						return_text +
						'</code></pre></div>';
					editor.execCommand('mceInsertContent', false, return_text);
				},
			});
		},
		createControl(n, cm) {
			return null;
		},
	});

	tinymce.PluginManager.add(
		'hcb_external_script',
		tinymce.plugins.hcb_external_script
	);
})();
