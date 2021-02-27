/* eslint no-var: 0 */

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
	var globalHcbLangs = window.hcbLangs; // eslint-disable-line
	var tinymce = window.tinymce; // eslint-disable-line
	if (typeof globalHcbLangs !== 'object') {
		globalHcbLangs = {
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

	const varluesArr = [{ text: 'Plain Text', value: 'plain' }];

	Object.keys(globalHcbLangs).forEach(function (key) {
		varluesArr.push({ text: globalHcbLangs[key], value: key });
	});

	tinymce.create('tinymce.plugins.hcb_external_script', {
		init(editor, url) {
			editor.addButton('hcb_select', {
				title: 'Highlighting Code Block',
				text: 'Code Block',
				type: 'listbox',
				values: varluesArr,
				onselect(e) {
					// var thisElem = e.target; //this;
					var thisV = e.target.value();
					if (thisV === '') return;

					var thisT = e.target.text();
					var selectedText = editor.selection.getContent({
						format: 'text',
					});
					var returnText = '';
					if (selectedText) {
						returnText = sanitizeCodeblock(selectedText);
					} else {
						returnText = '/* Your code... */';
					}
					returnText =
						'<div class="hcb_wrap"><pre class="prism undefined-numbers lang-' +
						thisV +
						'" data-lang="' +
						thisT +
						'"><code>' +
						returnText +
						'</code></pre></div>';
					editor.execCommand('mceInsertContent', false, returnText);
				},
			});
		},
		createControl(n, cm) {
			return null;
		},
	});

	tinymce.PluginManager.add('hcb_external_script', tinymce.plugins.hcb_external_script);
})();
