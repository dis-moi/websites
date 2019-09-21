<?php

/*
 * Enqueue parent and child styles
 */
function my_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'bulle-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'parent-style' ),
		wp_get_theme()->get('Version')
	);

    wp_register_script(
        'bulle-child-script',
        get_stylesheet_directory_uri() . '/dist/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );

    $page = get_theme_mod( 'bulle_setting_non_supporte' );
    $url = (isset($page) && !empty($page)) ? get_permalink($page) : '';
    $data = [
        'bulle_non_supporte' => $url
    ];
    wp_localize_script( 'bulle-child-script', 'bull_config', $data );
    wp_enqueue_script( 'bulle-child-script' );

}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


/*
 * Extracting the browser based on the user agent
 *
 * @return string
 */
function get_browser_name($user_agent) {
	if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'opera';
	elseif (strpos($user_agent, 'Edge')) return 'edge';
	elseif (strpos($user_agent, 'Chrome')) return 'chrome';
	elseif (strpos($user_agent, 'Safari')) return 'safari';
	elseif (strpos($user_agent, 'Firefox')) return 'firefox';
	elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'ie';
	return 'Other';
}

function output_bulle_overlay() {
	if( get_browser_name($_SERVER['HTTP_USER_AGENT']) == 'firefox'):
		echo get_template_part( 'includes/overlayFirefox');
	elseif(get_browser_name($_SERVER['HTTP_USER_AGENT']) == 'chrome'):
		echo get_template_part( 'includes/overlayChrome');
	endif;

}
add_action( 'wp_footer', 'output_bulle_overlay' );


function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');



/**
 * Add page selector to the customizer.
 *
 * @since Theme 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function prefix_customize_register( $wp_customize ) {

    $wp_customize->add_section( 'bulle_section' , array(
        'title'      => __( 'Bulle', 'divi-child-bulle' ),
        'priority'   => 30,
    ) );


    $wp_customize->add_setting( 'bulle_setting_non_supporte',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_non_supporte',
            array(
                'label'          => __( 'Page Extension Non Supporté', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_non_supporte',
                'type'           => 'dropdown-pages'
            )
        )
    );


}
add_action( 'customize_register', 'prefix_customize_register' );


