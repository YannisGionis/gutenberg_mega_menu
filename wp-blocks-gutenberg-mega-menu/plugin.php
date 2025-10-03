<?php
/**
 * Plugin Name:       Gutenberg Mega Menu
 * Description:       Gutenberg Mega Menu (desktop only).
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Yannis Gkionis
 * License:           GPL-2.0-or-later
 * Text Domain:       gutenberg-mega-menu
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function gmm_register_blocks() {
	register_block_type( __DIR__, array(
		'render_callback' => 'gmm_render_block',
	) );
	register_block_type( __DIR__ . '/src/menu-link' );
}
add_action( 'init', 'gmm_register_blocks' );

/**
 * Render desktop-only mega menu
 */
function gmm_render_block( $attributes, $content, $block ) {
	$menu_id = isset( $attributes['menuId'] ) ? (int) $attributes['menuId'] : 0;
	if ( ! $menu_id ) return '<p class="gmm-placeholder">Select a menu.</p>';

	$items = wp_get_nav_menu_items( $menu_id );
	if ( ! $items ) return '<p class="gmm-placeholder">Menu not found.</p>';

	// Index by parent
	$children_map = [];
	foreach ( $items as $item ) {
		$children_map[ (int) $item->menu_item_parent ][] = $item;
	}

	// Recursive desktop renderer
	$build_desktop = function( $parent = 0 ) use ( &$build_desktop, $children_map, $block ) {
		if ( empty( $children_map[ $parent ] ) ) return '';
		$html = '<ul class="gmm-desktop-list">';
		foreach ( $children_map[ $parent ] as $item ) {
			$html .= '<li class="gmm-item">';
			$html .= '<a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a>';

			// Nested submenu
			$html .= $build_desktop( $item->ID );

			// InnerBlocks dropdown content (columns, groups, images, etc.)
			foreach ( $block->inner_blocks as $child ) {
				if ( $child->block_type->name === 'gutenberg-mega-menu/menu-link'
					&& (int) $child->attributes['id'] === (int) $item->ID ) {
					$inner = render_block( $child );
					if ( trim( $inner ) ) {
						$html .= '<div class="gmm-dropdown">' . $inner . '</div>';
					}
				}
			}

			$html .= '</li>';
		}
		$html .= '</ul>';
		return $html;
	};

	return '<nav class="gutenberg-mega-menu">' . $build_desktop(0) . '</nav>';
}


/**
 * Ensure menus are available even if theme is block theme.
 */
function gmm_ensure_menus() {
    // Check if menus are supported
    if ( ! current_theme_supports( 'menus' ) ) {
        add_theme_support( 'menus' );
    }

    // Register a location for the mega menu
    register_nav_menus( array(
        'gmm-menu' => __( 'Gutenberg Mega Menu', 'gutenberg-mega-menu' ),
    ) );
}
add_action( 'after_setup_theme', 'gmm_ensure_menus' );
