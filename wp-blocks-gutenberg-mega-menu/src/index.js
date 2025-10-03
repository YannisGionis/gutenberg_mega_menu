import { registerBlockType, createBlock } from '@wordpress/blocks';
import './team-member';
import './style.scss';
import Edit from './edit';
import save from './save';

// Register the parent "Gutenberg Mega Menu" block
registerBlockType('gutenberg-mega-menu/block', {
    edit: Edit,
    save,
});