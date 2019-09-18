<?php
/**
 * Multi View Options helper class file
 *
 * @package ET/Builder
 *
 * @since 3.27.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

// Include dependency for ResponsiveOptions.
if ( ! function_exists( 'et_pb_responsive_options' ) ) {
	require_once 'ResponsiveOptions.php';
}

// Include dependency for HoverOptions.
if ( ! function_exists( 'et_pb_hover_options' ) ) {
	require_once 'HoverOptions.php';
}

/**
 * Multi View Options helper class
 *
 * Class ET_Builder_Module_Helper_MultiViewOptions
 *
 * @since 3.27.1
 */
class ET_Builder_Module_Helper_MultiViewOptions {

	/**
	 * HTML data attribute key.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $data_attr_key = 'data-et-multi-view';

	/**
	 * Find and replace data regex pattern.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $pattern = '/\{\{(.+)\}\}/';

	/**
	 * Module slug.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $module;

	/**
	 * Module props data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $props = array();

	/**
	 * Module slug.
	 *
	 * @since 3.27.1
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Custom props data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $custom_props = array();

	/**
	 * Conditional values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $conditional_values = array();

	/**
	 * Default values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $default_values = array();

	/**
	 * Cached values data.
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	protected $cached_values = array();

	/**
	 * Hover enabled option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $hover_enabled_suffix = '__hover_enabled';

	/**
	 * Responsive enabled option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $responsive_enabled_suffix = '_last_edited';

	/**
	 * Hover option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $hover_suffix = '__hover';

	/**
	 * Tablet option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $tablet_suffix = '_tablet';

	/**
	 * Phone option name suffix
	 *
	 * @since 3.27.1
	 *
	 * @var array
	 */
	public static $phone_suffix = '_phone';

	/**
	 * Class constructor
	 *
	 * @since 3.27.1
	 *
	 * @param ET_Builder_Element $module             Module object.
	 * @param array              $custom_props       Defined custom props data.
	 * @param array              $conditional_values Defined options conditional values.
	 * @param array              $default_values     Defined options default values.
	 */
	public function __construct( $module = false, $custom_props = array(), $conditional_values = array(), $default_values = array() ) {
		$this->set_module( $module );
		$this->set_custom_props( $custom_props );
		$this->set_conditional_values( $conditional_values );
		$this->set_default_values( $default_values );
	}

	/**
	 * Get props name by mode
	 *
	 * @since 3.27.1
	 *
	 * @param string $name Props name.
	 * @param string $mode Selected view mode.
	 *
	 * @return string
	 */
	public static function get_name_by_mode( $name, $mode ) {
		if ( 'tablet' === $mode || 'phone' === $mode ) {
			return "{$name}_{$mode}";
		}

		if ( 'hover' === $mode ) {
			return "{$name}__hover";
		}

		return $name;
	}

	/**
	 * Get view modes
	 *
	 * @since 3.27.1
	 *
	 * @return array
	 */
	public static function get_modes() {
		return array( 'desktop', 'tablet', 'phone', 'hover' );
	}

	/**
	 * Check if mode is enabled
	 *
	 * @since 3.27.1
	 *
	 * @param string $name Props name.
	 * @param string $mode Selected view mode.
	 *
	 * @return bool
	 */
	public function mode_is_enabled( $name, $mode ) {
		switch ( $mode ) {
			case 'hover':
				return $this->hover_is_enabled( $name );

			case 'tablet':
			case 'phone':
				return $this->responsive_is_enabled( $name );

			default:
				return true;
		}
	}

	/**
	 * Get responsive options filed suffixes
	 *
	 * @since 3.27.1
	 *
	 * @param bool $include_enabled_suffix Wethere to include the responsive enabled suffix or not.
	 *
	 * @return array
	 */
	public static function responsive_suffixes( $include_enabled_suffix = true ) {
		$suffixes = array( self::$tablet_suffix, self::$phone_suffix );

		if ( $include_enabled_suffix ) {
			$suffixes[] = self::$responsive_enabled_suffix;
		}

		return $suffixes;
	}

	/**
	 * Get hover options filed suffixes
	 *
	 * @since 3.27.1
	 *
	 * @param bool $include_enabled_suffix Wethere to include the hover enabled suffix or not.
	 *
	 * @return array
	 */
	public static function hover_suffixes( $include_enabled_suffix = true ) {
		$suffixes = array( self::$hover_suffix );

		if ( $include_enabled_suffix ) {
			$suffixes[] = self::$hover_enabled_suffix;
		}

		return $suffixes;
	}

	/**
	 * Check wheter an option is responsive enabled.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name options name.
	 *
	 * @return bool
	 */
	public function responsive_is_enabled( $name ) {
		return et_pb_responsive_options()->is_enabled( $name, $this->props );
	}

	/**
	 * Check wheter an option is hover enabled.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name options name.
	 *
	 * @return bool
	 */
	public function hover_is_enabled( $name ) {
		return et_pb_hover_options()->is_enabled( $name, $this->props );
	}

	/**
	 * Get module props desktop mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_desktop( $name, $default_value = '' ) {
		if ( '' === strval( $default_value ) && isset( $this->default_values[ $name ]['desktop'] ) ) {
			$default_value = $this->default_values[ $name ]['desktop'];
		}

		if ( isset( $this->custom_props[ $name ]['desktop'] ) ) {
			return et_()->array_get( $this->custom_props[ $name ], 'desktop', $default_value );
		}

		return et_()->array_get( $this->props, $name, $default_value );
	}

	/**
	 * Get module props tablet mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_tablet( $name, $default_value = '' ) {
		if ( '' === strval( $default_value ) && isset( $this->default_values[ $name ]['tablet'] ) ) {
			$default_value = $this->default_values[ $name ]['tablet'];
		}

		if ( isset( $this->custom_props[ $name ]['tablet'] ) ) {
			return et_()->array_get( $this->custom_props[ $name ], 'tablet', $default_value );
		} elseif ( $this->responsive_is_enabled( $name ) ) {
			return et_pb_responsive_options()->get_tablet_value( $name, $this->props, $default_value );
		}

		return $default_value;
	}

	/**
	 * Get module props phone mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_phone( $name, $default_value = '' ) {
		if ( '' === strval( $default_value ) && isset( $this->default_values[ $name ]['phone'] ) ) {
			$default_value = $this->default_values[ $name ]['phone'];
		}

		if ( isset( $this->custom_props[ $name ]['phone'] ) ) {
			return et_()->array_get( $this->custom_props[ $name ], 'phone', $default_value );
		} elseif ( $this->responsive_is_enabled( $name ) ) {
			return et_pb_responsive_options()->get_phone_value( $name, $this->props, $default_value );
		}

		return $default_value;
	}

	/**
	 * Get module props hover mode value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value_hover( $name, $default_value = '' ) {
		if ( '' === strval( $default_value ) && isset( $this->default_values[ $name ]['phone'] ) ) {
			$default_value = $this->default_values[ $name ]['phone'];
		}

		if ( isset( $this->custom_props[ $name ]['hover'] ) ) {
			return et_()->array_get( $this->custom_props[ $name ], 'hover', $default_value );
		} elseif ( $this->hover_is_enabled( $name ) ) {
			return et_pb_hover_options()->get_value( $name, $this->props, $default_value );
		}

		return $default_value;
	}

	/**
	 * Get module props value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param string $mode          Select only specified modes: desktop, tablet, phone, hover.
	 * @param mixed  $default_value Default value as fallback data.
	 *
	 * @return mixed Value of selected mode.
	 */
	public function get_value( $name, $mode = 'desktop', $default_value = '' ) {
		switch ( $mode ) {
			case 'hover':
				return $this->get_value_hover( $name, $default_value );

			case 'tablet':
				return $this->get_value_tablet( $name, $default_value );

			case 'phone':
				return $this->get_value_phone( $name, $default_value );

			default:
				return $this->get_value_desktop( $name, $default_value );
		}
	}

	/**
	 * Get module props values.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name     Props name.
	 * @param bool   $distinct Wether to disticnt the values or not.
	 *
	 * @return array Values of all view modes: desktop, tablet, phone, hover.
	 */
	public function get_values( $name, $distinct = true ) {
		$cache_key = $distinct ? $name . '__distinct' : $name;

		if ( isset( $this->cached_values[ $cache_key ] ) ) {
			return $this->cached_values[ $cache_key ];
		}

		$values = array();

		foreach ( self::get_modes() as $mode ) {
			if ( ! $this->mode_is_enabled( $name, $mode ) && ! isset( $this->custom_props[ $name ] ) ) {
				continue;
			}

			$values[ $mode ] = $this->get_value( $name, $mode );
		}

		// Normalize the values to make to have all the view modes hold each data.
		$values = $this->normalize_values( $values );

		if ( $distinct ) {
			$values = $this->distinct_values( $values );
		}

		$this->cached_values[ $cache_key ] = $values;

		return $values;
	}

	/**
	 * Compare values
	 *
	 * @since 3.27.1
	 *
	 * @param string $value Source value.
	 * @param [type] $value_compare Target value to compare.
	 *
	 * @return bool
	 */
	protected static function compare_value( $value, $value_compare = null ) {
		$match = false;

		if ( is_null( $value_compare ) ) {
			$match = '' !== $value;
		} elseif ( is_array( $value_compare ) ) {
			$match = in_array( $value, $value_compare, true );
		} elseif ( is_callable( $value_compare ) ) {
			$match = call_user_func( $value_compare, $value );
		} elseif ( '__empty' === $value_compare ) {
			$match = empty( $value );
		} elseif ( '__not_empty' === $value_compare ) {
			$match = ! empty( $value );
		} else {
			$match = strtolower( strval( $value_compare ) ) === strtolower( strval( $value ) );
		}

		return $match ? true : false;
	}

	/**
	 * Check if module props has value in any of data breakpoint: desktop, tablet, phone, hover.
	 *
	 * @since 3.27.1
	 *
	 * @param string          $name           Field key.
	 * @param string|callable $value_compare  The value to compare.
	 * @param string          $selected_mode  Selected view mode.
	 * @param bool            $inherit       Should the value inherited from previous breakpoint.
	 *
	 * @return bool
	 */
	public function has_value( $name, $value_compare = null, $selected_mode = false, $inherit = false ) {
		$has_value = false;

		if ( $selected_mode && is_string( $selected_mode ) ) {
			$selected_mode = false !== strpos( $selected_mode, ',' ) ? explode( ',', $selected_mode ) : array( $selected_mode );
		}

		if ( $selected_mode && ! is_array( $selected_mode ) ) {
			$selected_mode = array( $selected_mode );
		}

		$values = $this->get_values( $name, false );

		foreach ( $values as $mode => $value ) {
			if ( $selected_mode && ! in_array( $mode, $selected_mode, true ) ) {
				continue;
			}

			$has_value = self::compare_value( $value, $value_compare );

			if ( ! $has_value && 'desktop' !== $mode && $inherit ) {
				$has_value = self::compare_value( $this->get_inherit_value( $name, $mode ), $value_compare );
			}

			if ( $has_value ) {
				break;
			}
		}

		return $has_value;
	}

	/**
	 * Get props inherit value
	 *
	 * @since 3.27.1
	 *
	 * @param string $name           Field key.
	 * @param string $selected_mode  Selected view mode.
	 *
	 * @return mixed
	 */
	public function get_inherit_value( $name, $selected_mode ) {
		$values = $this->get_values( $name, false );

		if ( isset( $values[ $selected_mode ] ) && ! is_null( $values[ $selected_mode ] ) ) {
			return $values[ $selected_mode ];
		}

		if ( ( 'hover' === $selected_mode || 'tablet' === $selected_mode ) && isset( $values['desktop'] ) && ! is_null( $values['desktop'] ) ) {
			return $values['desktop'];
		}

		if ( 'phone' === $selected_mode && isset( $values['tablet'] ) && ! is_null( $values['tablet'] ) ) {
			return $values['tablet'];
		}

		if ( 'phone' === $selected_mode && isset( $values['desktop'] ) && ! is_null( $values['desktop'] ) ) {
			return $values['desktop'];
		}

		return null;
	}

	/**
	 * Get module props conditional value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Props name.
	 * @param string $mode          Select only specified modes: desktop, tablet, phone, hover.
	 * @param mixed  $conditionals  Extra data to compare.
	 *
	 * @return mixed Calculated conditional value. Will return null if not match any comparison.
	 */
	public function get_conditional_value( $name, $mode = 'desktop', $conditionals = array() ) {
		if ( ! $this->conditional_values ) {
			return null;
		}

		$value = null;

		foreach ( $this->conditional_values as $compare ) {
			if ( ! isset( $compare['name'] ) || $compare['name'] !== $name ) {
				continue;
			}

			if ( isset( $compare['conditionals'] ) && $compare['conditionals'] ) {
				$is_conditionals_match = true;

				foreach ( $compare['conditionals'] as $conditional_key => $conditional_value ) {
					if ( ! isset( $conditionals[ $conditional_key ] ) || $conditionals[ $conditional_key ] !== $conditional_value ) {
						$is_conditionals_match = false;
						break;
					}
				}

				if ( ! $is_conditionals_match ) {
					continue;
				}
			}

			if ( isset( $compare['props'] ) && $compare['props'] ) {
				$is_props_match = true;

				foreach ( $compare['props'] as $prop_key => $prop_value ) {
					if ( ! $prop_key && ! is_numeric( $prop_key ) ) {
						$is_props_match = false;
						break;
					}

					if ( 'hover' === $mode && ! $this->hover_is_enabled( $prop_key ) ) {
						$mode = false;
					}

					if ( in_array( $mode, array( 'tablet', 'phone' ), true ) && ! $this->responsive_is_enabled( $prop_key ) ) {
						$mode = false;
					}

					if ( ! $this->has_value( $prop_key, $prop_value, $mode ) ) {
						$is_props_match = false;
						break;
					}
				}

				if ( ! $is_props_match ) {
					continue;
				}
			}

			$value = $compare['value'];

			if ( preg_match_all( $this->pattern, $value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$value = str_replace( $match[0], $this->get_value( $match[1], $mode ), $value );
				}
			}
		}

		return $value;
	}

	/**
	 * Set module object.
	 *
	 * @since 3.27.1
	 *
	 * @param ET_Builder_Element $module Module object.
	 */
	public function set_module( $module ) {
		if ( ! $module instanceof ET_Builder_Element ) {
			return et_debug( __( 'Invalid module instance passed to ET_Builder_Module_Helper_MultiViewOptions::set_module', 'et_builder' ) );
		}

		$this->module = $module;

		if ( property_exists( $module, 'slug' ) ) {
			$this->slug = $module->slug;
		}

		if ( property_exists( $module, 'props' ) && $module->props && is_array( $module->props ) ) {
			$props = $module->props;

			if ( empty( $props['content'] ) && property_exists( $module, 'content' ) ) {
				$props['content'] = $module->content;
			}

			if ( in_array( $module->slug, array( 'et_pb_code', 'et_pb_fullwidth_code' ), true ) ) {
				if ( isset( $props['content'] ) ) {
					$props['raw_content'] = $props['content'];
				}

				if ( isset( $props[ 'content' . self::$hover_enabled_suffix ] ) ) {
					$props[ 'raw_content' . self::$hover_enabled_suffix ] = $props[ 'content' . self::$hover_enabled_suffix ];
				}

				if ( isset( $props[ 'content' . self::$responsive_enabled_suffix ] ) ) {
					$props[ 'raw_content' . self::$responsive_enabled_suffix ] = $props[ 'content' . self::$responsive_enabled_suffix ];
				}
			}

			foreach ( $props as $key => $value ) {
				if ( '' === strval( $value ) ) {
					continue;
				}

				$this->props[ $key ] = $value;
			}
		}
	}

	/**
	 * Set option default value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Data key.
	 * @param array  $default_value Default value.
	 */
	public function set_default_value( $name, $default_value ) {
		$this->default_values[ $name ] = $this->normalize_values( $default_value );
	}

	/**
	 * Set options default values.
	 *
	 * @since 3.27.1
	 *
	 * @param array $default_values Default values.
	 */
	public function set_default_values( $default_values ) {
		if ( $default_values && is_array( $default_values ) ) {
			foreach ( $default_values as $name => $value ) {
				$this->set_default_value( $name, $value );
			}
		}
	}

	/**
	 * Set option conditional value.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name          Prop key.
	 * @param string $value         Custom conditional value.
	 * @param array  $props         Key value pair of props list to compare.
	 * @param array  $conditionals  Conditionals parameter go compare to calculate the value.
	 */
	public function set_conditional_value( $name, $value, $props, $conditionals = array() ) {
		if ( ! $props || ! is_array( $props ) ) {
			return;
		}

		if ( ! is_array( $conditionals ) ) {
			return;
		}

		$conditional = array(
			// Order index is used to preserve original order when sorting "equal" items
			// as the order of "equal" items in PHP is "undefined" after sorting.
			'order'        => count( $this->conditional_values ),
			'name'         => $name,
			'value'        => $value,
			'props'        => $props,
			'conditionals' => $conditionals,
		);

		$this->conditional_values[] = $conditional;

		// Sort by count of props and count of conditionals.
		usort( $this->conditional_values, array( $this, 'sort_conditional_values' ) );
	}

	/**
	 * Set option conditional values.
	 *
	 * @since 3.27.1
	 *
	 * @param array $conditional_values Default values.
	 */
	public function set_conditional_values( $conditional_values ) {
		if ( ! $conditional_values || ! is_array( $conditional_values ) ) {
			return;
		}

		foreach ( $conditional_values as  $conditional_key => $param ) {
			if ( ! isset( $param['value'] ) ) {
				continue;
			}

			if ( ! isset( $param['props'] ) ) {
				continue;
			}

			$conditionals = isset( $param['conditionals'] ) ? $param['conditionals'] : array();

			$this->set_conditional_value( $conditional_key, $param['value'], $param['props'], $conditionals );
		}
	}

	/**
	 * Set custom variable data.
	 *
	 * @since 3.27.1
	 *
	 * @param string $name   Data key.
	 * @param array  $values The values to inject.
	 */
	public function set_custom_prop( $name, $values ) {
		$this->custom_props[ $name ] = $this->normalize_values( $values );
	}

	/**
	 * Set custom variables data.
	 *
	 * @since 3.27.1
	 *
	 * @param array $custom_props Defined custom props data.
	 */
	public function set_custom_props( $custom_props ) {
		if ( $custom_props && is_array( $custom_props ) ) {
			foreach ( $custom_props as $name => $values ) {
				$this->set_custom_prop( $name, $values );
			}
		}
	}

	/**
	 * Render the multi view HTML element
	 *
	 *      Example:
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Hello {{name}}', // Assume name props value is John
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div>Hello John</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'p',
	 *          'content' => 'get_the_title', // Assume current page title is Hello World
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <p>Hello World</p>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'h3',
	 *          'content' => get_the_title(), // Assume current page title is Hello World
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <h3>Hello World</h3>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'img',
	 *          'attrs'   => array(
	 *              'src'    => '{{image_url}}, // Assume image_url props value is test.jpg
	 *              'width'  => '{{image_width}}px', // Assume image_width props value is 50
	 *              'height' => '{{image_height}}px', // Assume image_height props value is 100
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <img src="test.jpg" width="50px" height="100px" />
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'attrs'   => array(
	 *              'background-image' => 'url({{image_url}})', // Assume image_url props value is test.jpg
	 *              'font-size'        => '{{title_font_size}}px', // Assume title_font_size props value is 20
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div style="background-image: url(test.jpg); font-size: 20px;">Lorem Ipsum</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'classes' => array(
	 *              'et_pb_slider_no_arrows' => array
	 *                 'show_arrows' => 'off', // Assume show_arrows props value is off
	 *              ),
	 *              'et_pb_slider_carousel'  => array
	 *                  'show_thumbnails' => 'on', // Assume show_thumbnails props value is on
	 *              ),
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output:
	 *        <div class=et_pb_slider_no_arrows et_pb_slider_carousel">Lorem Ipsum</div>
	 *
	 *      $multi_view->render_element( array(
	 *          'tag'     => 'div',
	 *          'content' => 'Lorem Ipsum',
	 *          'visibility' => array(
	 *              'show_arrows'     => 'on',
	 *              'show_thumbnails' => 'off',
	 *          ),
	 *      ) );
	 *
	 *      - Will generate output that will visible when show_arrows is on and show_thumbnails is off:
	 *        <div>Lorem Ipsum</div>
	 *
	 * @param array   $contexts {
	 *       Data contexts.
	 *
	 *     @type string          $tag                HTML element tag name. Example: div, img, p. Default is span.
	 *
	 *     @type string|callable $content            Param that will be used to populate the content data.
	 *                                               Callable function as value is allowed and excuted. Example: 'get_the_title' function.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' atribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is assciative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 *
	 *     @type string          $target             HTML element selector target which the element will be modified. Default is empty string.
	 *                                               Dynamic module order class wilcard string is accepted: %%order_class%%
	 *
	 *     @type string          $hover_selector     HTML element selector which trigger the hover event. Default is empty string.
	 *                                               Dynamic module order class wilcard string is accepted: %%order_class%%
	 *
	 *     @type string          $render_slug        Render slug that will be used to calculate the module order class. Default is current module slug.
	 *
	 *     @type array           $custom_props       Defined custom props data.
	 *
	 *     @type array           $conditional_values Defined data sources for data toggle.
	 *
	 *     @type array           $required           List of requireds props key to render the element.
	 *                                               Will returning empty string if any required props is empty.
	 *                                               Default is empty array it will try to gather any props name set in the 'content' context.
	 *                                               Set to false to diable conditional check.
	 * }
	 * @param boolean $echo Whether to print the output instead returning it.
	 *
	 * @return string|void
	 *
	 * @since 3.27.1
	 */
	public function render_element( $contexts = array(), $echo = false ) {
		// Define the array of defaults.
		$defaults = array(
			'tag'                => 'span',
			'content'            => '',
			'attrs'              => array(),
			'styles'             => array(),
			'classes'            => array(),
			'visibility'         => array(),
			'target'             => '',
			'hover_selector'     => '',
			'render_slug'        => '',
			'custom_props'       => array(),
			'conditional_values' => array(),
			'required'           => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		// Set custom props data.
		if ( $contexts['custom_props'] && is_array( $contexts['custom_props'] ) ) {
			$this->set_custom_props( $contexts['custom_props'] );
		}
		unset( $contexts['custom_props'] );

		// Set conditional values data.
		if ( $contexts['conditional_values'] && is_array( $contexts['conditional_values'] ) ) {
			$this->set_conditional_values( $contexts['conditional_values'] );
		}
		unset( $contexts['conditional_values'] );

		// Validate element tag.
		$tag = et_core_sanitize_element_tag( $contexts['tag'] );

		// Bail early when the tag is invalid.
		if ( ! $tag || is_wp_error( $tag ) ) {
			return '';
		}

		// Bail early when required props is not fulfilled.
		if ( ! $this->is_required_props_fulfilled( $contexts ) ) {
			return '';
		}

		// Populate the element data.
		$data = $this->populate_data( $contexts );

		// Bail early when data is empty.
		if ( ! $data ) {
			return '';
		}

		$desktop_attrs   = '';
		$desktop_styles  = array();
		$desktop_classes = array();

		// Generate desktop attribute.
		foreach ( et_()->array_get( $data, 'attrs.desktop', array() ) as $attr_key => $attr_value ) {
			$skip = is_string( $attr_value ) || is_numeric( $attr_value ) ? ! strlen( $attr_value ) : empty( $attr_value );

			if ( $skip ) {
				continue;
			}

			if ( 'style' === $attr_key ) {
				foreach ( explode( ';', $attr_value ) as $inline_style ) {
					$inline_styles = explode( ':', $inline_style );

					if ( count( $inline_styles ) === 2 ) {
						$desktop_styles[ $inline_styles[0] ] = $inline_styles[1];
					}
				}

				continue;
			} elseif ( 'class' === $attr_key ) {
				if ( is_string( $attr_value ) ) {
					$desktop_classes = array_merge( $desktop_classes, explode( ' ', $attr_value ) );
				} elseif ( is_array( $attr_value ) ) {
					$desktop_classes = array_merge( $desktop_classes, $attr_value );
				}
			} else {
				if ( ! is_string( $attr_value ) ) {
					$attr_value = esc_attr( wp_json_encode( $attr_value ) );
				}

				$desktop_attrs .= ' ' . esc_attr( $attr_key ) . '="' . et_core_esc_previously( $attr_value ) . '"';
			}
		}

		// Inject desktop inline style attribute.
		foreach ( et_()->array_get( $data, 'styles.desktop', array() ) as $style_key => $style_value ) {
			$desktop_styles[ $style_key ] = $style_value;
		}

		if ( $desktop_styles ) {
			$styles = array();

			foreach ( $desktop_styles as $style_key => $style_value ) {
				$styles[] = esc_attr( $style_key ) . ':' . et_core_esc_previously( $style_value );
			}

			$desktop_attrs .= ' style="' . implode( ';', $styles ) . '"';
		}

		// Inject desktop class attribute.
		foreach ( et_()->array_get( $data, 'classes.desktop', array() ) as $class_action => $class_names ) {
			foreach ( $class_names as $class_name ) {
				if ( 'remove' === $class_action && in_array( $class_name, $desktop_classes, true ) ) {
					$desktop_classes = array_diff( $desktop_classes, array( $class_name ) );
				}

				if ( 'add' === $class_action && ! in_array( $class_name, $desktop_classes, true ) ) {
					$desktop_classes[] = $class_name;
				}
			}
		}

		// Inject desktop visibility class attribute.
		if ( ! et_()->array_get( $data, 'visibility.desktop', true ) ) {
			$desktop_classes[] = 'et_multi_view_hidden';
		}

		if ( $desktop_classes ) {
			$desktop_attrs .= ' class="' . implode( ' ', array_unique( $desktop_classes ) ) . '"';
		}

		// Render the output.
		if ( $this->is_self_closing_tag( $tag ) ) {
			$output = sprintf(
				'<%1$s%2$s%3$s />',
				et_core_esc_previously( $tag ), // #1
				et_core_esc_previously( $desktop_attrs ), // #2
				et_core_esc_previously(
					$this->render_attrs(
						array(
							'target'         => $contexts['target'],
							'hover_selector' => $contexts['hover_selector'],
							'render_slug'    => $contexts['render_slug'],
						),
						false,
						$data
					)
				) // #3
			);
		} else {
			$output = sprintf(
				'<%1$s%2$s%3$s>%4$s</%1$s>',
				et_core_esc_previously( $tag ), // #1
				et_core_esc_previously( $desktop_attrs ), // #2
				et_core_esc_previously(
					$this->render_attrs(
						array(
							'target'         => $contexts['target'],
							'hover_selector' => $contexts['hover_selector'],
							'render_slug'    => $contexts['render_slug'],
						),
						false,
						$data
					)
				), // #3
				et_core_esc_previously( et_()->array_get( $data, 'content.desktop', '' ) ) // #4
			);
		}

		if ( ! $echo ) {
			return $output;
		}

		echo et_core_esc_previously( $output );
	}

	/**
	 * Get or render the multi content attribute.
	 *
	 * @param array   $contexts {
	 *       Data contexts.
	 *
	 *     @type string|callable $content            Param that will be used to populate the content data.
	 *                                               Callable function as value is allowed and excuted. Example: 'get_the_title' function.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' atribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is assciative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 *
	 *     @type string          $target             HTML element selector target which the element will be modified. Default is empty string.
	 *                                               Dynamic module order class wilcard string is accepted: %%order_class%%
	 *
	 *     @type string          $hover_selector     HTML element selector which trigger the hover event. Default is empty string.
	 *                                               Dynamic module order class wilcard string is accepted: %%order_class%%
	 *
	 *     @type string          $render_slug        Render slug that will be used to calculate the module order class. Default is current module slug.
	 *
	 *     @type array           $custom_props       Defined custom props data.
	 *
	 *     @type array           $conditional_values Defined data sources for data toggle.
	 *
	 *     @type array           $required           List of requireds props key to render the element.
	 *                                               Will returning empty string if any required props is empty.
	 *                                               Default is empty array it will try to gather any props name set in the 'content' context.
	 *                                               Set to false to diable conditional check.
	 * }
	 * @param bool  $echo Whether to print the output instead returning it.
	 * @param array $data Pre populated data in case just need to format the attributes output.
	 * @param bool  $as_array Whether to return the output as array or string.
	 *
	 * @return string|void
	 *
	 * @since 3.27.1
	 */
	public function render_attrs( $contexts = array(), $echo = false, $data = null, $as_array = false ) {
		// Define the array of defaults.
		$defaults = array(
			'content'            => '',
			'attrs'              => array(),
			'styles'             => array(),
			'classes'            => array(),
			'visibility'         => array(),
			'target'             => '',
			'hover_selector'     => '',
			'render_slug'        => '',
			'custom_props'       => array(),
			'conditional_values' => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		if ( $contexts['custom_props'] && is_array( $contexts['custom_props'] ) ) {
			$this->set_custom_props( $contexts['custom_props'] );
		}

		unset( $contexts['custom_props'] );

		if ( $contexts['conditional_values'] && is_array( $contexts['conditional_values'] ) ) {
			$this->set_conditional_values( $contexts['conditional_values'] );
		}

		unset( $contexts['conditional_values'] );

		if ( is_null( $data ) ) {
			$data = $this->populate_data( $contexts );
		}

		if ( $data ) {
			foreach ( $data as $context => $modes ) {
				if ( count( $modes ) === 1 ) {
					unset( $data[ $context ] );
				}
			}
		}

		$output = '';

		if ( $data ) {
			if ( isset( $data['content'] ) ) {
				foreach ( $data['content'] as $mode => $content ) {
					if ( ! $content ) {
						continue;
					}

					$content = str_replace( '&lt;', htmlentities( '&lt;' ), $content );
					$content = str_replace( '&gt;', htmlentities( '&gt;' ), $content );

					$data['content'][ $mode ] = $content;
				}
			}

			$has_content_tablet = isset( $data['content']['tablet'] );
			$has_content_phone  = isset( $data['content']['phone'] );

			$has_visibility_tablet = isset( $data['visibility']['tablet'] );
			$has_visibility_phone  = isset( $data['visibility']['phone'] );

			$data = array(
				'schema' => $data,
				'slug'   => $this->slug,
			);

			if ( ! empty( $contexts['target'] ) ) {
				if ( false !== strpos( $contexts['target'], '%%order_class%%' ) ) {
					$render_slug = ! empty( $contexts['render_slug'] ) ? $contexts['render_slug'] : $this->slug;
					$order_class = ET_Builder_Element::get_module_order_class( $render_slug );

					if ( $order_class ) {
						$data['target'] = str_replace( '%%order_class%%', ".{$order_class}", $contexts['target'] );
					}
				} else {
					$data['target'] = $contexts['target'];
				}
			}

			if ( ! empty( $contexts['hover_selector'] ) ) {
				if ( false !== strpos( $contexts['hover_selector'], '%%order_class%%' ) ) {
					$render_slug = ! empty( $contexts['render_slug'] ) ? $contexts['render_slug'] : $this->slug;
					$order_class = ET_Builder_Element::get_module_order_class( $render_slug );

					if ( $order_class ) {
						$data['hover_selector'] = str_replace( '%%order_class%%', ".{$order_class}", $contexts['hover_selector'] );
					}
				} else {
					$data['hover_selector'] = $contexts['hover_selector'];
				}
			}

			$data_attr_key = esc_attr( $this->data_attr_key );

			if ( $as_array ) {
				$output = array();

				$output[ $data_attr_key ] = esc_attr( wp_json_encode( $data ) );

				if ( $has_content_tablet || $has_visibility_tablet ) {
					$output[ $data_attr_key. '-load-tablet-hidden'] = 'true';
				}

				if ( $has_content_phone || $has_visibility_phone ) {
					$output[ $data_attr_key. '-load-phone-hidden'] = 'true';
				}
			} else {
				// Format the html data attribute output.
				$output = sprintf( ' %1$s="%2$s"', $data_attr_key, esc_attr( wp_json_encode( $data ) ) );
	
				if ( $has_content_tablet || $has_visibility_tablet ) {
					$output .= sprintf( ' %1$s="%2$s"', $data_attr_key . '-load-tablet-hidden', 'true' );
				}
	
				if ( $has_content_phone || $has_visibility_phone ) {
					$output .= sprintf( ' %1$s="%2$s"', $data_attr_key . '-load-phone-hidden', 'true' );
				}
			}
		}

		if ( ! $echo || $as_array ) {
			return $output;
		}

		echo et_core_esc_previously( $output );
	}

	/**
	 * Populate the multi view data.
	 *
	 * @param array $contexts {
	 *     Data contexts.
	 *
	 *     @type string|callable $content            Param that will be used to populate the content data.
	 *                                               Callable function as value is allowed and excuted. Example: 'get_the_title' function.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $attrs              Param that will be used to populate the attributes data.
	 *                                               Associative array key used as attribute name and the value will be used as attribute value.
	 *                                               Special case for 'class' and 'style' atribute name will only generating output for desktop mode.
	 *                                               Use 'styles' or 'classes' context for multi modes usage.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $styles             Param that will be used to populate the inline style attributes data.
	 *                                               Associative array key used as style property name and the value will be used as inline style property value.
	 *                                               Use props name wrapped with 2 curly brakets within the value for find & replace wilcard: {{props_name}}
	 *
	 *     @type array           $classes            Param that will be used to populate the class data.
	 *                                               Associative array key used as class name and the value is assciative array as the conditional check compared with prop value.
	 *                                               The conditional check array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The class will be added if all conditional check is true and will be removed if any of conditional check is false.
	 *
	 *     @type array           $visibility         Param that will be used to populate the visibility data.
	 *                                               Associative array key used as the prop name and the value used as the conditional check compared with prop value.
	 *                                               The element will visible if all conditional check is true and will be hidden if any of conditional check is false.
	 * }
	 *
	 * @return array
	 *
	 * @since 3.27.1
	 */
	public function populate_data( $contexts = array() ) {
		$data = array();

		// Define the array of defaults.
		$defaults = array(
			'content'    => '',
			'attrs'      => array(),
			'styles'     => array(),
			'classes'    => array(),
			'visibility' => array(),
		);

		// Parse incoming $args into an array and merge it with $defaults.
		$contexts = wp_parse_args( $contexts, $defaults );

		foreach ( $contexts as $context => $context_args ) {
			// Skip if the context is not listed as default.
			if ( ( ! isset( $defaults[ $context ] ) ) ) {
				continue;
			}

			$callback = array( $this, "populate_data__{$context}" );

			// Skip if the context has no callback handler.
			if ( ! is_callable( $callback ) ) {
				continue;
			}

			$context_data = call_user_func( $callback, $context_args );

			// Skip if the context data is empty or WP_Error object.
			if ( ! $context_data || is_wp_error( $context_data ) ) {
				continue;
			}

			// Set the context data for each breakpoints.
			foreach ( $context_data as $mode => $context_value ) {
				$data[ $context ][ $mode ] = $context_value;
			}
		}

		return $this->filter_data( $data );
	}

	/**
	 * Populate content data context.
	 *
	 * @since 3.27.1
	 *
	 * @param string|callable $content Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__content( $content ) {
		if ( ! $content || ! is_string( $content ) ) {
			return new WP_Error();
		}

		$data = array();

		if ( preg_match_all( $this->pattern, $content, $matches, PREG_SET_ORDER, 0 ) ) {
			$replacements = array();

			foreach ( $matches as $match ) {
				if ( ! isset( $match[1] ) ) {
					continue;
				}

				$values = $this->get_values( $match[1] );

				if ( $values ) {
					$replacements[ $match[0] ] = array(
						'context' => 'content',
						'name'    => $match[1],
						'values'  => $values,
					);
				}
			}

			if ( $replacements ) {
				foreach ( $replacements as $find => $replacement ) {
					foreach ( $replacement['values'] as $mode => $value ) {
						// Manipulate the value if needed.
						$value = $this->filter_value(
							$value,
							array_merge(
								$replacement,
								array(
									'mode' => $mode,
								)
							)
						);

						if ( ! is_wp_error( $value ) ) {
							if ( ! isset( $data[ $mode ] ) ) {
								$data[ $mode ] = $content;
							}

							$data[ $mode ] = str_replace( $find, $value, $data[ $mode ] );
						}
					}
				}
			}
		} else {
			if ( is_callable( $content ) ) {
				$content = call_user_func( $content );
			}

			// Manipulate the value if needed.
			$value = $this->filter_value(
				$content,
				array(
					'context' => 'content',
					'mode'    => 'desktop',
				)
			);

			if ( ! is_wp_error( $value ) ) {
				// Update the multi content data.
				$data['desktop'] = $value;
			}
		}

		return $data;
	}

	/**
	 * Populate attrs data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $attrs Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__attrs( $attrs ) {
		if ( ! $attrs || ! is_array( $attrs ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( $attrs as $attr_key => $attr_value ) {
			if ( preg_match_all( $this->pattern, $attr_value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$values = $this->get_values( $match[1] );

					if ( $values ) {
						foreach ( $values as $mode => $value ) {
							// Manipulate the value if needed.
							$value = $this->filter_value(
								$value,
								array(
									'context'  => 'attrs',
									'mode'     => $mode,
									'name'     => $match[1],
									'attr_key' => $attr_key,
								)
							);

							if ( ! is_wp_error( $value ) ) {
								$value = et_core_esc_attr( $attr_key, $value );
							}

							if ( ! is_wp_error( $value ) ) {
								if ( ! isset( $data[ $mode ][ $attr_key ] ) ) {
									$data[ $mode ][ $attr_key ] = $attr_value;
								}

								$data[ $mode ][ $attr_key ] = str_replace( $match[0], $value, $data[ $mode ][ $attr_key ] );
							}
						}
					}
				}
			} else {
				// Manipulate the value if needed.
				$attr_value = $this->filter_value(
					$attr_value,
					array(
						'context'  => 'attrs',
						'mode'     => 'desktop',
						'attr_key' => $attr_key,
					)
				);

				if ( ! is_wp_error( $attr_value ) ) {
					$attr_value = et_core_esc_attr( $attr_key, $attr_value );
				}

				if ( ! is_wp_error( $attr_value ) ) {
					// Update the multi content data.
					$data['desktop'][ $attr_key ] = $attr_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Populate styles data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $styles Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__styles( $styles ) {
		if ( ! $styles || ! is_array( $styles ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( $styles as $style_key => $style_value ) {
			if ( preg_match_all( $this->pattern, $style_value, $matches, PREG_SET_ORDER, 0 ) ) {
				foreach ( $matches as $match ) {
					if ( ! isset( $match[1] ) ) {
						continue;
					}

					$values = $this->get_values( $match[1] );

					if ( $values ) {
						foreach ( $values as $mode => $value ) {
							// Manipulate the value if needed.
							$value = $this->filter_value(
								$value,
								array(
									'context'   => 'styles',
									'mode'      => $mode,
									'name'      => $match[1],
									'style_key' => $style_key,
								)
							);

							if ( ! is_wp_error( $value ) ) {
								if ( ! isset( $data[ $mode ][ $style_key ] ) ) {
									$data[ $mode ][ $style_key ] = $style_value;
								}

								$full_style_value = str_replace( $match[0], $value, $data[ $mode ][ $style_key ] );

								if ( ! is_wp_error( et_core_esc_attr( 'style', $style_key . ':' . $full_style_value ) ) ) {
									$data[ $mode ][ $style_key ] = $full_style_value;
								}
							}
						}
					}
				}
			} else {
				// Manipulate the value if needed.
				$style_value = $this->filter_value(
					$style_value,
					array(
						'context'   => 'styles',
						'mode'      => 'desktop',
						'style_key' => $style_key,
					)
				);

				if ( ! is_wp_error( $style_value ) && ! is_wp_error( et_core_esc_attr( 'style', $style_key . ':' . $style_value ) ) ) {
					$data['desktop'][ $style_key ] = $style_value;
				}
			}
		}

		return $data;
	}

	/**
	 * Populate classes data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $classes Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__classes( $classes ) {
		if ( ! $classes || ! is_array( $classes ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( $classes as $class_name => $conditionals ) {
			$results = array();

			foreach ( $conditionals as $name => $value_compare ) {
				$values = $this->get_values( $name );

				if ( ! $values ) {
					continue;
				}

				foreach ( $values as $mode => $value ) {
					if ( isset( $results[ $mode ][ $class_name ] ) && 'remove' === $results[ $mode ][ $class_name ] ) {
						continue;
					}

					// Manipulate the value if needed.
					$value = $this->filter_value(
						$value,
						array(
							'context' => 'classes',
							'mode'    => $mode,
							'name'    => $name,
						)
					);

					if ( ! is_wp_error( $value ) ) {
						$value = et_core_esc_attr( 'class', $value );
					}

					if ( ! is_wp_error( $value ) ) {
						if ( is_array( $value_compare ) ) {
							$match = in_array( $value, $value_compare, true );
						} elseif ( is_callable( $value_compare ) ) {
							$match = call_user_func( $value_compare, $value );
						} elseif ( '__empty' === $value_compare ) {
							$match = empty( $value );
						} elseif ( '__not_empty' === $value_compare ) {
							$match = ! empty( $value );
						} else {
							$match = strval( $value_compare ) === strval( $value );
						}

						$results[ $mode ][ $class_name ] = $match ? 'add' : 'remove';
					}
				}
			}

			// Update the multi content data.
			foreach ( $results as $mode => $classes ) {
				foreach ( $classes as $class_name => $action ) {
					$data[ $mode ][ $action ][] = $class_name;
				}
			}
		}

		return $data;
	}

	/**
	 * Populate visibility data context.
	 *
	 * @since 3.27.1
	 *
	 * @param array $visibility Data contexts.
	 *
	 * @return array
	 */
	protected function populate_data__visibility( $visibility ) {
		if ( ! $visibility || ! is_array( $visibility ) ) {
			return new WP_Error();
		}

		$data = array();

		foreach ( self::get_modes() as $mode ) {
			if ( ! isset( $data[ $mode ] ) ) {
				$data[ $mode ] = array();
			}

			foreach ( $visibility as $name => $value_compare ) {
				$value = $this->get_inherit_value( $name, $mode );

				// Manipulate the value if needed.
				$value = $this->filter_value(
					$value,
					array(
						'context' => 'visibility',
						'mode'    => $mode,
						'name'    => $name,
					)
				);

				if ( ! is_wp_error( $value ) ) {
					$data[ $mode ][ $name ] = self::compare_value( $value, $value_compare ) ? 1 : 0;
				}
			}
		}

		foreach ( $data as $mode => $value ) {
			$data[ $mode ] = count( $value ) === array_sum( $value );
		}

		return $this->distinct_values( $data );
	}

	/**
	 * Props value filter.
	 *
	 * @since 3.27.1
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed|WP_Error return WP_Error to skip the data.
	 */
	protected function filter_value( $raw_value, $args = array() ) {
		if ( $this->module instanceof ET_Builder_Element && method_exists( $this->module, 'multi_view_filter_value' ) && is_callable( array( $this->module, 'multi_view_filter_value' ) ) ) {
			/**
			 * Execute the filter value function defined for current module.
			 *
			 * @since 3.27.1
			 *
			 * @param mixed $raw_value Props raw value.
			 * @param array $args {
			 *     Context data.
			 *
			 *     @type string $context      Context param: content, attrs, visibility, classes.
			 *     @type string $name         Module options props name.
			 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
			 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
			 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
			 * }
			 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Current instance.
			 *
			 * @return mixed
			 */
			$raw_value = call_user_func( array( $this->module, 'multi_view_filter_value' ), $raw_value, $args, $this );

			// Bail eraly if the $raw_value is WP_error object.
			if ( is_wp_error( $raw_value ) ) {
				return $raw_value;
			}
		}

		$context = isset( $args['context'] ) ? $args['context'] : '';
		$name    = isset( $args['name'] ) ? $args['name'] : '';
		$mode    = isset( $args['mode'] ) ? $args['mode'] : 'desktop';

		$content_fields = array(
			'content',
			'raw_content',
			'description',
			'footer_content',
		);

		if ( $name ) {
			// Get conditional value.
			$conditional_value = $this->get_conditional_value( $name, $mode, $args );

			if ( ! is_null( $conditional_value ) ) {
				$raw_value = $conditional_value;
			}

			if ( $raw_value && 'content' === $context && 'desktop' !== $mode && in_array( $name, $content_fields, true ) ) {
				$raw_value = str_replace( array( '%22', '%92', '%91', '%93' ), array( '"', '\\', '&#91;', '&#93;' ), $raw_value );

				// Cleaning up invalid starting <\p> tag.
				$cleaned_value = preg_replace( '/(^<\/p>)(.*)/ius', '$2', $raw_value );

				// Cleaning up invalid ending <p> tag.
				$cleaned_value = preg_replace( '/(.*)(<p>$)/ius', '$1', $cleaned_value );

				// Override the raw value.
				if ( $raw_value !== $cleaned_value ) {
					$raw_value = trim( $cleaned_value, "\n" );

					if ( 'raw_content' !== $name ) {
						$raw_value = force_balance_tags( $raw_value );
					}
				}

				// Try to process shortcode.
				if ( false !== strpos( $raw_value, '&#91;' ) && false !== strpos( $raw_value, '&#93;' ) ) {
					$raw_value = do_shortcode( et_pb_fix_shortcodes( str_replace( array( '&#91;', '&#93;' ), array( '[', ']' ), $raw_value ), true ) );
				}
			}
		}

		$skip = is_string( $raw_value ) || is_numeric( $raw_value ) ? ! strlen( $raw_value ) : empty( $raw_value );

		if ( $skip ) {
			return new WP_Error();
		}

		return $raw_value;
	}

	/**
	 * Filter populated multi view data
	 *
	 * The use case of this method is to manipulate populated data such as injecting srcset attributes.
	 *
	 * @since 3.27.1
	 *
	 * @param array $data All populated raw data. The value value passed to this method has been processed by filter_value method.
	 *
	 * @return array
	 */
	protected function filter_data( $data ) {
		static $defaults = array( false, false, false, false );

		// Inject the image srcset and sizes attributes data.
		if ( ! empty( $data['attrs'] ) && et_is_responsive_images_enabled() ) {
			foreach ( $data['attrs'] as $mode => $attrs ) {
				// Skip if src attr is empty.
				if ( empty( $attrs['src'] ) ) {
					continue;
				}

				$attachment_srcset_sizes = et_get_image_srcset_sizes( $attrs['src'] );

				if ( isset( $attachment_srcset_sizes['srcset'] ) ) {
					$data['attrs'][ $mode ]['srcset'] = $attachment_srcset_sizes['srcset'];
				}

				if ( isset( $attachment_srcset_sizes['sizes'] ) ) {
					$data['attrs'][ $mode ]['sizes'] = $attachment_srcset_sizes['sizes'];
				}

				if ( ! isset( $data['attrs'][ $mode ]['srcset'] ) ) {
					$data['attrs'][ $mode ]['srcset'] = '';
				}

				if ( ! isset( $data['attrs'][ $mode ]['sizes'] ) ) {
					$data['attrs'][ $mode ]['sizes'] = '';
				}
			}
		}

		if ( $this->module instanceof ET_Builder_Element && method_exists( $this->module, 'multi_view_filter_data' ) && is_callable( array( $this->module, 'multi_view_filter_data' ) ) ) {
			/**
			 * Execute the filter data function defined for current module.
			 *
			 * @since 3.27.1
			 *
			 * @param mixed                                     $data       All populated raw data.
			 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Current instance.
			 *
			 * @return mixed
			 */
			$data = call_user_func( array( $this->module, 'multi_view_filter_data' ), $data, $this );
		}

		return $data;
	}

	/**
	 * Normalize values to inject value for all modes
	 *
	 * @since 3.27.1
	 *
	 * @param array $values Raw values.
	 *
	 * @return array Normalized values for all modes.
	 */
	protected function normalize_values( $values = array() ) {
		$normalized = array();

		if ( is_array( $values ) ) {
			if ( ! isset( $values['desktop'] ) ) {
				$values['desktop'] = '';
			}

			if ( ! isset( $values['tablet'] ) ) {
				$values['tablet'] = isset( $values['desktop'] ) ? $values['desktop'] : '';
			}

			if ( ! isset( $values['phone'] ) ) {
				$values['phone'] = isset( $values['tablet'] ) ? $values['tablet'] : ( isset( $values['desktop'] ) ? $values['desktop'] : '' );
			}

			if ( ! isset( $values['hover'] ) ) {
				$values['hover'] = isset( $values['desktop'] ) ? $values['desktop'] : '';
			}

			foreach ( self::get_modes() as $mode ) {
				if ( ! isset( $values[ $mode ] ) ) {
					continue;
				}

				$normalized[ $mode ] = $values[ $mode ];
			}
		} else {
			foreach ( self::get_modes() as $mode ) {
				$normalized[ $mode ] = $values;
			}
		}

		return $normalized;
	}

	/**
	 * Distinct values
	 *
	 * @since 3.27.1
	 *
	 * @param array $values Raw values.
	 *
	 * @return array Distincted values for all modes.
	 */
	public function distinct_values( $values ) {
		$distincted = array();

		// Unset phone mode value if it same with tablet mode value.
		if ( isset( $values['tablet'] ) && isset( $values['phone'] ) && $values['tablet'] === $values['phone'] ) {
			unset( $values['phone'] );
		}

		// Unset tablet mode value if it same with desktop mode value.
		if ( isset( $values['desktop'] ) && isset( $values['tablet'] ) && $values['desktop'] === $values['tablet'] ) {
			unset( $values['tablet'] );
		}

		// Unset hover mode value if it same with desktop mode value.
		if ( isset( $values['desktop'] ) && isset( $values['hover'] ) && $values['desktop'] === $values['hover'] ) {
			unset( $values['hover'] );
		}

		foreach ( self::get_modes() as $mode ) {
			if ( ! isset( $values[ $mode ] ) ) {
				continue;
			}

			$distincted[ $mode ] = $values[ $mode ];
		}

		return $distincted;
	}

	/**
	 * Check wether self closing tag or not
	 *
	 * @since 3.27.1?
	 *
	 * @param string $tag Element tag.
	 *
	 * @return boolean
	 */
	protected function is_self_closing_tag( $tag ) {
		$self_closing_tags = array(
			'area',
			'base',
			'br',
			'col',
			'embed',
			'hr',
			'img',
			'input',
			'link',
			'meta',
			'param',
			'source',
			'track',
			'wbr',
		);

		return in_array( $tag, $self_closing_tags, true );
	}

	/**
	 * Check if required props is fulfilled
	 *
	 * @since 3.27.1?
	 *
	 * @param string $contexts Element contexts data.
	 *
	 * @return bool
	 */
	protected function is_required_props_fulfilled( $contexts ) {
		if ( false === $contexts['required'] ) {
			return true;
		}

		$requireds = ! empty( $contexts['required'] ) ? $contexts['required'] : array();

		if ( $requireds && ! is_array( $requireds ) ) {
			$requireds = array( $requireds );
		}

		if ( ! empty( $contexts['content'] ) && preg_match_all( $this->pattern, $contexts['content'], $matches, PREG_SET_ORDER, 0 ) ) {
			foreach ( $matches as $match ) {
				if ( ! isset( $match[1] ) ) {
					continue;
				}

				$requireds[] = $match[1];
			}
		}

		$fulfilled = true;

		foreach ( $requireds as $required_key => $required_value_compare ) {
			if ( ( ! $required_value_compare && is_numeric( $required_key ) ) || ( ! $required_key && ! is_numeric( $required_key ) ) ) {
				$fulfilled = false;
				break;
			}

			// Handle zero indexed data.
			if ( is_numeric( $required_key ) ) {
				$fulfilled = $this->has_value( $required_value_compare );
			} else {
				$fulfilled = $this->has_value( $required_key, $required_value_compare );
			}

			// Bail early if required props is not fulfilled.
			if ( ! $fulfilled ) {
				break;
			}
		}

		return $fulfilled;
	}

	/**
	 * Sort conditionals values list by number of props and conditionals params.
	 *
	 * @since 3.27.1
	 *
	 * @param array $a Array data to compare.
	 * @param array $b Array data to compare.
	 *
	 * @return array
	 */
	public function sort_conditional_values( $a, $b ) {
		$a_priority = count( $a['props'] ) + count( $a['conditionals'] );
		$b_priority = count( $b['props'] ) + count( $b['conditionals'] );

		if ( $a_priority === $b_priority ) {
			return $a['order'] - $b['order'];
		}

		return ( $a_priority < $b_priority ) ? -1 : 1;
	}

	/**
	 * Gets the Module props.
	 *
	 * The Module is restricted in scope. Hence we use this getter.
	 *
	 * @since 3.29
	 *
	 * @used-by ET_Builder_Module_Woocommerce_Description::multi_view_filter_value()
	 *
	 * @return array
	 */
	public function get_module_props() {
		if ( ! isset( $this->props ) ) {
			return array();
		}

		return $this->props;
	}
}

/**
 * Class ET_Builder_Module_Helper_MultiViewOptions wrapper
 *
 * @since 3.27.1
 *
 * @param ET_Builder_Element $module             Module object.
 * @param array              $custom_props       Defined custom props data.
 * @param array              $conditional_values Defined options conditional values.
 * @param array              $default_values     Defined options default values.
 *
 * @return ET_Builder_Module_Helper_MultiViewOptions
 */
function et_pb_multi_view_options( $module = false, $custom_props = array(), $conditional_values = array(), $default_values = array() ) {
	return new ET_Builder_Module_Helper_MultiViewOptions( $module, $custom_props, $conditional_values, $default_values );
}
