import { useBlockProps, InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

export default function Edit({ attributes, setAttributes, context }) {
	const { id } = attributes;

	// Parent passes menuId via context
	const { menuId } = context['gutenberg-mega-menu/menu'] || { menuId: 0 };

	// Fetch menu items for this menu
	const items = useSelect(
		(select) =>
			menuId
				? select('core').getEntityRecords('postType', 'nav_menu_item', {
						per_page: -1,
						menus: menuId,
				  })
				: [],
		[menuId]
	);

	return (
		<div {...useBlockProps()}>
			<InspectorControls>
				<PanelBody title="Menu Link Settings">
					<SelectControl
						label="Attach to Menu Item"
						value={id || 0}
						options={[
							{ label: 'Select a menu item…', value: 0 },
							...(items || []).map((item) => ({
								label: item.title?.rendered || `(no title)`,
								value: item.id,
							})),
						]}
						onChange={(value) => setAttributes({ id: parseInt(value, 10) })}
					/>
				</PanelBody>
			</InspectorControls>

			<p>
				Attach content to:{' '}
				<strong>
					{items?.find((item) => item.id === id)?.title?.rendered || 'Not set'}
				</strong>
			</p>

			<InnerBlocks
				allowedBlocks={[
					'core/group',
					'core/columns',
					'core/column',
					'core/separator',
					'core/image'
				]}
			/>
		</div>
	);
}