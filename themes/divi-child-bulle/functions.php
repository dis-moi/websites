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
        // bulle_setting_profiler_version
        $profiler_version = get_theme_mod( 'bulle_setting_profiler_version', '0.9' );
        if ( empty( $profiler_version ) ) {
            $profiler_version = false;
        }

        wp_register_script(
            'dismoi-profiler-app',
            'https://profiles.dismoi.io/js/profiles.bundle.js',
            [],
            $profiler_version,
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
function dismoi_output_bulle_overlay() {
	if( get_browser_name($_SERVER['HTTP_USER_AGENT']) == 'firefox'):
		echo get_template_part( 'includes/overlayFirefox');
	elseif(get_browser_name($_SERVER['HTTP_USER_AGENT']) == 'chrome'):
		echo get_template_part( 'includes/overlayChrome');
	endif;

}
add_action( 'wp_footer', 'dismoi_output_bulle_overlay' );


function dismoi_cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['webp'] = 'image/webp';
	return $mimes;
}
add_filter('upload_mimes', 'dismoi_cc_mime_types');



/**
 * Add Bulles settings to the customizer.
 *
 * @since Theme 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function dismoi_prefix_customize_register( $wp_customize ) {

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

    $wp_customize->add_setting( 'bulle_setting_profiler_version',
        array(
            'type'       => 'theme_mod',
            'capability' => 'edit_theme_options',
            'default'    => '0.9'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_profiler_version',
            array(
                'label'          => __( 'Version JS Bundle pour la page "Les Contributeurs"', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_profiler_version'
            )
        )
    );

    // profile page
    $wp_customize->add_setting( 'bulle_setting_profile_page',
        array(
            'type'       => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'bulle_control_profile_page',
            array(
                'label'          => __( 'Page Profile (Assurer Régles Rewrite)', 'divi-child-bulle' ),
                'section'        => 'bulle_section',
                'settings'       => 'bulle_setting_profile_page',
                'type'           => 'dropdown-pages'
            )
        )
    );

    // Add settings for output description
    $wp_customize->add_setting( 'bulle_setting_profile_page_rewrite', array(
        'default'    => '',
        'type'       => 'theme_mod'
    ) );

    // Add control and output for select field
    $wp_customize->add_control( 'bulle_control_profile_page_rewrite', array(
        'label'      => __( 'Activer Page Profile Rewrite Rules', 'divi-child-bulle' ),
        'section'    => 'bulle_section',
        'settings'   => 'bulle_setting_profile_page_rewrite',
        'type'       => 'checkbox',
        'std'        => '1'
    ) );


}
add_action( 'customize_register', 'dismoi_prefix_customize_register' );


/**
 * Set up rewrites based on caonfiguration settings
 *
 */
function dismoi_profiler_rewrite_url( $wp_rewrite ) {
    $page_profile = get_theme_mod( 'bulle_setting_profile_page' );
    $page_profile_rules = get_theme_mod( 'bulle_setting_profile_page_rewrite' );
    if ( isset( $page_profile ) &&
        !empty( $page_profile ) &&
        $page_profile_rules == '1' ) {
        $slug_page_profile = get_post_field( 'post_name', get_post( $page_profile ) );

        $new_rules = array(
            $slug_page_profile . '/([0-9]+)/([^/]+)/?$' => 'index.php?pagename=' . $slug_page_profile,
            $slug_page_profile . '/([0-9]+)/?$' => 'index.php?pagename=' . $slug_page_profile
        );

        $wp_rewrite->rules =  $new_rules + $wp_rewrite->rules;
    }
    return $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules', 'dismoi_profiler_rewrite_url');



/**
 * Output Matomo tag manager tag
 *
 */
function dismoi_hook_matomo_tag() {
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
add_action('wp_head', 'dismoi_hook_matomo_tag');

/**
 *	This will hide the Divi "Project" post type.
 *	Thanks to georgiee (https://gist.github.com/EngageWP/062edef103469b1177bc#gistcomment-1801080) for his improved solution.
 */
add_filter( 'et_project_posttype_args', 'dismoi_et_project_posttype_args', 10, 1 );
function dismoi_et_project_posttype_args( $args ) {
    return array_merge( $args, array(
        'public'              => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => false,
        'show_in_nav_menus'   => false,
        'show_ui'             => false
    ));
}

add_filter ('widget_text', 'do_shortcode');

function dismoi_year_shortcode () {
    $year = date_i18n ('Y');
    return $year;
}
add_shortcode ('year', 'dismoi_year_shortcode');


/**
 * Function to get profile object
 * Use cacheed version if it exists to prevent unnecessary retrieval
 */
function get_profile_object ( $id ) {
    $key = 'PROFILE_' . $id;
    $group = 'PROFILES';
    $expiration = 60;

    // if cached object
    // $profile_object = get_transient( $key );
    $profile_object = wp_cache_get( $key, $group );

    if ( empty( $profile_object ) ) {
        try {
            $response = wp_remote_get(
                sprintf(
                    'https://notices.bulles.fr/api/v3/contributors/%s',
                    $id
                )
            );
            if ( is_array( $response ) && ! is_wp_error( $response ) && $response['body'] ) {
                // $headers = $response['headers']; // array of http header lines
                $profile_object    = json_decode( $response['body'] ); // use the content
                // for caching
                wp_cache_set( $key, $profile_object, $group, $expiration );
                // set_transient( $key, $profile_object, $expiration );
            }
            ;
        } catch ( Exception $ex ) {
        }
    }

    return $profile_object;
}

function dismoi_format_informateur_title($name) {
    return esc_attr(sprintf('%s - Informateur sur DisMoi', $name));
}

// override title
function dismoi_wpseo_title( $default ) {

    $informateur = dismoi_get_informateur_by_id();

    if ($informateur && !empty($informateur->name)) {
        return dismoi_format_informateur_title($informateur->name);
    }
    return $default;
}

add_filter( 'wpseo_twitter_title', 'dismoi_wpseo_title' );

function dismoi_wpseo_opengraph_title($default) {
    $informateur = dismoi_get_informateur_by_id();
    if ($informateur) {
        return $informateur->title ? $informateur->title : dismoi_format_informateur_title($informateur->name);
    }

    return $default;
}

add_filter( 'wpseo_opengraph_title', 'dismoi_wpseo_opengraph_title' );

function dismoi_wpseo_opengraph_image($default) {
    $informateur = dismoi_get_informateur_by_id();

    return ($informateur && $informateur->preview) ? $informateur->preview : $default;
}

add_filter( 'wpseo_opengraph_image', 'dismoi_wpseo_opengraph_image');

/**
 * Get description
 *
 * @param $default string
 * @return string
 */
function dismoi_wpseo_description( $default ) {

    $id = dismoi_get_informateur_id();

    if ( !empty( $id ) ) {
        $profile_object = get_profile_object( $id );

        if ( !empty( $profile_object ) && count( $profile_object ) > 0 && !empty( $profile_object->intro ) ) {
            return esc_attr( strip_tags( $profile_object->intro ) );
        }
    }
    return $default;
}
add_filter( 'wpseo_opengraph_desc', 'dismoi_wpseo_description' );
add_filter( 'wpseo_twitter_description', 'dismoi_wpseo_description' );
add_filter( 'wpseo_metadesc', 'dismoi_wpseo_description' );

/**
 * Get Informateur ID
 *
 * @return string
 */
function dismoi_get_informateur_id( ) {
    // only change if we're on the profiler template
    if ( get_page_template_slug() ===  'page-profile-app.php') {

        $path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
        if ( strpos( $path, '/' ) === 0 ) {
            $path = substr( $path, 1 );
        }
        $parts = explode("/", $path);

        if ( count( $parts ) > 1 ) {
            $id = $parts[1];
            return $id;
        }

    }
    return null;
}

/**
 * Get Informateur by id
 *
 * @return stdClass|null
 */
function dismoi_get_informateur_by_id( ) {
    $id = dismoi_get_informateur_id();
    if (!empty($id)) {
        return get_profile_object( $id );
    }

    return null;
}



/**
 * override OG url
 */
function dismoi_wpseo_opengraph_url( $default ) {
    $id = dismoi_get_informateur_id();
    if ( !empty( $id ) ) {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    return $default;
}
add_filter( 'wpseo_opengraph_url', 'dismoi_wpseo_opengraph_url' );


/**
 * Changes @type of Webpage Schema data.
 *
 * @param array $data Schema.org Webpage data array.
 *
 * @return array Schema.org Webpage data array.
 */
function dismoi_change_webpage( $data ) {
    $id = dismoi_get_informateur_id();
    if ( !empty( $id ) ) {

        // $data['@type'] = 'AboutPage';
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $data['url'] = $url;
        $data['@id'] = $url . '#webpage';

    }
    return $data;
}
add_filter( 'wpseo_schema_webpage', 'dismoi_change_webpage' );


/**
 * Filter title
 *
 * @param string $title
 *
 * @return string
 */
function dismoi_wpseo_meta_title( $title ) {
    if ( ! is_singular() ) return $title;

    $id = dismoi_get_informateur_id();
    if ( !empty( $id ) ) {
        $profile_object = get_profile_object( $id );

        if ( !empty( $profile_object ) && count( $profile_object ) > 0 && !empty( $profile_object->name ) ) {
            // $current_post = get_post();
            // $title = isset( $current_post->post_title ) ? $current_post->post_title : '';
            return sprintf(
                '%s - Informateur sur DisMoi',
                $profile_object->name
            );
        }
    }
}
add_filter( 'wpseo_title', 'dismoi_wpseo_meta_title', 11, 1 );


/**
 * Head hook
 *
 */
function dismoi_wpseo_canonical_informateurs( $url ) {
    if ( get_page_template_slug() ===  'page-profile-app.php') {
        $id = dismoi_get_informateur_id();
        if ( !empty( $id ) ) {
            $profile_object = get_profile_object( $id );
            if ( empty( $profile_object ) ) {
                return $url;
            }
            // construct canonical
            $arr = explode("-", sanitize_title( $profile_object->name ) );
            $transformed_arr = array_map( 'ucwords', $arr );
            return sprintf(
                '%s%s/%s',
                $url,
                $id,
                implode('-', $transformed_arr)
            );
        }
    }
    return $url;
}

add_action('wpseo_canonical', 'dismoi_wpseo_canonical_override');


