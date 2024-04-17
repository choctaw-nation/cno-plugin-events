<?php
/**
 * The Post Type Builder
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

use ChoctawNation\Events\CPT;

/**
 * Builds the Post Type w/ default ACF fields
 */
class Post_Type_Builder {
	/**
	 * The cpt slug
	 *
	 * @var string $cpt_slug
	 */
	protected string $cpt_slug;

	/**
	 * The CPT Rewrite
	 *
	 * @var string $rewrite
	 */
	protected string $rewrite;

	/**
	 * Die if no ACF, else build the plugin.
	 *
	 * @param string $cpt_slug the Events CPT Slug / ID (defaults to "choctaw-events" for plugin compatibility)
	 * @param string $rewrite the CPT rewrite (defaults to "events" for logical permalinks)
	 */
	public function __construct( string $cpt_slug, string $rewrite ) {
		if ( ! class_exists( 'ACF' ) ) {
			$plugin_error = new \WP_Error( 'Choctaw Events Error', 'ACF not installed!' );
			echo $plugin_error->get_error_messages( 'Choctaw Events Error' );
			die;
		}
		$this->cpt_slug = $cpt_slug;
		$this->rewrite  = $rewrite;
		$this->init_cpt();
		$this->init_acf();
		include_once dirname( __DIR__ ) . '/acf/classes/class-choctaw-event.php';
	}

	/** Inits the CPT */
	private function init_cpt() {
		require_once __DIR__ . '/class-cpt.php';
		$cpt = new CPT( $this->cpt_slug, $this->rewrite );
		add_action( 'init', array( $cpt, 'init' ) );
	}

	/** Inits the ACF Fields */
	private function init_acf() {
		require_once __DIR__ . '/class-custom-fields.php';
		new Custom_Fields();
	}
}
