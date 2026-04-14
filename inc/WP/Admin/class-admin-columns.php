<?php
/**
 * The Admin Handler
 *
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events\WP\Admin;

/** Handles the WP Hooks & Filters logic */
class Admin_Columns {
	/**
	 * Post type slug
	 *
	 * @var string $post_type_slug
	 */
	private string $post_type_slug;

	/**
	 * Whether to include taxonomies in the admin columns
	 *
	 * @var bool $taxonomies_enabled
	 */
	private bool $taxonomies_enabled;

	/**
	 * Constructor
	 *
	 * @param string $post_type_slug The slug of the CPT to associate ACF fields with.
	 * @param bool   $load_taxonomies Whether to include taxonomies in the admin columns.
	 */
	public function __construct( string $post_type_slug, bool $load_taxonomies ) {
		$this->post_type_slug     = $post_type_slug;
		$this->taxonomies_enabled = $load_taxonomies;
	}

	/** Handles the WordPress Admin Columns Hooks & Filters */
	public function init() {
		// Add ACF Start & End Date to Admin Columns
		add_filter( "manage_edit-{$this->post_type_slug}_columns", array( $this, 'add_acf_to_columns_array' ) );
		add_action( "manage_{$this->post_type_slug}_posts_custom_column", array( $this, 'echo_acf_value_to_column' ), 10, 2 );
		// Make ACF Columns Sortable
		add_filter( "manage_edit-{$this->post_type_slug}_sortable_columns", array( $this, 'declare_sortable_acf_field_column' ) );
		add_action( 'pre_get_posts', array( $this, 'add_acf_to_column_query' ) );
		add_action( 'pre_get_posts', array( $this, 'include_choctaw_events_post_type_in_search' ) );
		if ( $this->taxonomies_enabled ) {
			// Add Category Column to Admin Columns
			add_filter( 'manage_posts_columns', array( $this, 'choctaw_events_admin_column' ) );
			add_filter( 'manage_custom_post_type_columns', array( $this, 'choctaw_events_admin_column' ) );
			add_action( 'manage_posts_custom_column', array( $this, 'choctaw_events_admin_column_data' ), 10, 2 );
			add_action( 'manage_custom_post_type_custom_column', array( $this, 'choctaw_events_admin_column_data' ), 10, 2 );
		}
	}

	/**
	 * Adds a new column to the admin events list table.
	 *
	 * @param array $columns The existing columns in the events list table.
	 * @return array The updated columns with the new 'Category' column.
	 */
	public function choctaw_events_admin_column( array $columns ) {
		$columns[ $this->post_type_slug ] = 'Category';
		return $columns;
	}

	/**
	 * Retrieves the data for the admin column of the Choctaw events custom post type.
	 *
	 * @param string $column The name of the column to retrieve data for.
	 * @param int    $post_id The ID of the post to retrieve data for.
	 */
	public function choctaw_events_admin_column_data( string $column, int $post_id ) {
		if ( '{$this->post_type_slug}' === $column ) {
			$terms = get_the_terms( $post_id, 'choctaw-events-category' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				$term_names = array();
				foreach ( $terms as $term ) {
					$term_names[] = "<a href='edit.php?post_type=choctaw-events&{$term->taxonomy}={$term->slug}'>{$term->name}</a>";
				}
				echo implode( ', ', $term_names );
			} else {
				echo '';
			}
		}
	}

	/**
	 * Adds ACF Start & End Date to Admin Columns
	 *
	 * @param array $columns the Admin Columns
	 */
	public function add_acf_to_columns_array( array $columns ) {
		$columns['start_date'] = 'Event Start Date';
		$columns['end_date']   = 'Event End Date';
		return $columns;
	}

	/**
	 * Displays ACF Start & End Date on Admin Edit screen
	 *
	 * @param string $column the column
	 * @param int    $post_id the post id
	 */
	public function echo_acf_value_to_column( string $column, int $post_id ) {
		$column_keys = array( 'start_date', 'end_date' );
		if ( ! in_array( $column, $column_keys, true ) ) {
			return;
		}
		$value = get_field( $column, $post_id );
		echo $value ? esc_html( $value ) : '';
	}

	/**
	 * Make the ACF Field column sortable
	 *
	 * @param array $columns the columns
	 */
	public function declare_sortable_acf_field_column( array $columns ) {
		$columns['start_date'] = 'start_date';
		$columns['end_date']   = 'end_date';
		return $columns;
	}

	/**
	 * Sort the posts based on the ACF Field column
	 *
	 * @param \WP_Query $query the query
	 */
	public function add_acf_to_column_query( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'start_date' === $orderby ) {
			$query->set( 'meta_key', 'start_date' ); // Replace 'acf_field' with the custom field name
			$query->set( 'orderby', 'meta_value' );
		}

		if ( 'end_date' === $orderby ) {
			$query->set( 'meta_key', 'end_date' ); // Replace 'acf_field' with the custom field name
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Callback Function: Adds Custom Post Type to WP Query
	 *
	 * @param \WP_Query $query the current query
	 */
	public function include_choctaw_events_post_type_in_search( \WP_Query $query ) {
		if ( $query->is_search && ! is_admin() ) {
			$query->set( 'post_type', array( $this->post_type_slug ) );
		}
	}
}