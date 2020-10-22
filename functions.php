<?php

use WPGraphQL\AppContext;
use WPGraphQL\Model\User;

/**
 * create the acf field for the example
 */
if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array(
		'key' => 'group_5f91f1e6effdf',
		'title' => 'post example',
		'fields' => array(
			array(
				'key' => 'field_5f91f1f099ea0',
				'label' => 'example',
				'name' => 'example',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'show_in_graphql' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'post',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => true,
		'description' => '',
		'show_in_graphql' => 1,
		'graphql_field_name' => 'example',
	));

endif;

/**
 * This enables the additional fields on graphql initialization
 */
add_action( 'graphql_init', static function () {
	// not necessary for ACF field
	// registers an individual object
	register_graphql_object_type( 'UserSocial', [
		'fields' => [
			'github' => [ 'type' => 'String' ],
		]
	] );

	// not necessary for ACF field
	// add the resolve function to the User
	register_graphql_field( 'User', 'personalLinks', [
		'type'        => 'UserSocial',
		'description' => __( 'The social links on the user.', 'copt-dev' ),
		'resolve'     => function ( User $user, array $args, AppContext $context ) {
			$github = get_user_meta( $user->userId, 'github', true );

			return array(
				'github' => trim( $github ),
			);
		}
	] );
} );

// the important part for ACF mutations specifically and also in general for input actions.
// adds the input fields for the ACF fields to add, or just general fields you would want to add.
add_action( 'graphql_input_fields', function ( $fields, $type_name, $config ) {
	//todo also create user input requires this
	if ( $type_name === 'UpdateUserInput' ) {
		$fields = array_merge( $fields, [
			//  add the fields to the input element of the update user input type.
			'github'   => [ 'type' => 'String' ],
			'linkedIn' => [ 'type' => 'String' ],
			'twitter'  => [ 'type' => 'String' ],
		] );
	}
	// todo create and update
	// this type name can be found by looking at what type of input is given in mutations on graphql
	if ( $type_name === 'UpdatePostInput' ) {
		$fields = array_merge( $fields, [
			//  add the fields to the input element of the update user input type.
			// simple string created in ACF
			'example' => [ 'type' => 'String' ],
		] );
	}

	return $fields;
}, 20, 3 );

// what happens when mutation contains any of the input fields when mutating user info, todo check if create works
add_action( 'graphql_user_object_mutation_update_additional_data', function ( $user_id, $input, $mutation_name, $context, $info ) {
	if ( isset( $input['github'] ) ) {
		// Consider other sanitization if necessary and validation such as which
		// user role/capability should be able to insert this value, etc.
		update_user_meta( $user_id, 'github', $input['github'] );
	}
	if ( isset( $input['linkedIn'] ) ) {
		// Consider other sanitization if necessary and validation such as which
		// user role/capability should be able to insert this value, etc.
		update_user_meta( $user_id, 'linkedin', $input['linkedIn'] );
	}
	if ( isset( $input['twitter'] ) ) {
		// Consider other sanitization if necessary and validation such as which
		// user role/capability should be able to insert this value, etc.
		update_user_meta( $user_id, 'twitter', $input['twitter'] );
	}
}, 10, 5 );

//todo
// also do create
add_action( 'graphql_post_object_mutation_update_additional_data', function ( $post_id, $input, $mutation_name, $context, $info ) {
	if ( isset( $input['example'] ) ) {
		// get the field by field name, found when exporting a field created through ACF gui.
		update_field('field_5f91f1f099ea0', $input['example'], $post_id);
	}
}, 10, 5 );

