<?php
/**
 * Tests plugin loader activation and settings initialization.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use ChoctawNation\Events\CPT;
use ChoctawNation\Events\Plugin_Loader;
use ChoctawNation\Events\WP\Admin\Admin_Screen;
use ChoctawNation\Events\WP\Plugin_Settings;
use WP_UnitTestCase;

/**
 * Class Test_Plugin_Loader
 */
class Test_Plugin_Loader extends WP_UnitTestCase {
	/**
	 * Loader instance under test.
	 *
	 * @var Plugin_Loader
	 */
	private Plugin_Loader $loader;

	/**
	 * Admin screen instance under test.
	 *
	 * @var Admin_Screen
	 */
	private Admin_Screen $admin_screen;

	/**
	 * Created post type slugs for cleanup.
	 *
	 * @var array<int, string>
	 */
	private array $created_post_types = array();

	/**
	 * Set up test state.
	 */
	public function set_up() {
		parent::set_up();

		delete_option( Plugin_Settings::OPTION_KEY );
		delete_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION );

		$this->loader = new Plugin_Loader();

		$this->admin_screen = new Admin_Screen();
	}

	/**
	 * Clean up options and globals.
	 */
	public function tear_down() {
		foreach ( $this->created_post_types as $post_type_slug ) {
			if ( post_type_exists( $post_type_slug ) ) {
				unregister_post_type( $post_type_slug );
			}
		}

		delete_option( Plugin_Settings::OPTION_KEY );
		delete_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION );
		unset( $_GET['activate-multi'] );

		parent::tear_down();
	}

	/**
	 * Defaults should include all required initialization fields.
	 */
	public function test_default_options_include_required_fields() {
		$defaults = Plugin_Settings::get_default_options();

		$this->assertSame( 'event', $defaults['post_type_slug'] );
		$this->assertSame( 'Event', $defaults['post_type_label_single'] );
		$this->assertSame( 'Events', $defaults['post_type_label_plural'] );
		$this->assertTrue( $defaults['has_archive'] );
		$this->assertSame( '', $defaults['archive_slug'] );
	}

	/**
	 * Activation should initialize plugin options and mark redirect.
	 */
	public function test_activate_initializes_options_and_sets_redirect_flag() {
		$this->loader->activate();

		$options = get_option( Plugin_Settings::OPTION_KEY, array() );

		$this->assertIsArray( $options );
		$this->assertSame( 'event', $options['post_type_slug'] );
		$this->assertSame( 'Event', $options['post_type_label_single'] );
		$this->assertSame( 'Events', $options['post_type_label_plural'] );
		$this->assertTrue( $options['has_archive'] );
		$this->assertSame( '', $options['archive_slug'] );

		$this->assertSame( '1', get_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION ) );
	}

	/**
	 * Admin screen redirect check should pass for admins in wp-admin context.
	 */
	public function test_admin_screen_should_redirect_after_activation_for_admin_user() {
		$admin_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);

		wp_set_current_user( $admin_id );
		set_current_screen( 'dashboard' );

		update_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION, '1', false );

		$this->assertTrue( $this->admin_screen->should_redirect_after_activation() );
	}

	/**
	 * Saving a conflicting slug should be blocked and preserve existing slug.
	 */
	public function test_admin_screen_blocks_conflicting_slug_on_save() {
		update_option(
			Plugin_Settings::OPTION_KEY,
			array_merge(
				Plugin_Settings::get_default_options(),
				array(
					'post_type_slug' => 'cno-current-events',
				)
			)
		);

		$sanitized = $this->admin_screen->sanitize_options(
			array(
				'post_type_slug'         => 'post',
				'post_type_label_single' => 'Event',
				'post_type_label_plural' => 'Events',
				'has_archive'            => true,
				'archive_slug'           => 'events',
			)
		);

		$this->assertSame( 'cno-current-events', $sanitized['post_type_slug'] );

		$errors = get_settings_errors( Plugin_Settings::OPTION_KEY );
		$this->assertNotEmpty( $errors );
	}

	/**
	 * Saving a unique slug should be allowed.
	 */
	public function test_admin_screen_allows_non_conflicting_slug_on_save() {
		$new_slug = 'cno-events-' . wp_generate_password( 8, false, false );

		$sanitized = $this->admin_screen->sanitize_options(
			array(
				'post_type_slug'         => $new_slug,
				'post_type_label_single' => 'Event',
				'post_type_label_plural' => 'Events',
				'has_archive'            => true,
				'archive_slug'           => 'events',
			)
		);

		$this->assertSame( $new_slug, $sanitized['post_type_slug'] );
	}

	/**
	 * CPT should be registered when slug is available.
	 */
	public function test_cpt_registers_when_slug_is_available() {
		$post_type_slug = 'cno-evt-' . wp_generate_password( 8, false, false );

		$this->created_post_types[] = $post_type_slug;

		$cpt = new CPT( $post_type_slug, 'events-test' );
		$cpt->init();

		$this->assertTrue( post_type_exists( $post_type_slug ) );
	}

	/**
	 * CPT should not register when slug collides, and warning hook should be added.
	 */
	public function test_cpt_collision_adds_warning_notice_hook() {
		$post_type_slug = 'cno-existing-' . wp_generate_password( 8, false, false );
		register_post_type(
			$post_type_slug,
			array(
				'public' => true,
			)
		);
		$this->created_post_types[] = $post_type_slug;

		$cpt = new CPT( $post_type_slug, 'events-test' );
		$cpt->init();

		$this->assertNotFalse( has_action( 'admin_notices', array( $cpt, 'render_slug_conflict_notice' ) ) );
	}
}
