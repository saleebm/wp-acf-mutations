<?php

use WPGraphQL\AppContext;
use WPGraphQL\Model\User;

//todo use an array and loop
add_action('graphql_init', static function () {
    register_graphql_object_type('UserSocial', [
        'fields' => [
            'github' => ['type' => 'String'],
            'linkedIn' => ['type' => 'String'],
            'twitter' => ['type' => 'String'],
        ]
    ]);

    register_graphql_field('User', 'socialLinks', [
        'type' => 'UserSocial',
        'description' => __('The social links on the user.', 'copt-dev'),
        'resolve' => function (User $user, array $args, AppContext $context) {
            $github = get_user_meta($user->userId, 'github', true);
            $linkedIn = get_user_meta($user->userId, 'linkedin', true);
            $twitter = get_user_meta($user->userId, 'twitter', true);

            return array(
                'github' => trim($github),
                'linkedIn' => trim($linkedIn),
                'twitter' => trim($twitter),
            );
        }
    ]);
});

add_action('graphql_input_fields', function ($fields, $type_name, $config) {
    if ($type_name === 'UpdateUserInput') {
        $fields = array_merge($fields, [
            'github' => ['type' => 'String'],
            'linkedIn' => ['type' => 'String'],
            'twitter' => ['type' => 'String'],
        ]);
    }

    return $fields;
}, 20, 3);

add_action('graphql_user_object_mutation_update_additional_data', function ($user_id, $input, $mutation_name, $context, $info) {
    if (isset($input['github'])) {
        // Consider other sanitization if necessary and validation such as which
        // user role/capability should be able to insert this value, etc.
        update_user_meta($user_id, 'github', $input['github']);
    }
    if (isset($input['linkedIn'])) {
        // Consider other sanitization if necessary and validation such as which
        // user role/capability should be able to insert this value, etc.
        update_user_meta($user_id, 'linkedin', $input['linkedIn']);
    }
    if (isset($input['twitter'])) {
        // Consider other sanitization if necessary and validation such as which
        // user role/capability should be able to insert this value, etc.
        update_user_meta($user_id, 'twitter', $input['twitter']);
    }
}, 10, 5);

