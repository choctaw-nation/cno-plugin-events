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
			'key'               => 'field_69de59e2f88fd',
			'label'             => 'Date & Time',
			'name'              => '',
			'aria-label'        => '',
			'type'              => 'tab',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'placement'         => 'top',
			'endpoint'          => 0,
			'selected'          => 0,
		),
		array(
			'key'               => 'field_69de59212323c',
			'label'             => 'All Day Event',
			'name'              => 'is_all_day',
			'aria-label'        => '',
			'type'              => 'true_false',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'message'           => 'All Day Event',
			'default_value'     => 0,
			'allow_in_bindings' => 0,
			'ui'                => 0,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		),
		array(
			'key'                     => 'field_69de58a56589b',
			'label'                   => 'Start Date',
			'name'                    => 'start_date',
			'aria-label'              => '',
			'type'                    => 'date_picker',
			'instructions'            => '',
			'required'                => 1,
			'conditional_logic'       => 0,
			'wrapper'                 => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'display_format'          => 'm/d/Y',
			'return_format'           => 'F j, Y',
			'first_day'               => 0,
			'default_to_current_date' => 0,
			'allow_in_bindings'       => 1,
		),
		array(
			'key'                     => 'field_69de58ca2323b',
			'label'                   => 'End Date',
			'name'                    => 'end_date',
			'aria-label'              => '',
			'type'                    => 'date_picker',
			'instructions'            => '',
			'required'                => 0,
			'conditional_logic'       => 0,
			'wrapper'                 => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'display_format'          => 'm/d/Y',
			'return_format'           => 'F j, Y',
			'first_day'               => 0,
			'default_to_current_date' => 0,
			'allow_in_bindings'       => 1,
		),
		array(
			'key'               => 'field_69de58a56589f',
			'label'             => 'Start Time',
			'name'              => 'start_time',
			'aria-label'        => '',
			'type'              => 'time_picker',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_69de59212323c',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'display_format'    => 'g:i a',
			'return_format'     => 'g:i a',
			'allow_in_bindings' => 1,
		),
		array(
			'key'               => 'field_69de58a5658a2',
			'label'             => 'End Time',
			'name'              => 'end_time',
			'aria-label'        => '',
			'type'              => 'time_picker',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_69de59212323c',
						'operator' => '!=',
						'value'    => '1',
					),
				),
			),
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'display_format'    => 'g:i a',
			'return_format'     => 'g:i a',
			'allow_in_bindings' => 1,
		),
		array(
			'key'               => 'field_69de59f5f88fe',
			'label'             => 'Event Details',
			'name'              => '',
			'aria-label'        => '',
			'type'              => 'tab',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'placement'         => 'top',
			'endpoint'          => 0,
			'selected'          => 0,
		),
		array(
			'key'               => 'field_69de58a5658a8',
			'label'             => 'Brief Event Description',
			'name'              => 'brief_description',
			'aria-label'        => '',
			'type'              => 'textarea',
			'instructions'      => 'Please provide a brief description of the event, max 250 characters. New lines ignored.',
			'required'          => 1,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'maxlength'         => 250,
			'allow_in_bindings' => 1,
			'rows'              => '',
			'placeholder'       => '',
			'new_lines'         => '',
		),
		array(
			'key'               => 'field_69de58a5658aa',
			'label'             => 'Event Website',
			'name'              => 'event_website',
			'aria-label'        => '',
			'type'              => 'url',
			'instructions'      => 'Provide a link to the event registration',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'allow_in_bindings' => 1,
			'placeholder'       => '',
		),
	);

	/**
	 * Post Type Slug (used for ACF field group location rules)
	 *
	 * @var string $post_type_slug
	 */
	private string $post_type_slug;

	/**
	 * Constructor
	 *
	 * @param string $post_type_slug the Events CPT Slug / ID (defaults to "choctaw-events" for plugin compatibility)
	 */
	public function __construct( $post_type_slug ) {
		$this->post_type_slug = $post_type_slug;
	}


	/** Default Post Type Fields */
	public function init_default_fields() {
		acf_add_local_field_group(
			array(
				'key'                   => 'group_65087b74b18b8',
				'title'                 => 'Post Type — Choctaw Events',
				'fields'                => $this->event_details_fields,
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => $this->post_type_slug,
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => 'Custom fields for the Choctaw Events post type.',
				'show_in_rest'          => 1,
			)
		);
	}
}
