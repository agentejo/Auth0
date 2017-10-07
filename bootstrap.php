<?php


$app->module('auth0')->extend([

    'userinfo' => function($token, $options = []) {

        $options = array_merge([
            'normalize' => false,
            'cache' => false
        ], $options);

        $domain = $this->retrieve('config/auth0/domain', false);
        $info   = $this->app->helper('cache')->read("auth0.user.{$domain}.{$token}", null);

        if (!$info) {

            $ch = curl_init('https://agentejo.eu.auth0.com/userinfo');

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$token}"]);

            $result = curl_exec($ch);

            curl_close($ch);

            if ($result === false) {
                return null;
            }

            $info = json_decode($result, true);
        }

        if ($info && $options['cache']) {
            $this->app->helper('cache')->write("auth0.user.{$domain}.{$token}", $info, $options['cache']);
        }

        if ($info && $options['normalize']) {

            $user = array_merge([
                '_id'   => $info['user_id'],
                'name'  => $info['name'],
                'email' => $info['email'],
                'group' => 'auth0user'
            ], isset($info['user_metadata']['cockpit']) ? $info['user_metadata']['cockpit']:[]);

            $user['auth0'] = $info;

            $info = $user;
        }

        $info['auth0token'] = $token;

        return $info;
    }
]);

if (!$app->retrieve('config/auth0/enabled')) {
    return;
}

// override views
$app->path('cockpit', __DIR__.'/cockpit');


// REST
if (COCKPIT_API_REQUEST) {

    // INIT REST API HANDLER
    include_once(__DIR__.'/api.php');
}

// ADMIN
if (COCKPIT_ADMIN && !COCKPIT_API_REQUEST) {

    include_once(__DIR__.'/admin.php');
}
