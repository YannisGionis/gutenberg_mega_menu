import { InnerBlocks } from '@wordpress/block-editor';

export default function save() {
    // Return only InnerBlocks content
    // The actual menu rendering happens in PHP render_callback
    return <InnerBlocks.Content />;
}