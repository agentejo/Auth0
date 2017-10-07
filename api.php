<?php

$this->on('cockpit.api.authenticate', function($data) {

    if ($data['token']) {

        $user = $this->module('auth0')->userinfo($data['token'], [
            'normalize' => true,
            'cache'     => $this->retrieve('config/auth0/cache', false)
        ]);

        if ($user) {
            $data['authenticated'] = true;
            $data['user'] = $user;
        }
    }
});



$app->bind('/api/auth0/loginByAccessToken', function() {

    $token = $this->param('token', null);

    if (!$token) {
        return false;
    }

    $user = $this->module('auth0')->userinfo($token, ['normalize'=>true]);

    if (!$user) {
        return false;
    }

    if ($this->module("cockpit")->hasaccess('cockpit', 'backend', @$user['group'])) {
        $this->module('cockpit')->setUser($user, true);
        return $user;
    }

    return false;
});
