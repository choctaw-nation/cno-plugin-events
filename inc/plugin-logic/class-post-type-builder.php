<?php
/**
 * The Post Type Builder
 *
 * @package ChoctawNation
 */

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
			$plugin_error = new WP_Error( 'Choctaw Events Error', 'ACF not installed!' );
			echo $plugin_error->get_error_messages( 'Choctaw Events Error' );
			die;
		}
		add_action( 'admin_notices', array( $this, 'display_dependency_notice' ) );
		$this->cpt_slug = $cpt_slug;
		$this->rewrite  = $rewrite;
		$this->init_cpt();
		$this->init_acf();
		include_once dirname( __DIR__ ) . '/acf/objects/class-choctaw-event.php';
	}

	/** Displays Plugin Dependency notices in the Admin Dashboard */
	public function display_dependency_notice() {
		$is_active = class_exists( 'WPGraphQL' );
		if ( ! $is_active ) {
			echo '<div class="notice notice-error"><p>The Choctaw Events Plugin requires "WPGraphQL" to be active. Activate it to dismiss this notice.</p></div>';
		} else {
			return;
		}
	}
	/** Inits the CPT */
	private function init_cpt() {
		require_once __DIR__ . '/class-events-cpt.php';
		$cpt = new Events_CPT( $this->cpt_slug, $this->rewrite );
		add_action( 'init', array( $cpt, 'init' ) );
	}

	/** Inits the ACF Fields */
	private function init_acf() {
		require_once __DIR__ . '/class-choctaw-events-custom-fields.php';
		new Choctaw_Events_Custom_Fields();
	}
}