/**
 * コードブロックの高さを設定する関数
 * Function for setting code block height
 */
export function setHeightCodeBlocks(block, elem) {
	const num = elem.value.match(/\r\n|\n/g);
	// let line;
	if (null !== num) {
		// line = num.length + 1;
		elem.style.setProperty('--hcb--code-linenum', num.length + 1);
	}
	//  else {
	// 	line = 1;
	// }

	// let height = line * 1.5; // 1.5 = line-height
	// height += 2 * 2; // py

	// offsetXXX: padding + border + scrollbar
	// scrollXXX: padding. スクロール可能なとき、見えていない部分のサイズも含む。
	// clientXXX: padding. スクロール可能なとき、見えている部分のサイズ。
	const isScrollableX = elem.scrollWidth > elem.offsetWidth;
	if (isScrollableX) {
		const scbarH = elem.offsetHeight - elem.clientHeight;
		elem.style.setProperty('--hcb--scbarH', scbarH + 'px');
		// elem.style.height = `calc(${height}em + ${scbarH}px)`;
	} else {
		// elem.style.height = height + 'em';
	}
}

/**
 * サニタイズ用の関数
 * Sanitize function
 */
export function sanitizeCodeblock(str) {
	if (str) {
		return str
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');
	}
	return str;
}
