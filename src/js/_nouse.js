/**
 * Valid Blocks - 高度な設定を追加するブロックを指定
 */
function isValidBlockType(blockName) {
	const validBlocks = ['loos-hcb/code-block'];
	return -1 !== validBlocks.indexOf(blockName);
}

/**
 * Override props assigned to save component to inject our custom data.
 * This is only done if the component is a valid block type.
 *
 * @param {Object} extraProps Additional props applied to save element.
 * @param {Object} blockType  Block type.
 * @param {Object} attributes Current block attributes.
 *
 * @return {Object} Filtered props applied to save element.
 */
function addSaveProps(extraProps, blockType, attributes) {
	if (isValidBlockType(blockType.name)) {
		// 「wp-block-loos-hcb-code-block」をつけない
		extraProps.className = attributes.className;
	}
	return extraProps;
}
// addFilter("blocks.getSaveContent.extraProps", "loos-hcb/add-props", addSaveProps);

/**
 * ベータ版での設定値によるバリデーション回避
 *
 * @param {Object} attributes Current block attributes.
 * @param {Object} settings Current block settings.
 * @param {Object} content Current block content.
 */
function beforeValidation(attributes, settings, content) {
	//console.log(settings.name);
	if ('loos-hcb/code-block' === settings.name) {
		if ('undefined' === typeof attributes.preClass) {
			let isLineShow = attributes.isLineShow;
			if ('undefined' === typeof isLineShow) {
				isLineShow = 'line'; // "on" or "off"
			}
			const preClass =
				'prism ' + isLineShow + '-numbers lang-' + attributes.langType;
			attributes.preClass = preClass;
		}
	}
	return attributes;
}
// addFilter("blocks.getBlockAttributes", "loos-hcb/before-validation", beforeValidation);
