<?php
/**
 *  The ACF Fields
 */

add_action(
	'acf/include_fields',
	function() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			  return;
		}

		acf_add_local_field_group(
			array(
				'key'                   => 'group_6491dd72003a6',
				'title'                 => 'Post Options - Events',
				'fields'                => array(
					array(
						'key'               => 'field_6491dd721319a',
						'label'             => 'Event Info',
						'name'              => 'info',
						'aria-label'        => '',
						'type'              => 'group',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'layout'            => 'block',
						'sub_fields'        => array(
							array(
								'key'               => 'field_6491ddeb1319b',
								'label'             => 'Start Date and Time',
								'name'              => 'start_date_and_time',
								'aria-label'        => '',
								'type'              => 'date_time_picker',
								'instructions'      => '',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'display_format'    => 'm/d/Y g:i a',
								'return_format'     => 'm/d/Y g:i a',
								'first_day'         => 0,
							),
							array(
								'key'               => 'field_6491de1a1319c',
								'label'             => 'End Date and Time',
								'name'              => 'end_date_and_time',
								'aria-label'        => '',
								'type'              => 'date_time_picker',
								'instructions'      => '',
								'required'          => 1,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'display_format'    => 'm/d/Y g:i a',
								'return_format'     => 'm/d/Y g:i a',
								'first_day'         => 0,
							),
							array(
								'key'               => 'field_6491de3e1319d',
								'label'             => 'Event Description',
								'name'              => 'event_description',
								'aria-label'        => '',
								'type'              => 'wysiwyg',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'tabs'              => 'visual',
								'toolbar'           => 'basic',
								'media_upload'      => 0,
								'delay'             => 1,
							),
							array(
								'key'               => 'field_6491de6c1319e',
								'label'             => 'Learn More Button?',
								'name'              => 'has_learn_more_button',
								'aria-label'        => '',
								'type'              => 'true_false',
								'instructions'      => 'Use this to enable an external link displayed as a button',
								'required'          => 0,
								'conditional_logic' => 0,
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'message'           => 'Show Learn More Button',
								'default_value'     => 0,
								'ui'                => 0,
								'ui_on_text'        => '',
								'ui_off_text'       => '',
							),
							array(
								'key'               => 'field_6491de9c1319f',
								'label'             => 'Button Link',
								'name'              => 'button_link',
								'aria-label'        => '',
								'type'              => 'text',
								'instructions'      => 'use https:// for external link, or / for internal link',
								'required'          => 0,
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_6491de6c1319e',
											'operator' => '==',
											'value'    => '1',
										),
									),
								),
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => '',
								'maxlength'         => '',
								'placeholder'       => 'https://...',
								'prepend'           => '',
								'append'            => '',
							),
							array(
								'key'               => 'field_6491dedf131a0',
								'label'             => 'Button Text',
								'name'              => 'button_text',
								'aria-label'        => '',
								'type'              => 'text',
								'instructions'      => '',
								'required'          => 0,
								'conditional_logic' => array(
									array(
										array(
											'field'    => 'field_6491de6c1319e',
											'operator' => '==',
											'value'    => '1',
										),
									),
								),
								'wrapper'           => array(
									'width' => '',
									'class' => '',
									'id'    => '',
								),
								'default_value'     => 'Learn More',
								'maxlength'         => '',
								'placeholder'       => '',
								'prepend'           => '',
								'append'            => '',
							),
						),
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'events',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => array(
					0 => 'the_content',
					1 => 'excerpt',
				),
				'active'                => true,
				'description'           => '',
				'show_in_rest'          => 1,
			)
		);
	}
);