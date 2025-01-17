<?php
/**
 * Sitemaps: PSP_Sitemaps_Index class.
 *
 * Generates the sitemap index.
 *
 * @package WordPress PlatinumSeo
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class PSP_Sitemaps_Index.
 * Builds the sitemap index page that lists the links to all of the sitemaps.
 *
 * @since 5.5.0
 */
class PSP_Sitemaps_Index {

	/**
	 * The main registry of supported sitemaps.
	 *
	 * @since 5.5.0
	 * @var PSP_Sitemaps_Registry
	 */
	protected $registry;

	/**
	 * PSP_Sitemaps_Index constructor.
	 *
	 * @since 5.5.0
	 *
	 * @param PSP_Sitemaps_Registry $registry Sitemap provider registry.
	 */
	public function __construct( PSP_Sitemaps_Registry $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Gets a sitemap list for the index.
	 *
	 * @since 5.5.0
	 *
	 * @return array List of all sitemaps.
	 */
	public function get_sitemap_list() {
		$sitemaps = array();

		$providers = $this->registry->get_sitemaps();
		/* @var PSP_Sitemaps_Provider $provider */
		foreach ( $providers as $provider ) {
			$sitemap_entries = $provider->get_sitemap_entries();

			// Prevent issues with array_push and empty arrays on PHP < 7.3.
			if ( ! $sitemap_entries ) {
				continue;
			}

			// Using array_push is more efficient than array_merge in a loop.
			array_push( $sitemaps, ...$sitemap_entries );
		}

		return $sitemaps;
	}

	/**
	 * Builds the URL for the sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @return string The sitemap index url.
	 */
	public function get_index_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$url = home_url( '/wp-sitemap.xml' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$url = add_query_arg( 'sitemap', 'index', home_url( '/' ) );
		}

		return $url;
	}
}
