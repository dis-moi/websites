<?php

/**
 * Gets the dynamic content fields related to Product post type.
 *
 * @since 3.29
 *
 * @return array
 */
function et_builder_get_product_dynamic_content_fields() {
	return array(
		'product_breadcrumb'             => array(
			'label' => esc_html__( 'Product Breadcrumb', 'et_builder' ),
			'type'  => 'text',
		),
		'product_price'                  => array(
			'label' => esc_html__( 'Product Price', 'et_builder' ),
			'type'  => 'text',
		),
		'product_description'            => array(
			'label' => esc_html__( 'Product Description', 'et_builder' ),
			'type'  => 'text',
		),
		'product_short_description'      => array(
			'label' => esc_html__( 'Product Short Description', 'et_builder' ),
			'type'  => 'text',
		),
		'product_reviews_count'          => array(
			'label' => esc_html__( 'Product Reviews Count', 'et_builder' ),
			'type'  => 'text',
		),
		'product_sku'                    => array(
			'label' => esc_html__( 'Product SKU', 'et_builder' ),
			'type'  => 'text',
		),
		'product_reviews'                => array(
			'label' => esc_html__( 'Product Reviews', 'et_builder' ),
			'type'  => 'any',
		),
		'product_additional_information' => array(
			'label' => esc_html__( 'Product Additional Information', 'et_builder' ),
			'type'  => 'text',
		),
	);
}

/**
 * Get built-in dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id
 *
 * @return array<string, array>
 */
function et_builder_get_built_in_dynamic_content_fields( $post_id ) {
	$cache_key = 'et_builder_get_built_in_dynamic_content_fields';

	if ( et_core_cache_has( $cache_key ) ) {
		return et_core_cache_get( $cache_key );
	}

	$post_type           = get_post_type( $post_id );
	$post_type           = $post_type ? $post_type : 'post';
	$post_type_object    = get_post_type_object( $post_type );
	$post_type_label     = $post_type_object->labels->singular_name;
	$post_taxonomy_types = et_builder_get_taxonomy_types( get_post_type( $post_id ) );

	$default_category = 'post' === $post_type ? 'category' : "${post_type}_category";

	if ( ! empty( $post_taxonomy_types ) && ! isset( $post_taxonomy_types[$default_category] ) ) {
		// Use the 1st available taxonomy as the default value.
		// Do it in 2 steps in order to support PHP < 5.4 (array dereferencing).
		$default_category = array_keys( $post_taxonomy_types );
		$default_category = $default_category[0];
	}

	$date_format_options = array(
		'default' => esc_html__( 'Default', 'et_builder' ),
		'M j, Y'  => esc_html__( 'Aug 6, 1999 (M j, Y)', 'et_builder' ),
		'F d, Y'  => esc_html__( 'August 06, 1999 (F d, Y)', 'et_builder' ),
		'm/d/Y'   => esc_html__( '08/06/1999 (m/d/Y)', 'et_builder' ),
		'm.d.Y'   => esc_html__( '08.06.1999 (m.d.Y)', 'et_builder' ),
		'j M, Y'  => esc_html__( '6 Aug, 1999 (j M, Y)', 'et_builder' ),
		'l, M d'  => esc_html__( 'Tuesday, Aug 06 (l, M d)', 'et_builder' ),
		'custom'  => esc_html__( 'Custom', 'et_builder' ),
	);

	$fields = array(
		'post_title'                  => array(
			// Translators: %1$s: Post type name
			'label' => esc_html( sprintf( __( '%1$s Title', 'et_builder' ), $post_type_label ) ),
			'type'  => 'text',
		),
		'post_excerpt'                => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Excerpt', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'words'           => array(
					'label'   => esc_html__( 'Number of Words', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
				),
				'read_more_label' => array(
					'label'   => esc_html__( 'Read More Text', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
				),
			),
		),
		'post_date'                   => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Publish Date', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'date_format'        => array(
					'label'   => esc_html__( 'Date Format', 'et_builder' ),
					'type'    => 'select',
					'options' => $date_format_options,
					'default' => 'default',
				),
				'custom_date_format' => array(
					'label'   => esc_html__( 'Custom Date Format', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
					'show_if' => array(
						'date_format' => 'custom',
					),
				),
			),
		),
		'post_comment_count'          => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Comment Count', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'link_to_comments_page' => array(
					'label'   => esc_html__( 'Link to Comments Area', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default' => 'on',
				),
			),
		),
		'post_categories'             => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Categories', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'link_to_term_page' => array(
					'label'   => esc_html__( 'Link to Category Index Pages', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default' => 'on',
				),
				'separator'             => array(
					'label'   => esc_html__( 'Categories Separator', 'et_builder' ),
					'type'    => 'text',
					'default' => ' | ',
				),
				'category_type'         => array(
					'label'   => esc_html__( 'Category Type', 'et_builder' ),
					'type'    => 'select',
					'options' => $post_taxonomy_types,
					'default' => $default_category,
				),
			),
		),
		'post_tags'                   => array(),
		'post_link'                   => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Link', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'text'        => array(
					'label'   => esc_html__( 'Link Text', 'et_builder' ),
					'type'    => 'select',
					'options' => array(
						// Translators: %1$s: Post type name
						'post_title' => esc_html( sprintf( __( '%1$s Title', 'et_builder' ), $post_type_label ) ),
						'custom'     => esc_html__( 'Custom', 'et_builder' ),
					),
					'default' => 'post_title',
				),
				'custom_text' => array(
					'label'   => esc_html__( 'Custom Link Text', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
					'show_if' => array(
						'text' => 'custom',
					),
				),
			),
		),
		'post_author'                 => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Author', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'name_format'      => array(
					'label'   => esc_html__( 'Name Format', 'et_builder' ),
					'type'    => 'select',
					'options' => array(
						'display_name'    => esc_html__( 'Public Display Name', 'et_builder' ),
						'first_last_name' => esc_html__( 'First & Last Name', 'et_builder' ),
						'last_first_name' => esc_html__( 'Last, First Name', 'et_builder' ),
						'first_name'      => esc_html__( 'First Name', 'et_builder' ),
						'last_name'       => esc_html__( 'Last Name', 'et_builder' ),
						'nickname'        => esc_html__( 'Nickname', 'et_builder' ),
						'username'        => esc_html__( 'Username', 'et_builder' ),
					),
					'default' => 'display_name',
				),
				'link'             => array(
					'label'   => esc_html__( 'Link Name', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default' => 'off',
				),
				'link_destination' => array(
					'label'   => esc_html__( 'Link Destination', 'et_builder' ),
					'type'    => 'select',
					'options' => array(
						'author_archive' => esc_html__( 'Author Archive Page', 'et_builder' ),
						'author_website' => esc_html__( 'Author Website', 'et_builder' ),
					),
					'default' => 'author_archive',
					'show_if' => array(
						'link' => 'on',
					),
				),
			),
		),
		'post_author_bio'             => array(
			'label' => esc_html__( 'Author Bio', 'et_builder' ),
			'type'  => 'text',
		),
		'site_title'                  => array(
			'label' => esc_html__( 'Site Title', 'et_builder' ),
			'type'  => 'text',
		),
		'site_tagline'                => array(
			'label' => esc_html__( 'Site Tagline', 'et_builder' ),
			'type'  => 'text',
		),
		'current_date'                => array(
			'label'  => esc_html__( 'Current Date', 'et_builder' ),
			'type'   => 'text',
			'fields' => array(
				'date_format'        => array(
					'label'   => esc_html__( 'Date Format', 'et_builder' ),
					'type'    => 'select',
					'options' => $date_format_options,
					'default' => 'default',
				),
				'custom_date_format' => array(
					'label'   => esc_html__( 'Custom Date Format', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
					'show_if' => array(
						'date_format' => 'custom',
					),
				),
			),
		),
		'post_link_url'               => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( 'Current %1$s Link', 'et_builder' ), $post_type_label ) ),
			'type'   => 'url',
		),
		'home_url'                    => array(
			'label'  => esc_html__( 'Homepage Link', 'et_builder' ),
			'type'   => 'url',
		),
		'post_featured_image'         => array(
			'label'  => esc_html__( 'Featured Image', 'et_builder' ),
			'type'   => 'image',
		),
		'post_author_profile_picture' => array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Author Profile Picture', 'et_builder' ), $post_type_label ) ),
			'type'   => 'image',
		),
		'site_logo'                   => array(
			'label'  => esc_html__( 'Site Logo', 'et_builder' ),
			'type'   => 'image',
		),
	);

	/*
	 * Include Product dynamic fields on Product post type.
	 *
	 * This is enforced based on the discussion at
	 *
	 * @see https://github.com/elegantthemes/Divi/issues/15921#issuecomment-512707471
	 */
	if ( et_is_woocommerce_plugin_active() && 'product' === $post_type ) {
		$fields = array_merge( $fields, et_builder_get_product_dynamic_content_fields() );
	}

	// Fill in tag taxonomies.
	if ( isset( $post_taxonomy_types["${post_type}_tag"] ) ) {
		$fields['post_tags'] = array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Tags', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'link_to_term_page' => array(
					'label'   => esc_html__( 'Link to Tag Index Pages', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => esc_html__( 'Yes', 'et_builder' ),
						'off' => esc_html__( 'No', 'et_builder' ),
					),
					'default' => 'on',
				),
				'separator'        => array(
					'label'   => esc_html__( 'Tags Separator', 'et_builder' ),
					'type'    => 'text',
					'default' => ' | ',
				),
				'category_type'    => array(
					'label'   => esc_html__( 'Category Type', 'et_builder' ),
					'type'    => 'select',
					'options' => $post_taxonomy_types,
					'default' => "${post_type}_tag",
				),
			),
		);
	} else {
		unset( $fields['post_tags'] );
	}

	// Fill in post type URL options.
	$post_types = et_builder_get_public_post_types();
	foreach ( $post_types as $public_post_type ) {
		$public_post_type_label = $public_post_type->labels->singular_name;
		$key = 'post_link_url_' . $public_post_type->name;

		$fields[ $key ] = array(
			// Translators: %1$s: Post type name
			'label'  => esc_html( sprintf( __( '%1$s Link', 'et_builder' ), $public_post_type_label ) ),
			'type'   => 'url',
			'fields' => array(
				'post_id'            => array(
					'label'     => $public_post_type_label,
					'type'      => 'select_post',
					'post_type' => $public_post_type->name,
					'default'   => '',
				),
			),
		);
	}

	// Fill in boilerplate.
	foreach ( $fields as $key => $field ) {
		$fields[ $key ]['custom'] = false;

		if ( 'text' === $field['type'] ) {
			$settings = isset( $field['fields'] ) ? $field['fields'] : array();
			$settings = array_merge( array(
				'before' => array(
					'label'   => esc_html__( 'Before', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
				),
				'after'  => array(
					'label'   => esc_html__( 'After', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
				),
			), $settings );

			$fields[ $key ]['fields'] = $settings;
		}
	}

	et_core_cache_add( $cache_key, $fields );

	return $fields;
}

/**
 * Clear dynamic content fields cache whenever a custom post type is registered.
 *
 * @since 3.26.7
 *
 * @return void
 */
function et_builder_clear_get_built_in_dynamic_content_fields_cache() {
	et_core_cache_delete( 'et_builder_get_built_in_dynamic_content_fields' );
}
add_action( 'registered_post_type', 'et_builder_clear_get_built_in_dynamic_content_fields_cache' );

/**
 * Get all public taxonomies associated with a given post type.
 *
 * @since 3.17.2
 *
 * @param string $post_type
 *
 * @return array
 */
function et_builder_get_taxonomy_types( $post_type ) {
	$taxonomies = get_object_taxonomies( $post_type, 'object' );
	$list       = array();

	if ( empty( $taxonomies ) ) {
		return $list;
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! empty( $taxonomy ) && $taxonomy->public && $taxonomy->show_ui ) {
			$list[ $taxonomy->name ] = $taxonomy->label;
		}
	}

	return $list;
}

/**
 * Get custom dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id
 *
 * @return array<string, array>
 */
function et_builder_get_custom_dynamic_content_fields( $post_id ) {
	$raw_custom_fields = get_post_meta( $post_id );
	$custom_fields     = array();

	/**
	 * Filter post meta accepted as custom field options in dynamic content.
	 * Post meta prefixed with `_` is considered hidden from dynamic content options by default
	 * due to its nature as "hidden meta keys". This filter allows third parties to
	 * circumvent this limitation.
	 *
	 * @since 3.17.2
	 *
	 * @param array<string> $meta_keys
	 * @param integer $post_id
	 *
	 * @return array<string>
	 */
	$display_hidden_meta_keys = apply_filters( 'et_builder_dynamic_content_display_hidden_meta_keys', array(), $post_id );

	foreach ( $raw_custom_fields as $key => $values ) {
		if ( substr( $key, 0, 1 ) === '_' && ! in_array( $key, $display_hidden_meta_keys ) ) {
			// Ignore hidden meta keys.
			continue;
		}

		if ( substr( $key, 0, 3 ) === 'et_' ) {
			// Ignore ET meta keys as they are not suitable for dynamic content use.
			continue;
		}

		$label = str_replace( array( '_', '-' ), ' ', $key );
		$label = ucwords( $label );
		$label = trim( $label );

		/**
		 * Filter the display label for a custom field.
		 *
		 * @since 3.17.2
		 *
		 * @param string $label
		 * @param string $meta_key
		 */
		$label = apply_filters( 'et_builder_dynamic_content_custom_field_label', $label, $key );

		$field = array(
			'label'    => $label,
			'type'     => 'any',
			'fields'   => array(
				'before' => array(
					'label'   => esc_html__( 'Before', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
					'show_on' => 'text',
				),
				'after'  => array(
					'label'   => esc_html__( 'After', 'et_builder' ),
					'type'    => 'text',
					'default' => '',
					'show_on' => 'text',
				),
			),
			'meta_key' => $key,
			'custom'   => true,
		);

		if ( current_user_can( 'unfiltered_html' ) ) {
			$field['fields']['enable_html'] = array(
				'label'   => esc_html__( 'Enable raw HTML', 'et_builder' ),
				'type'    => 'yes_no_button',
				'options' => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default' => 'off',
				'show_on' => 'text',
			);
		}

		$custom_fields[ "custom_meta_{$key}" ] = $field;
	}

	/**
	 * Filter available custom field options for dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param array<string, array> $custom_fields
	 * @param integer $post_id
	 * @param array<string, mixed> $raw_custom_fields
	 *
	 * @return array<string, array>
	 */
	$custom_fields = apply_filters( 'et_builder_custom_dynamic_content_fields', $custom_fields, $post_id, $raw_custom_fields );

	return $custom_fields;
}

/**
 * Get all dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id
 * @param string $context
 *
 * @return array<string, array>
 */
function et_builder_get_dynamic_content_fields( $post_id, $context ) {
	$fields        = et_builder_get_built_in_dynamic_content_fields( $post_id );
	$custom_fields = array();

	if ( 'display' === $context || et_pb_is_allowed( 'read_dynamic_content_custom_fields' ) ) {
		$custom_fields = et_builder_get_custom_dynamic_content_fields( $post_id );
	}

	return array_merge( $fields, $custom_fields );
}

/**
 * Get default value for a dynamic content field's setting.
 *
 * @since 3.17.2
 *
 * @param integer $post_id
 * @param string $field
 * @param string $setting
 *
 * @return string
 */
function et_builder_get_dynamic_attribute_field_default( $post_id, $field, $setting ) {
	$_      = ET_Core_Data_Utils::instance();
	$fields = et_builder_get_dynamic_content_fields( $post_id, 'edit' );

	return $_->array_get( $fields, "$field.fields.$setting.default", '' );
}

/**
 * Resolve dynamic content to a simple value.
 *
 * @since 3.17.2
 *
 * @param string $name
 * @param array $settings
 * @param integer $post_id
 * @param string $context
 * @param array $overrides
 *
 * @return string
 */
function et_builder_resolve_dynamic_content( $name, $settings, $post_id, $context, $overrides = array(), $is_content = false ) {
	/**
	 * Generic filter for content resolution based on a given field and post.
	 *
	 * @since 3.17.2
	 *
	 * @param string $content
	 * @param string $name
	 * @param array $settings
	 * @param integer $post_id
	 * @param string $context
	 * @param array $overrides
	 *
	 * @return string
	 */
	$content = apply_filters( 'et_builder_resolve_dynamic_content', '', $name, $settings, $post_id, $context, $overrides );

	/**
	 * Field-specific filter for content resolution based on a given field and post.
	 *
	 * @since 3.17.2
	 *
	 * @param string $content
	 * @param array $settings
	 * @param integer $post_id
	 * @param string $context
	 * @param array $overrides
	 *
	 * @return string
	 */
	$content = apply_filters( "et_builder_resolve_dynamic_content_{$name}", $content, $settings, $post_id, $context, $overrides );

	return $is_content ? do_shortcode( $content ) : $content;
}

/**
 * Wrap a dynamic content value with its before/after settings values.
 *
 * @since 3.17.2
 *
 * @param integer $post_id
 * @param string $name
 * @param string $value
 * @param array $settings
 *
 * @return string
 */
function et_builder_wrap_dynamic_content( $post_id, $name, $value, $settings ) {
	$_       = ET_Core_Data_Utils::instance();
	$def     = 'et_builder_get_dynamic_attribute_field_default';
	$before  = $_->array_get( $settings, 'before', $def( $post_id, $name, 'before' ) );
	$after   = $_->array_get( $settings, 'after', $def( $post_id, $name, 'after' ) );
	$user_id = get_post_field( 'post_author', $post_id );

	if ( ! user_can( $user_id, 'unfiltered_html' ) ) {
		$before = esc_html( $before );
		$after  = esc_html( $after );
	}

	return $before . $value . $after;
}

/**
 * Resolve built-in dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param string $content
 * @param string $name
 * @param array $settings
 * @param string $context
 * @param integer $post_id
 *
 * @return string
 */
function et_builder_filter_resolve_default_dynamic_content( $content, $name, $settings, $post_id, $context, $overrides ) {
	global $shortname;

	$post = get_post( $post_id );

	if ( ! $post ) {
		return $content;
	}

	$_       = ET_Core_Data_Utils::instance();
	$def     = 'et_builder_get_dynamic_attribute_field_default';
	$author  = get_userdata( $post->post_author );
	$wrapped = false;

	switch ( $name ) {
		case 'product_title': // Intentional fallthrough.
		case 'post_title':
			$content = isset( $overrides[ $name ] ) ? $overrides[ $name ] : get_the_title( $post_id );
			$content = esc_html( $content );
			break;

		case 'post_excerpt':
			$words      = (int) $_->array_get( $settings, 'words', $def( $post_id, $name, 'words' ) );
			$read_more  = $_->array_get( $settings, 'read_more_label', $def( $post_id, $name, 'read_more_label' ) );
			$content    = isset( $overrides[ $name ] ) ? $overrides[ $name ] : get_the_excerpt( $post_id );

			if ( $words > 0 ) {
				$content = wp_trim_words( $content, $words );
			}

			if ( ! empty( $read_more ) ) {
				$content .= sprintf(
					' <a href="%1$s">%2$s</a>',
					esc_url( get_permalink( $post_id ) ),
					esc_html( $read_more )
				);
			}
			break;

		case 'post_date':
			$format        = $_->array_get( $settings, 'date_format', $def( $post_id, $name, 'date_format' ) );
			$custom_format = $_->array_get( $settings, 'custom_date_format', $def( $post_id, $name, 'custom_date_format' ) );

			if ( 'default' === $format ) {
				$format = '';
			}

			if ( 'custom' === $format ) {
				$format = $custom_format;
			}

			$content = esc_html( get_the_date( $format, $post_id ) );
			break;

		case 'post_comment_count':
			$link    = $_->array_get( $settings, 'link_to_comments_page', $def( $post_id, $name, 'link_to_comments_page' ) );
			$link    = 'on' === $link;
			$content = esc_html( get_comments_number( $post_id ) );

			if ( $link ) {
				$content = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( get_comments_link( $post_id ) ),
					et_core_esc_previously( et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings ) )
				);
				$wrapped = true;
			}
			break;

		case 'post_categories': // Intentional fallthrough.
		case 'post_tags':
			$post_taxonomies = et_builder_get_taxonomy_types( get_post_type( $post_id ) );
			$overrides_map   = array( 'category' => 'post_categories', 'post_tag' => 'post_tags' );

			$link      = $_->array_get( $settings, 'link_to_term_page', $def( $post_id, $name, 'link_to_category_page' ) );
			$link      = 'on' === $link;
			$separator = $_->array_get( $settings, 'separator', $def( $post_id, $name, 'separator' ) );
			$separator = ! empty( $separator ) ? $separator : $def( $post_id, $name, 'separator' );
			$taxonomy  = $_->array_get( $settings, 'category_type', '' );
			$taxonomy  = isset( $post_taxonomies[ $taxonomy ] ) ? $taxonomy : $def( $post_id, $name, 'category_type' );
			$ids_key   = isset( $overrides_map[ $taxonomy ] ) ? $overrides_map[ $taxonomy ] : '';
			$ids       = isset( $overrides[ $ids_key ] ) ? array_filter( array_map( 'intval', explode( ',', $overrides[ $ids_key ] ) ) ) : array();
			$terms     = ! empty( $ids ) ? get_terms( array( 'taxonomy' => $taxonomy, 'include'  => $ids ) ) : get_the_terms( $post_id, $taxonomy );
			if ( is_array( $terms ) ) {
				$content = et_builder_list_terms( $terms, $link, $separator );
			} else {
				$content = '';
			}
			break;

		case 'post_link':
			$text        = $_->array_get( $settings, 'text', $def( $post_id, $name, 'text' ) );
			$custom_text = $_->array_get( $settings, 'custom_text', $def( $post_id, $name, 'custom_text' ) );
			$label       = 'custom' === $text ? $custom_text : get_the_title( $post_id );
			$content     = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_permalink( $post_id ) ),
				esc_html( $label )
			);
			break;

		case 'post_author':
			$name_format      = $_->array_get( $settings, 'name_format', $def( $post_id, $name, 'name_format' ) );
			$link             = $_->array_get( $settings, 'link', $def( $post_id, $name, 'link' ) );
			$link             = 'on' === $link;
			$link_destination = $_->array_get( $settings, 'link_destination', $def( $post_id, $name, 'link_destination' ) );
			$link_target      = 'author_archive' === $link_destination ? '_self' : '_blank';
			$label            = '';
			$url              = '';

			if ( false === $author ) {
				$content = '';
				break;
			}

			switch( $name_format ) {
				case 'display_name':
					$label = $author->display_name;
					break;
				case 'first_last_name':
					$label = $author->first_name . ' ' . $author->last_name;
					break;
				case 'last_first_name':
					$label = $author->last_name . ', ' . $author->first_name;
					break;
				case 'first_name':
					$label = $author->first_name;
					break;
				case 'last_name':
					$label = $author->last_name;
					break;
				case 'nickname':
					$label = $author->nickname;
					break;
				case 'username':
					$label = $author->user_login;
					break;
			}

			switch ( $link_destination ) {
				case 'author_archive':
					$url = get_author_posts_url( $author->ID );
					break;
				case 'author_website':
					$url = $author->user_url;
					break;
			}

			$content = esc_html( $label );

			if ( $link && ! empty( $url ) ) {
				$content = sprintf(
					'<a href="%1$s" target="%2$s">%3$s</a>',
					esc_url( $url ),
					esc_attr( $link_target ),
					et_core_esc_previously( $content )
				);
			}
			break;

		case 'post_author_bio':
			if ( false === $author ) {
				$content = '';
				break;
			}

			$content = esc_html( $author->description );
			break;

		case 'site_title':
			$content = esc_html( get_bloginfo( 'name' ) );
			break;

		case 'site_tagline':
			$content = esc_html( get_bloginfo( 'description' ) );
			break;

		case 'current_date':
			$format        = $_->array_get( $settings, 'date_format', $def( $post_id, $name, 'date_format' ) );
			$custom_format = $_->array_get( $settings, 'custom_date_format', $def( $post_id, $name, 'custom_date_format' ) );

			if ( 'default' === $format ) {
				$format = get_option( 'date_format' );
			}

			if ( 'custom' === $format ) {
				$format = $custom_format;
			}

			$content = esc_html( date_i18n( $format ) );
			break;

		case 'post_link_url':
			$content = esc_url( get_permalink( $post_id ) );
			break;

		case 'home_url':
			$content = esc_url( home_url( '/' ) );
			break;

		case 'any_post_link_url':
			$selected_post_id  = $_->array_get( $settings, 'post_id', $def( $post_id, $name, 'post_id' ) );
			$content           = esc_url( get_permalink( $selected_post_id ) );
			break;

		case 'post_featured_image':
			if ( isset( $overrides[ $name ] ) ) {
				$id      = (int) $overrides[ $name ];
				$content = wp_get_attachment_image_url( $id, 'full' );
				break;
			}

			$url = get_the_post_thumbnail_url( $post_id, 'full' );
			$content = $url ? esc_url( $url ) : '';
			break;

		case 'post_author_profile_picture':
			$content = get_avatar_url( $author->ID );
			break;

		case 'site_logo':
			$logo    = et_get_option( $shortname . '_logo' );
			$content = '';

			if ( ! empty( $logo ) ) {
				$content = esc_url( $logo );
			}

			break;

		case 'product_breadcrumb':
			$content = ET_Builder_Module_Woocommerce_Breadcrumb::get_breadcrumb( array( 'product' => $post_id ) );
			break;

		case 'product_price':
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );
			$content = $product->get_price_html();
			break;

		case 'product_description':
			if ( et_pb_is_pagebuilder_used( $post_id ) ) {
				$content = get_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, true );
				$content = ! empty( $content ) ? $content : '';
			} else {
				$content = $post->post_content;
			}
			break;

		case 'product_short_description':
			$content = get_the_excerpt( $post_id );
			break;

		case 'product_reviews_count':
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );
			if ( $product ) {
				$content = $product->get_review_count();
			} else {
				$content = 0;
			}
			break;

		case 'product_sku':
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );
			if ( $product ) {
				$content = $product->get_sku();
			} else {
				$content = '';
			}
			break;

		case 'product_reviews':
			// Return early if comments are closed.
			if ( ! comments_open( $post_id ) ) {
				$content = '';
				break;
			}

			$product = wc_get_product( $post_id );
			if ( ! ( $product instanceof WC_Product ) ) {
				$content = '';
				break;
			}

			// Product description refers to Product short description.
			// Product short description is nothing but post excerpt.
			$args        = array( 'post_id' => $post_id );
			$comments    = get_comments( $args );
			$total_pages = get_comment_pages_count( $comments );
			$content     = wp_list_comments( array(
				'callback' => 'woocommerce_comments',
				'echo'     => false,
			), $comments );

			// Pass $product, $reviews to unify the flow of data.
			$reviews_title        = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_title( $product );
			$reviews_comment_form = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_comment_form( $product, $comments );
			$no_reviews_text      = sprintf(
				'<p class="woocommerce-noreviews">%s</p>',
				esc_html__( 'There are no reviews yet.', 'et_builder' )
			);

			$no_reviews = is_array( $comments ) && count( $comments ) > 0 ? '' : $no_reviews_text;

			if ( wp_doing_ajax() ) {
				$page = get_query_var( 'cpage' );
				if ( ! $page ) {
					$page = 1;
				}
				$args = array(
					'base'         => add_query_arg( 'cpage', '%#%' ),
					'format'       => '',
					'total'        => $total_pages,
					'current'      => $page,
					'echo'         => false,
					'add_fragment' => '#comments',
					'type'         => 'list',
				);
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$args['base'] = user_trailingslashit( trailingslashit( get_permalink() ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
				}

				$pagination = paginate_links( $args );
			} else {
				$pagination = paginate_comments_links( array(
					'echo'  => false,
					'type'  => 'list',
					'total' => $total_pages
				) );
			}

			$content = sprintf( '
						<div id="reviews" class="woocommerce-Reviews">
								<h2 class="woocommerce-Reviews-title">
									%1$s
								</h2>
							<div id="comments">
								<ol class="commentlist">
								%2$s
								</ol>
								<nav class="woocommerce-pagination">
									%5$s
								</nav>
								%4$s
							</div>
							<div id="review_form_wrapper">
								%3$s
							</div>
						</div>
						',
				et_core_esc_previously( $reviews_title ),
				et_core_esc_previously( $content ),
				et_core_esc_previously( $reviews_comment_form ),
				et_core_esc_previously( $no_reviews ),
				et_core_esc_previously( $pagination )
			);
			$wrapped = true;
			break;

		case 'product_additional_information':
			$default_content = '';
			if ( ! function_exists( 'wc_get_product' ) ) {
				$content = $default_content;
				break;
			}

			$product = wc_get_product( $post_id );
			if ( ! ( $product instanceof WC_Product ) ) {
				$content = $default_content;
				break;
			}

			if ( ! class_exists( 'ET_Builder_Module_Helper_Woocommerce_Modules' ) ) {
				$content = $default_content;
				break;
			}

			$title_markup = sprintf( '<h2 class="et_pb_wc_additional_info__header">%s</h2>',
				esc_html__( 'Additional Information', 'et_builder' )
			);

			$markup            = '';
			$weight_markup     = ET_Builder_Module_Helper_Woocommerce_Modules::get_weight_formatted( $product->get_id() );
			$dimensions_markup = ET_Builder_Module_Helper_Woocommerce_Modules::get_dimensions_formatted( $product->get_id() );

			if ( ! empty( $weight_markup ) ) {
				$markup .= sprintf( '
				<tr>
					<th>%1$s</th>
					<td>%2$s</td>
				</tr>
				',
					/* 1$s */
					esc_html__( 'Weight', 'et_builder' ),
					/* 2$s */
					esc_html( $weight_markup )
				);
			}

			if ( ! empty( $dimensions_markup ) ) {
				$markup .= sprintf( '
				<tr>
					<th>%1$s</th>
					<td>%2$s</td>
				</tr>'
					,
					/* 1$s */
					esc_html__( 'Dimensions', 'et_builder' ),
					/* 2$s */
					esc_html( $dimensions_markup )
				);
			}

			if ( $product->is_type( 'variable' ) ) {
				$variation_attributes = $product->get_variation_attributes();

				foreach ( $variation_attributes as $attribute => $attribute_values ) {
					$markup .= sprintf( '
					<tr>
					<th>%1$s</th>
					<td>%2$s</td>
					</tr>'
						,
						/* 1$s */
						esc_html( $attribute ),
						/* 2$s */
						esc_html( implode( ', ', $attribute_values ) )
					);
				}
			}

			$content = sprintf( '
				%1$s
				<table class="et_pb_wc_additional_info__table">
					%2$s
				</table>
				',
				/* 1$s */
				et_core_esc_previously( $title_markup ),
				/* 2$s */
				et_core_esc_previously( $markup )
			);

			break;
	}

	// Handle in post type URL options.
	$post_types = et_builder_get_public_post_types();
	foreach ( $post_types as $public_post_type ) {
		$key = 'post_link_url_' . $public_post_type->name;

		if ( $key !== $name ) {
			continue;
		}

		$selected_post_id  = $_->array_get( $settings, 'post_id', $def( $post_id, $name, 'post_id' ) );
		$content           = esc_url( get_permalink( $selected_post_id ) );
		break;
	}

	if ( ! $wrapped ) {
		$content = et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings );
		$wrapped = true;
	}

	return $content;
}
add_filter( 'et_builder_resolve_dynamic_content', 'et_builder_filter_resolve_default_dynamic_content', 10, 6 );

/**
 * Resolve custom field dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param string $content
 * @param string $name
 * @param array $settings
 * @param string $context
 * @param integer $post_id
 *
 * @return string
 */
function et_builder_filter_resolve_custom_field_dynamic_content( $content, $name, $settings, $post_id, $context, $overrides ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return $content;
	}

	$fields = et_builder_get_dynamic_content_fields( $post_id, $context );

	if ( empty( $fields[ $name ]['meta_key'] ) ) {
		return $content;
	}

	if ( 'edit' === $context && ! et_pb_is_allowed( 'read_dynamic_content_custom_fields' ) ) {
		if ( 'text' === $fields[ $name ]['type'] ) {
			return esc_html__( 'You don\'t have sufficient permissions to access this content.', 'et_builder' );
		}
		return '';
	}

	$_           = ET_Core_Data_Utils::instance();
	$def         = 'et_builder_get_dynamic_attribute_field_default';
	$enable_html = $_->array_get( $settings, 'enable_html', $def( $post_id, $name, 'enable_html' ) );
	$content     = get_post_meta( $post_id, $fields[ $name ]['meta_key'], true );

	/**
	 * Provide a hook for third party compatibility purposes of formatting meta values.
	 *
	 * @since 3.17.2
	 *
	 * @param string $meta_value
	 * @param string $meta_key
	 * @param integer $post_id
	 */
	$content = apply_filters( 'et_builder_dynamic_content_meta_value', $content, $fields[ $name ]['meta_key'], $post_id );

	// Sanitize HTML contents.
	$content = wp_kses_post( $content );

	if ( 'on' !== $enable_html ) {
		$content = esc_html( $content );
	}

	$content = et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings );

	return $content;
}
add_filter( 'et_builder_resolve_dynamic_content', 'et_builder_filter_resolve_custom_field_dynamic_content', 10, 6 );

/**
 * Resolve a dynamic group post content field for use during editing.
 *
 * @since 3.17.2
 *
 * @param string $field
 * @param array $settings
 * @param integer $post_id
 * @param array $overrides
 * @param boolean $is_content
 *
 * @return string
 */
function et_builder_filter_resolve_dynamic_post_content_field( $field, $settings, $post_id, $overrides = array(), $is_content = false ) {
	return et_builder_resolve_dynamic_content( $field, $settings, $post_id, 'edit', $overrides, $is_content );
}
add_action( 'et_builder_resolve_dynamic_post_content_field', 'et_builder_filter_resolve_dynamic_post_content_field', 10, 5 );

/**
 * Clean potential dynamic content from filter artifacts.
 *
 * @since 3.20.2
 *
 * @param string $value
 *
 * @return string
 */
function et_builder_clean_dynamic_content( $value ) {
	// Strip wrapping <p></p> tag as it appears in shortcode content in certain cases (e.g. BB preview).
	$value = preg_replace( '/^<p>(.*)<\/p>$/i', '$1', trim( $value ) );
	return $value;
}

/**
 * Parse a JSON-encoded string into an ET_Builder_Value instance or null on failure.
 *
 * @since 3.20.2
 *
 * @param string $json
 *
 * @return ET_Builder_Value|null
 */
function et_builder_parse_dynamic_content_json( $json ) {
	$dynamic_content    = json_decode( $json, true );
	$is_dynamic_content = is_array( $dynamic_content ) && isset( $dynamic_content['dynamic'] ) && (bool) $dynamic_content['dynamic'];
	$has_content        = is_array( $dynamic_content ) && isset( $dynamic_content['content'] ) && is_string( $dynamic_content['content'] );
	$has_settings       = is_array( $dynamic_content ) && isset( $dynamic_content['settings'] ) && is_array( $dynamic_content['settings'] );

	if ( ! $is_dynamic_content || ! $has_content || ! $has_settings ) {
		return null;
	}

	return new ET_Builder_Value(
		(bool) $dynamic_content['dynamic'],
		sanitize_text_field( $dynamic_content['content'] ),
		array_map( 'wp_kses_post', $dynamic_content['settings'] )
	);
}

/**
 * Convert a value to an ET_Builder_Value representation.
 *
 * @since 3.17.2
 *
 * @param string $content
 *
 * @return ET_Builder_Value
 */
function et_builder_parse_dynamic_content( $content ) {
	$json            = et_builder_clean_dynamic_content( $content );
	$json            = preg_replace( '/^@ET-DC@(.*?)@$/', '$1', $json );
	$dynamic_content = et_builder_parse_dynamic_content_json( $json );

	if ( null === $dynamic_content ) {
		$json            = base64_decode( $json );
		$dynamic_content = et_builder_parse_dynamic_content_json( $json );
	}

	if ( null === $dynamic_content ) {
		return new ET_Builder_Value( false, wp_kses_post( $content ), array() );
	}

	return $dynamic_content;
}

/**
 * Serialize dynamic content.
 *
 * @since 3.20.2
 *
 * @param boolean $dynamic
 * @param string $content
 * @param array<string, mixed> $settings
 *
 * @return string
 */
function et_builder_serialize_dynamic_content( $dynamic, $content, $settings ) {
	// JSON_UNESCAPED_SLASHES is only supported from 5.4.
	$options = defined( 'JSON_UNESCAPED_SLASHES' ) ? JSON_UNESCAPED_SLASHES : 0;
	$result  = wp_json_encode( array(
		'dynamic' => $dynamic,
		'content' => $content,
		// Force object type for keyed arrays as empty arrays will be encoded to
		// javascript arrays instead of empty objects.
		'settings' => (object) $settings,
	), $options );

	// Use fallback if needed
	$result = 0 === $options ? str_replace( '\/', '/', $result ) : $result;

	return '@ET-DC@' . base64_encode( $result ) . '@';
}

/**
 * Reencode legacy dynamic content in post excerpts.
 *
 * @since 3.20.2
 *
 * @param string $post_excerpt
 * @param integer $post_id
 *
 * @return string
 */
function et_builder_reencode_legacy_dynamic_content_in_excerpt( $post_excerpt, $post_id ) {
	$json = '/
		\{              # { character
			(?:         # non-capturing group
				[^{}]   # anything that is not a { or }
				|       # OR
				(?R)    # recurse the entire pattern
			)*          # previous group zero or more times
		\}              # } character
	/x';

	return preg_replace_callback( $json, 'et_builder_reencode_legacy_dynamic_content_in_excerpt_callback', $post_excerpt );
}
add_filter( 'et_truncate_post', 'et_builder_reencode_legacy_dynamic_content_in_excerpt', 10, 2 );

/**
 * Callback to reencode legacy dynamic content for preg_replace_callback.
 *
 * @since 3.20.2
 *
 * @param array $matches
 *
 * @return string
 */
function et_builder_reencode_legacy_dynamic_content_in_excerpt_callback( $matches ) {
	$value = et_builder_parse_dynamic_content_json( $matches[0] );
	return null === $value ? $matches[0] : $value->serialize();
}

/**
 * Resolve dynamic content in post excerpts instead of showing raw JSON.
 *
 * @since 3.17.2
 *
 * @param string $post_excerpt
 * @param integer $post_id
 *
 * @return string
 */
function et_builder_resolve_dynamic_content_in_excerpt( $post_excerpt, $post_id ) {
	// Use an obscure acronym named global variable instead of an anonymous function as we are
	// targeting PHP 5.2.
	global $_et_brdcie_post_id;

	$_et_brdcie_post_id = $post_id;
	$post_excerpt = preg_replace_callback( '/@ET-DC@.*?@/', 'et_builder_resolve_dynamic_content_in_excerpt_callback', $post_excerpt );
	$_et_brdcie_post_id = 0;

	return $post_excerpt;
}
add_filter( 'et_truncate_post', 'et_builder_resolve_dynamic_content_in_excerpt', 10, 2 );

/**
 * Callback to resolve dynamic content for preg_replace_callback.
 *
 * @since 3.17.2
 *
 * @param array $matches
 *
 * @return string
 */
function et_builder_resolve_dynamic_content_in_excerpt_callback( $matches ) {
	global $_et_brdcie_post_id;
	return et_builder_parse_dynamic_content( $matches[0] )->resolve( $_et_brdcie_post_id );
}
