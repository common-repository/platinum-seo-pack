<?php
/**
 * Sitemaps: PSP_Sitemaps_Renderer class
 *
 * Responsible for rendering Sitemaps data to XML in accordance with sitemap protocol.
 *
 * @package WordPress PlatinumSeo
 * @subpackage Sitemaps
 * @since 5.5.0
 */

/**
 * Class PSP_Sitemaps_Renderer
 *
 * @since 5.5.0
 */
class PSP_Sitemaps_Renderer {
	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $stylesheet = '';

	/**
	 * XSL stylesheet for styling a sitemap for web browsers.
	 *
	 * @since 5.5.0
	 *
	 * @var string
	 */
	protected $stylesheet_index = '';

	/**
	 * PSP_Sitemaps_Renderer constructor.
	 *
	 * @since 5.5.0
	 */
	public function __construct() {
		$stylesheet_url = $this->get_sitemap_stylesheet_url();
		if ( $stylesheet_url ) {
			$this->stylesheet = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_url ) . '" ?>';
		}
		$stylesheet_index_url   = $this->get_sitemap_index_stylesheet_url();
		if ( $stylesheet_index_url ) {
			$this->stylesheet_index = '<?xml-stylesheet type="text/xsl" href="' . esc_url( $stylesheet_index_url ) . '" ?>';
		}
	}

	/**
	 * Gets the URL for the sitemap stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string The sitemap stylesheet url.
	 */
	public function get_sitemap_stylesheet_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$sitemap_url = home_url( '/wp-sitemap.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = add_query_arg( 'sitemap-stylesheet', 'sitemap', home_url( '/' ) );
		}

		/**
		 * Filters the URL for the sitemap stylesheet.
		 *
		 * If a falsy value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps xsl file.
		 */
		return apply_filters( 'psp_sitemaps_stylesheet_url', $sitemap_url );
	}

	/**
	 * Gets the URL for the sitemap index stylesheet.
	 *
	 * @since 5.5.0
	 *
	 * @return string The sitemap index stylesheet url.
	 */
	public function get_sitemap_index_stylesheet_url() {
		/* @var WP_Rewrite $wp_rewrite */
		global $wp_rewrite;

		$sitemap_url = home_url( '/wp-sitemap-index.xsl' );

		if ( ! $wp_rewrite->using_permalinks() ) {
			$sitemap_url = add_query_arg( 'sitemap-stylesheet', 'index', home_url( '/' ) );
		}

		/**
		 * Filters the URL for the sitemap index stylesheet.
		 *
		 * If a falsy value is returned, no stylesheet will be used and
		 * the "raw" XML of the sitemap index will be displayed.
		 *
		 * @since 5.5.0
		 *
		 * @param string $sitemap_url Full URL for the sitemaps index xsl file.
		 */
		return apply_filters( 'psp_sitemaps_stylesheet_index_url', $sitemap_url );
	}

	/**
	 * Renders a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 */
	public function render_index( $sitemaps ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();
		
		$index_xml = $this->get_sitemap_index_xml( $sitemaps );					

		if ( ! empty( $index_xml ) ) {
			// All output is escaped within get_sitemap_index_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($index_xml);
            echo $dom->saveXML();
			//echo $index_xml;
		}
	}

	/**
	 * Gets XML for a sitemap index.
	 *
	 * @since 5.5.0
	 *
	 * @param array $sitemaps Array of sitemap URLs.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_index_xml( $sitemaps ) {
		$index_xml = "";
		$index_xml = apply_filters( "psp_sitemaps_get_sitemap_index_xml", $index_xml, $sitemaps );
		
		if (!empty($index_xml) ) {
			
			return $index_xml;
		}
		
		$sitemap_index = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet_index,
				'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $sitemaps as $entry ) {
			$sitemap = $sitemap_index->addChild( 'sitemap' );

			// Add each element as a child node to the <sitemap> entry.
			foreach ( $entry as $name => $value ) {
				if ( 'loc' === $name ) {
					$sitemap->addChild( $name, esc_url( $value ) );
				} elseif ( 'lastmod' === $name ) {
					$sitemap->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						/* translators: %s: list of element names */
						sprintf(
							__( 'Fields other than %s are not currently supported for the sitemap index.', 'core-sitemaps' ),
							implode( ',', array( 'loc', 'lastmod' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $sitemap_index->asXML();
	}

	/**
	 * Renders a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 */
	public function render_sitemap( $url_list, $object_type, $object_subtype ) {
		header( 'Content-type: application/xml; charset=UTF-8' );

		$this->check_for_simple_xml_availability();
		
		$sitemap_xml = $this->get_sitemap_xml( $url_list, $object_type, $object_subtype );		

		if ( ! empty( $sitemap_xml ) ) {
			// All output is escaped within get_sitemap_xml().
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($sitemap_xml);
            echo $dom->saveXML();
			//echo $sitemap_xml;
		}
	}

	/**
	 * Gets XML for a sitemap.
	 *
	 * @since 5.5.0
	 *
	 * @param array $url_list Array of URLs for a sitemap.
	 * @return string|false A well-formed XML string for a sitemap index. False on error.
	 */
	public function get_sitemap_xml( $url_list, $object_type, $object_subtype ) {
		
		$sitemap_xml = "";
		$sitemap_xml = apply_filters( "psp_sitemaps_get_sitemap_xml", $sitemap_xml, $url_list, $object_type, $object_subtype );
		
		if ( !empty($sitemap_xml) ) {
			
			return $sitemap_xml;
		}
		
		$urlset = new SimpleXMLElement(
			sprintf(
				'%1$s%2$s%3$s',
				'<?xml version="1.0" encoding="UTF-8" ?>',
				$this->stylesheet,
				'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" />'
			)
		);

		foreach ( $url_list as $url_item ) {
			$url = $urlset->addChild( 'url' );

			// Add each element as a child node to the <url> entry.
			foreach ( $url_item as $name => $value ) {
				if ( 'loc' === $name ) {
					$url->addChild( $name, esc_url( $value ) );
				} elseif ( in_array( $name, array( 'lastmod', 'changefreq', 'priority' ), true ) ) {
					$url->addChild( $name, esc_xml( $value ) );
				} else {
					_doing_it_wrong(
						__METHOD__,
						/* translators: %s: list of element names */
						sprintf(
							__( 'Fields other than %s are not currently supported for sitemaps.', 'core-sitemaps' ),
							implode( ',', array( 'loc', 'lastmod', 'changefreq', 'priority' ) )
						),
						'5.5.0'
					);
				}
			}
		}

		return $urlset->asXML();
	}

	/**
	 * Checks for the availability of the SimpleXML extension and errors if missing.
	 *
	 * @since 5.5.0
	 */
	private function check_for_simple_xml_availability() {
		if ( ! class_exists( 'SimpleXMLElement' ) ) {
			add_filter(
				'wp_die_handler',
				static function () {
					return '_xml_wp_die_handler';
				}
			);

			wp_die(
				sprintf(
					/* translators: %s: SimpleXML */
					esc_xml( __( 'Could not generate XML sitemap due to missing %s extension', 'core-sitemaps' ) ),
					'SimpleXML'
				),
				esc_xml( __( 'WordPress &rsaquo; Error', 'core-sitemaps' ) ),
				array(
					'response' => 501, // "Not implemented".
				)
			);
		}
	}
}
