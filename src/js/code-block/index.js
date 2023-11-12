/**
 * @WordPress dependencies
 */
// import { __ } from '@wordpress/i18n';
import { registerBlockType, createBlock } from '@wordpress/blocks';

/**
 * @Inner dependencies
 */
import edit from './edit';
import save from './save';
import metadata from './block.json';
import hcbIcon from './icon';
import deprecated from './_deprecated';

/**
 * Register Highlighting Code Block
 */
registerBlockType(metadata.name, {
	icon: hcbIcon,
	// attributes: metadata.attributes,
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
	edit,
	save,
	deprecated,
});
