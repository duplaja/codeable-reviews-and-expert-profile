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
function codeable_hire_button_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'codeable-hire-button/index.js';
	wp_register_script(
		'codeable-hire-button-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-editor',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'codeable-hire-button/editor.css';
	wp_register_style(
		'codeable-hire-button-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'codeable-hire-button/style.css';
	wp_register_style(
		'codeable-hire-button-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'codeable-reviews-and-expert-profile/codeable-hire-button', array(
		'editor_script' => 'codeable-hire-button-block-editor',
		'editor_style'  => 'codeable-hire-button-block-editor',
		'style'         => 'codeable-hire-button-block',
		'render_callback'=>	'codeable_display_expert_hire',
	) );
}
add_action( 'init', 'codeable_hire_button_block_init' );
