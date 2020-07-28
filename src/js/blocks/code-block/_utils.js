/**
 * コードブロックの高さを設定する関数
 * Function for setting code block height
 */
export function setHeightCodeBlocks(elem) {
	const num = elem.value.match(/\r\n|\n/g);
	let line;
	if (null !== num) {
		line = num.length + 1;
	} else {
		line = 1;
	}
	elem.style.height = line * 1.8 + 1.6 + 2 + 'em';
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
