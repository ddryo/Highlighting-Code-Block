document.addEventListener('DOMContentLoaded', function () {
	// if (!window.Prism) return;

	// Prism 実行
	// window.Prism.highlightAll();

	// Token customize
	const keyToken = document.querySelectorAll('.token.keyword');
	for (let i = 0; i < keyToken.length; i++) {
		const elem = keyToken[i];

		const defArr = ['function', 'def', 'class'];
		if (-1 !== defArr.indexOf(elem.textContent)) {
			elem.classList.add('def');
		} else if ('this' === elem.textContent) {
			elem.classList.add('this');
		}
	}

	// Line highlighter position.
	const lineHighlighter = document.querySelectorAll('.line-highlight');
	for (let i = 0; i < lineHighlighter.length; i++) {
		const elem = lineHighlighter[i];
		if (!elem.parentNode.classList.contains('line-numbers')) {
			const dataStart = elem.getAttribute('data-start');

			const topPosEm = (dataStart - 1) * 1.5; //line-heightの値

			// Line highlighterの位置がずれるので桁数に応じて微調節 -> ずれなくなった？
			// if (5 < dataStart) {
			//     topPosEm = topPosEm - dataStart * 0.02;
			// }
			// if (10 < dataStart) {
			//     topPosEm = topPosEm - dataStart * 0.01;
			// }
			// if (20 < dataStart) {
			//     topPosEm = topPosEm - dataStart * 0.001;
			// }

			elem.style.top = topPosEm + 'em';
		}
	}

	// clipboard
	(function () {
		if ('on' !== window.hcbVars.showCopy || !window.ClipboardJS) return;

		let clipCt = 1;

		// hcb_wrap
		const hcbWraps = document.querySelectorAll('.hcb_wrap');
		// const hcbClips = document.querySelectorAll('.hcb-clipboard');
		for (let i = 0; i < hcbWraps.length; i++) {
			const elem = hcbWraps[i];
			const code = elem.querySelector('code');
			if (null === code) continue;

			// ボタン生成
			const button = document.createElement('button');
			button.classList.add('hcb-clipboard');
			button.setAttribute('data-clipboard-target', '[data-hcb-clip="' + clipCt + '"]');
			button.setAttribute('data-clipboard-action', 'copy');
			// button.innerHTML = `コピー`;
			elem.prepend(button);

			// codeタグにターゲット属性追加
			code.setAttribute('data-hcb-clip', clipCt);

			clipCt++;
		}
		const clipboard = new ClipboardJS('.hcb-clipboard');
		clipboard.on('success', function (e) {
			const btn = e.trigger;
			btn.classList.add('-done');
			setTimeout(() => {
				btn.classList.remove('-done');
			}, 5000);
		});
		// clipboard.on('error', function (e) {
		// 	alert(e);
		// });
	})();
});
