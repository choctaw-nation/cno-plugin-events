<?php
/**
 * Sample test case.
 * Delete me on init
 *
 * @package ChoctawNation
 */

namespace ChoctawNation\Tests;

use WP_UnitTestCase;

/**
 * Class Test_Sample
 */
class Test_Sample extends WP_UnitTestCase {

	/**
	 * Test something.
	 */
	public function test_plugin_activation_creates_events_cpt() {
		$this->assertTrue( post_type_exists( 'event' ) );
	}
}
