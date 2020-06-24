<?php

/*
 * Enqueue parent and child styles
 */
function divi_child_bulle_enqueue_styles() {

    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    $relpath = 'style.css';
    $uri = get_theme_file_uri($relpath);
    $vsn = filemtime(get_theme_file_path($relpath));
    wp_enqueue_style(
        'divi-style',
        $uri,
        array( 'parent-style' ),
        $vsn
    );


    wp_register_script(
        'bulle-child-script',
        get_stylesheet_directory_uri() . '/dist/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );

    $page_nonsupporte = get_theme_mod( 'bulle_setting_non_supporte' );
    $url_nonsupporte = (isset($page_nonsupporte) && !empty($page_nonsupporte)) ? get_permalink($page_nonsupporte) : '';

    $page_opera = get_theme_mod( 'bulle_setting_lien_opera' );
    $url_opera = (isset($page_opera) && !empty($page_opera)) ? get_permalink($page_opera) : '';

    $page_edge = get_theme_mod( 'bulle_setting_lien_edge' );
    $url_edge = (isset($page_edge) && !empty($page_edge)) ? get_permalink($page_edge) : '';

    // $page_installe = get_theme_mod( 'bulle_setting_deja_installe' );
    // $url_installe = (isset($page_installe) && !empty($page_installe)) ? get_permalink($page_installe) : '';

    $page_nonsupporte_mobile = get_theme_mod( 'bulle_setting_non_supporte_mobile' );
    $url_nonsupporte_mobile = (isset($page_nonsupporte_mobile) && !empty($page_nonsupporte_mobile)) ? get_permalink($page_nonsupporte_mobile) : '';

    $data = [
        'bulle_non_supporte_mobile' => $url_nonsupporte_mobile,
        'bulle_non_supporte' => $url_nonsupporte,
        // 'bulle_deja_installe' => $url_installe,
        'bulle_lien_opera' => $url_opera,
        'bulle_lien_edge' => $url_edge,
        'bulle_extension_id_chrome' => get_theme_mod( 'bulle_setting_extension_id_chrome', 'fpjlnlnbacohacebkadbbjebbipcknbg' ),
        'bulle_lien_extension_chrome' => get_theme_mod( 'bulle_setting_extension_chrome', 'https://chrome.google.com/webstore/detail/dismoi/fpjlnlnbacohacebkadbbjebbipcknbg?hl=fr' ),
        'bulle_lien_extension_firefox' => get_theme_mod( 'bulle_setting_extension_firefox', 'https://addons.mozilla.org/en-US/firefox/addon/dismoi/' ),
        'bulle_lien_extension_chrome_mobile' => get_theme_mod( 'bulle_setting_extension_chrome_mobile', 'https://chrome.google.com/webstore/detail/dismoi/fpjlnlnbacohacebkadbbjebbipcknbg?hl=fr' ),
        'bulle_lien_extension_firefox_mobile' => get_theme_mod( 'bulle_setting_extension_firefox_mobile', 'https://addons.mozilla.org/en-US/firefox/addon/dismoi/' ),

    ];
    wp_localize_script( 'bulle-child-script', 'bull_config', $data );
    wp_enqueue_script( 'bulle-child-script' );

    if ( is_page_template( 'page-profile-app.php' ) ) {
        $relpath = '/profile-app/js/profiles.bundle.js';
        $vsn = filemtime( get_theme_file_path( $relpath ) );
        wp_register_script(
            'dismoi-profiler-app',
            get_stylesheet_directory_uri() . $relpath,
            [],
            $vsn,
            true
        );
        wp_enqueue_script( 'dismoi-profiler-app' );
   }

}
add_action( 'wp_enqueue_scripts', 'divi_child_bulle_enqueue_styles' );


/*
 * Remove dashicons sometimes
 */
function divi_child_bulle_remove_dash () {
    // remove dashicons for performance
    if (current_user_can( 'update_core' )) {
        return;
    }
    wp_deregister_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'divi_child_bulle_remove_dash' );


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

/*
 * Absolutely not cache compatible
 * ToDo: To be handled on the FE only
 */
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
 * Add Bulles settings to the customizer.
 *
 * @since Theme 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function prefix_customize_register( $wp_customize ) {

    $wp_customize->add_section( 'bulle_section' , array(
        'title'      => __( 'Configuration Bulles/Dismoi', 'divi-child-bulle' ),
        'priority'   => 30,
    ) );


    // non supporté
    $wp_customize->add_setting( 'bulle_setting_non_supporte',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_non_supporte',
            array(
                'label'          => __( 'Desktop Page Browser Non Supporté (Ni Chrome, Firefox, Edge, où Opéra)', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_non_supporte',
                'type'           => 'dropdown-pages'
            )
        )
    );

    // lien opera
    $wp_customize->add_setting( 'bulle_setting_lien_opera',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_lien_opera',
            array(
                'label'          => __( 'Page Opera', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_lien_opera',
                'type'           => 'dropdown-pages'
            )
        )
    );

    // lien edge
    $wp_customize->add_setting( 'bulle_setting_lien_edge',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_setting_lien_edge',
            array(
                'label'          => __( 'Page Edge', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_lien_edge',
                'type'           => 'dropdown-pages'
            )
        )
    );

    // extension chrome
    $wp_customize->add_setting( 'bulle_setting_extension_chrome',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => 'https://chrome.google.com/webstore/detail/le-m%C3%AAme-en-mieux/fpjlnlnbacohacebkadbbjebbipcknbg?hl=fr'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_extension_chrome',
            array(
                'label'          => __( 'Lien Extension Chrome', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_extension_chrome'
            )
        )
    );

    // extension FF
    $wp_customize->add_setting( 'bulle_setting_extension_firefox',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => 'https://addons.mozilla.org/fr/firefox/addon/lmem/'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_extension_firefox',
            array(
                'label'          => __( 'Lien Extension Firefox', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_extension_firefox'
            )
        )
    );

    // EXTENSION_ID CHROME
    $wp_customize->add_setting( 'bulle_setting_extension_id_chrome',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => 'cifabmmlclhhhlhhabmbhhfocdgglljb'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_extension_id_chrome',
            array(
                'label'          => __( 'Identifiant Extension Chrome (pour détection JS)', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_extension_id_chrome'
            )
        )
    );


    // deja installé
    /*
    $wp_customize->add_setting( 'bulle_setting_deja_installe',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_deja_installe',
            array(
                'label'          => __( 'Page Déjà Installé', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_deja_installe',
                'type'           => 'dropdown-pages'
            )
        )
    );
    */

    $wp_customize->add_setting( 'bulle_setting_extension_chrome_mobile',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => 'https://chrome.google.com/webstore/detail/le-m%C3%AAme-en-mieux/fpjlnlnbacohacebkadbbjebbipcknbg?hl=fr'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_extension_chrome_mobile',
            array(
                'label'          => __( 'Lien Extension Chrome Mobile', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_extension_chrome_mobile'
            )
        )
    );

    $wp_customize->add_setting( 'bulle_setting_extension_firefox_mobile',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => 'https://addons.mozilla.org/fr/firefox/addon/lmem/'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_extension_firefox_mobile',
            array(
                'label'          => __( 'Lien Extension Firefox Mobile', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_extension_firefox_mobile'
            )
        )
    );

    $wp_customize->add_setting( 'bulle_setting_non_supporte_mobile',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_non_supporte_mobile',
            array(
                'label'          => __( 'Mobile Page Browser Non Supporté (Ni Chrome, Firefox)', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_non_supporte_mobile',
                'type'           => 'dropdown-pages'
            )
        )
    );


}
add_action( 'customize_register', 'prefix_customize_register' );

/**
 * Output Matomo tag manager tag
 *
 */
function hook_matomo_tag() {
    ?>
    <!-- Matomo Tag Manager -->
    <script type="text/javascript">
		var _mtm = _mtm || [];
		_mtm.push({'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start'});
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.type='text/javascript'; g.async=true; g.defer=true; g.src='https://stats.lmem.net/js/container_5XvDMUox.js'; s.parentNode.insertBefore(g,s);
    </script>
    <!-- End Matomo Tag Manager -->
    <?php
}
add_action('wp_head', 'hook_matomo_tag');

/**
 *	This will hide the Divi "Project" post type.
 *	Thanks to georgiee (https://gist.github.com/EngageWP/062edef103469b1177bc#gistcomment-1801080) for his improved solution.
 */
add_filter( 'et_project_posttype_args', 'mytheme_et_project_posttype_args', 10, 1 );
function mytheme_et_project_posttype_args( $args ) {
    return array_merge( $args, array(
        'public'              => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'show_in_nav_menus'   => false,
        'show_ui'             => false
    ));
}

add_filter ('widget_text', 'do_shortcode');

function year_shortcode () {
    $year = date_i18n ('Y');
    return $year;
}
add_shortcode ('year', 'year_shortcode');


function profiles_rewrite() {
    // get first page with profile
    $args = array(
        'post_type' => 'page',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => '_wp_page_template',
                'value' => 'page-profile-app.php'
            )
        )
    );
    $the_pages = new WP_Query( $args );
    if ( $the_pages->posts && count( $the_pages->posts ) ) {
        $slug = $the_pages->posts[0]->post_name;
        $id = $the_pages->posts[0]->ID;

        // must refresh permalinks
        add_rewrite_rule(
            '^'.$slug.'/([0-9]+)/([^/]*)?',
            'index.php?pagename='.$slug,
            'top'
        );

    }
}
add_action('init', 'profiles_rewrite');
