<?php
/**
 * Tests plugin loader activation and settings initialization.
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use ChoctawNation\Events\Plugin_Loader;
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
	 * Set up test state.
	 */
	public function set_up() {
		parent::set_up();

		delete_option( Plugin_Settings::OPTION_KEY );
		delete_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION );

		$this->loader = new Plugin_Loader();
	}

	/**
	 * Clean up options and globals.
	 */
	public function tear_down() {
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
	 * Redirect check should pass for admins in wp-admin context.
	 */
	public function test_should_redirect_after_activation_for_admin_user() {
		$admin_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);

		wp_set_current_user( $admin_id );
		set_current_screen( 'dashboard' );

		update_option( Plugin_Loader::ACTIVATION_REDIRECT_OPTION, '1', false );

		$this->assertTrue( $this->loader->should_redirect_after_activation() );

		$_GET['activate-multi'] = '1';
		$this->assertFalse( $this->loader->should_redirect_after_activation() );
	}

	/**
	 * Settings page URL should target the plugin page under settings.
	 */
	public function test_settings_page_url_uses_expected_slug() {
		$url = $this->loader->get_settings_page_url();

		$this->assertStringContainsString( 'options-general.php', $url );
		$this->assertStringContainsString( 'page=' . Plugin_Settings::SETTINGS_PAGE_SLUG, $url );
		$this->assertNotFalse( has_action( 'admin_menu', array( $this->loader, 'register_settings_page' ) ) );
	}
}
