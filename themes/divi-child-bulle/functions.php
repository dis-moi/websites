<?php

/*
 * Enqueue parent and child styles
 */
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'bulle-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'parent-style' ),
		wp_get_theme()->get('Version')
	);

	wp_enqueue_script(
		'bulle-child-cript',
		get_stylesheet_directory_uri() . '/dist/main.js',
		[],
		wp_get_theme()->get('Version'),
		true
	);

}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


