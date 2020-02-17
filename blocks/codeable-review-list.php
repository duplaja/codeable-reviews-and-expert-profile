<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package codeable-reviews-and-expert-profile
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function codeable_review_list_block_init() {
	$dir = dirname( __FILE__ );

	$block_js = 'codeable-review-list/block.js';
	wp_register_script(
		'codeable-review-list-block-editor',
		plugins_url( $block_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-editor',
		),
		filemtime( "$dir/$block_js" )
	);

	$editor_css = 'codeable-review-list/editor.css';
	wp_register_style(
		'codeable-review-list-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'codeable-review-list/style.css';
	wp_register_style(
		'codeable-review-list-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'codeable-reviews-and-expert-profile/codeable-review-list', array(
		'editor_script' => 'codeable-review-list-block-editor',
		'editor_style'  => 'codeable-review-list-block-editor',
		'style'         => 'codeable-review-list-block',
		'render_callback' => 'codeable_display_reviews',
	) );
}
add_action( 'init', 'codeable_review_list_block_init' );
