/**
 * @WordPress dependencies
 */
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * @Inner dependencies
 */
import { sanitizeCodeblock } from './_utils';

/**
 * @Other dependencies
 */
import classnames from 'classnames';

// save
export default ({ attributes }) => {
	const { code, fileName, langName, dataLineNum, dataStart, isLineShow, isShowLang } = attributes;
	const langType = attributes.langType || 'plain';
	const preClass = classnames('prism', `${isLineShow}-numbers`, `lang-${langType}`);

	const blockProps = useBlockProps.save({
		className: 'hcb_wrap',
	});
	return (
		<div {...blockProps}>
			<pre
				className={preClass}
				data-file={fileName || null}
				data-lang={langName || null}
				data-line={dataLineNum || null}
				data-start={1 === dataStart ? null : dataStart}
				data-show-lang={isShowLang || null}
			>
				<RichText.Content tagName='code' value={sanitizeCodeblock(code)} />
			</pre>
		</div>
	);
};
