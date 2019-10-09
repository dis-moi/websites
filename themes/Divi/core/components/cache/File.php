<?php
/**
 * Core class that implements an file cache.
 *
 * @since 3.27.3
 *
 * The Object Cache stores all of the cache data to file and makes the cache
 * contents available by using a file name as key, which is used to name and later retrieve
 * the cache contents.
 *
 * @package ET\Core\Cache_File
 */
class ET_Core_Cache_File {

	/**
	 * Cached data holder.
	 *
	 * @since 3.27.3
	 *
	 * @var array
	 */
	protected static $_cache = array();

	/**
	 * Loaded cache file data.
	 *
	 * @since 3.27.3
	 *
	 * @var array
	 */
	protected static $_cache_loaded = array();

	/**
	 * Cached data status.
	 *
	 * @since 3.27.3
	 *
	 * @var bool
	 */
	protected static $_dirty = array();

	/**
	 * Sets the data contents into the cache.
	 *
	 * @since 3.27.3
	 *
	 * @param string $cache_name What is the file name that storing the cache data.
	 * @param mixed  $data       The cache data to be set.
	 *
	 * @return void
	 */
	public static function set( $cache_name, $data ) {
		self::$_cache[ $cache_name ] = $data;
		self::$_dirty[ $cache_name ] = $cache_name;
	}

	/**
	 * Retrieves the cache contents, if it exists.
	 *
	 * @since 3.27.3
	 *
	 * @param string $cache_name What is the file name that storing the cache data.
	 *
	 * @return mixed
	 */
	public static function get( $cache_name ) {
		if ( ! isset( self::$_cache_loaded[ $cache_name ] ) ) {
			$file = self::get_cache_file_name( $cache_name );

			if ( is_readable( $file ) ) {
				self::$_cache[ $cache_name ] = unserialize( file_get_contents( $file ) );
			} else {
				self::$_cache[ $cache_name ] = array();
			}

			self::$_cache_loaded[ $cache_name ] = true;
		}

		return isset( self::$_cache[ $cache_name ] ) ? self::$_cache[ $cache_name ] : array();
	}

	/**
	 * Saves Cache.
	 *
	 * @since 3.27.3
	 *
	 * @return void
	 */
	public static function save_cache() {
		if ( ! self::$_dirty || ! self::$_cache ) {
			return;
		}

		foreach ( self::$_dirty as $cache_name ) {
			if ( ! isset( self::$_cache[ $cache_name ] ) ) {
				continue;
			}

			$data = self::$_cache[ $cache_name ];
			$file = self::get_cache_file_name( $cache_name );

			if ( ! is_writable( dirname( $file ) ) ) {
				continue;
			}

			file_put_contents( $file, serialize( $data ) );
		}
	}

	/**
	 * Get full path of cache file name.
	 *
	 * The file name will be suffixed with .data
	 *
	 * @since 3.27.3
	 *
	 * @param string $cache_name What is the file name that storing the cache data.
	 *
	 * @return string
	 */
	public static function get_cache_file_name( $cache_name ) {
		return sprintf( '%1$s/%2$s.data', ET_Core_PageResource::get_cache_directory(), $cache_name );
	}
}

// Hook the shutdown action once.
if ( ! has_action( 'shutdown', 'ET_Core_Cache_File::save_cache' ) ) {
	add_action( 'shutdown', 'ET_Core_Cache_File::save_cache' );
}
