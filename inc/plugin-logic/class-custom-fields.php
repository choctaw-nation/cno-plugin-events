<?php
/**
 * Handles the Loading and setting of the ACF fields for the plugin
 *
 * @since 1.0
 * @package ChoctawNation
 * @subpackage Events
 */

namespace ChoctawNation\Events;

/**
 * Wrapper for ACF Default fields
 */
class Custom_Fields {
	/**
	 *  The Event Details SubFields
	 *
	 * @var array
	 */
	protected array $event_details_fields = array(
		array(
			'key'                => 'field_65087ba7ad64d',
			'label'              => 'Event Description',
			'name'               => 'event_description',
			'aria-label'         => '',
			'type'               => 'wysiwyg',
			'instructions'       => '',
			'required'           => 1,
			'conditional_logic'  => 0,
			'wrapper'            => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'relevanssi_exclude' => 0,
			'default_value'      => '',
			'tabs'               => 'all',
			'toolbar'            => 'full',
			'media_upload'       => 1,
			'show_in_rest'       => 1,
			'delay'              => 0,
		),
		array(
			'key'                => 'field_65087ce9ad653',
			'label'              => 'Time & Date',
			'name'               => 'time_and_date',
			'aria-label'         => '',
			'type'               => 'group',
			'instructions'       => '',
			'required'           => 0,
			'conditional_logic'  => 0,
			'wrapper'            => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'relevanssi_exclude' => 0,
			'layout'             => 'row',
			'sub_fields'         => array(
				array(
					'key'                => 'field_6509d3c6f34b9',
					'label'              => 'Start Date',
					'name'               => 'start_date',
					'aria-label'         => '',
					'type'               => 'date_picker',
					'instructions'       => '',
					'required'           => 1,
					'conditional_logic'  => 0,
					'wrapper'            => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'relevanssi_exclude' => 0,
					'display_format'     => 'm/d/Y',
					'return_format'      => 'm/d/Y',
					'first_day'          => 0,
					'show_in_rest'       => 1,
				),
				array(
					'key'                => 'field_6509d3f3f34ba',
					'label'              => 'Start Time',
					'name'               => 'start_time',
					'aria-label'         => '',
					'type'               => 'time_picker',
					'instructions'       => '',
					'required'           => 0,
					'conditional_logic'  => array(
						array(
							array(
								'field'    => 'field_6509d406f34bb',
								'operator' => '!=',
								'value'    => '1',
							),
						),
					),
					'wrapper'            => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'relevanssi_exclude' => 0,
					'display_format'     => 'g:i a',
					'return_format'      => 'g:i a',
					'show_in_rest'       => 1,
				),
				array(
					'key'                => 'field_6509d452f34bd',
					'label'              => 'End Date',
					'name'               => 'end_date',
					'aria-label'         => '',
					'type'               => 'date_picker',
					'instructions'       => '',
					'required'           => 0,
					'conditional_logic'  => 0,
					'wrapper'            => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'relevanssi_exclude' => 0,
					'display_format'     => 'm/d/Y',
					'return_format'      => 'm/d/Y',
					'first_day'          => 0,
					'show_in_rest'       => 1,
				),
				array(
					'key'                => 'field_6509d451f34bc',
					'label'              => 'End Time',
					'name'               => 'end_time',
					'aria-label'         => '',
					'type'               => 'time_picker',
					'instructions'       => '',
					'required'           => 0,
					'conditional_logic'  => array(
						array(
							array(
								'field'    => 'field_6509d406f34bb',
								'operator' => '!=',
								'value'    => '1',
							),
						),
					),
					'wrapper'            => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'relevanssi_exclude' => 0,
					'display_format'     => 'g:i a',
					'return_format'      => 'g:i a',
					'show_in_rest'       => 1,
				),
				array(
					'key'                => 'field_6509d406f34bb',
					'label'              => 'All Day',
					'name'               => 'is_all_day',
					'aria-label'         => '',
					'type'               => 'true_false',
					'instructions'       => '',
					'required'           => 0,
					'conditional_logic'  => 0,
					'wrapper'            => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'relevanssi_exclude' => 0,
					'message'            => 'All Day Event',
					'default_value'      => 0,
					'ui'                 => 0,
					'ui_on_text'         => '',
					'ui_off_text'        => '',
					'show_in_rest'       => 1,
				),
			),
		),
		array(
			'key'                => 'field_65087f67ad654',
			'label'              => 'Event Website',
			'name'               => 'event_website',
			'aria-label'         => '',
			'type'               => 'url',
			'instructions'       => '',
			'required'           => 0,
			'conditional_logic'  => 0,
			'wrapper'            => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'relevanssi_exclude' => 0,
			'default_value'      => '',
			'placeholder'        => '',
			'show_in_rest'       => 0,
		),
		array(
			'key'                => 'field_6543a3915e1c0',
			'label'              => 'Brief Description',
			'name'               => 'archive_content',
			'aria-label'         => '',
			'type'               => 'textarea',
			'instructions'       => 'Character limit 155. New lines ignored.',
			'required'           => 0,
			'conditional_logic'  => 0,
			'wrapper'            => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'      => '',
			'maxlength'          => 155,
			'rows'               => '',
			'placeholder'        => '',
			'relevanssi_exclude' => 0,
			'show_in_rest'       => 0,
		),
	);

	/** Constructor */
	public function __construct() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}
		add_action( 'acf/include_fields', array( $this, 'init_default_fields' ) );
	}

	/** Default Post Type Fields */
	public function init_default_fields() {
		acf_add_local_field_group(
			array(
				'key'                                   => 'group_65087b74b18b8',
				'title'                                 => 'Post Type — Choctaw Events',
				'fields'                                => array(
					array(
						'key'                => 'field_65087b75ad64c',
						'label'              => 'Event Details',
						'name'               => 'event_details',
						'aria-label'         => '',
						'type'               => 'group',
						'instructions'       => '',
						'required'           => 0,
						'conditional_logic'  => 0,
						'wrapper'            => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'relevanssi_exclude' => 0,
						'layout'             => 'block',
						'sub_fields'         => $this->event_details_fields,
					),
				),
				'location'                              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'choctaw-events',
						),
					),
				),
				'menu_order'                            => 0,
				'position'                              => 'normal',
				'style'                                 => 'default',
				'label_placement'                       => 'top',
				'instruction_placement'                 => 'label',
				'hide_on_screen'                        => '',
				'active'                                => true,
				'description'                           => '',
				'show_in_rest'                          => 1,
				'show_in_graphql'                       => 1,
				'graphql_field_name'                    => 'choctawEventsDetails',
				'map_graphql_types_from_location_rules' => 0,
				'graphql_types'                         => '',
			)
		);
	}
}
