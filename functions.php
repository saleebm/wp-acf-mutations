<?php

use WPGraphQL\AppContext;
use WPGraphQL\Model\User;

/**
 * This enables the additional fields on graphql initialization
 */
add_action( 'graphql_init', static function () {
	// registers an individual object
	register_graphql_object_type( 'UserSocial', [
		'fields' => [
			'github'   => [ 'type' => 'String' ],
		]
	] );

	register_graphql_field( 'User', 'personalLinks', [
		'type'        => 'UserSocial',
		'description' => __( 'The social links on the user.', 'copt-dev' ),
		'resolve'     => function ( User $user, array $args, AppContext $context ) {
			graphql_debug( $user );
			$github   = get_user_meta( $user->userId, 'github', true );

			return array(
				'github'   => trim( $github ),
			);
		}
	] );
} );

add_action( 'graphql_input_fields', function ( $fields, $type_name, $config ) {
	if ( $type_name === 'UpdateUserInput' ) {
		$fields = array_merge( $fields, [
			//  add the fields to the input element of the update user input type.
			'github'   => [ 'type' => 'String' ],
			'linkedIn' => [ 'type' => 'String' ],
			'twitter'  => [ 'type' => 'String' ],
		] );
	}

	return $fields;
}, 20, 3 );

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

