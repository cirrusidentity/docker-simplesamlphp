<?php

$config = array(

    // This is a authentication source which handles admin authentication.
    'admin' => array(
        // The default is to use core:AdminPassword, but it can be replaced with
        // any authentication source.

        'core:AdminPassword',
    ),

    'example-cas' => [
        'cas:CAS',
        // Enable Slate support (optional)
        'slate.enabled' => true,
        'cas' => [
            'login'    => 'https://slate.idp.cirrusidentity.com/account/cas/login',
            'serviceValidate' => 'https://slate.idp.cirrusidentity.com/account/cas/serviceValidate',
            'logout'   => 'https://slate.idp.cirrusidentity.com/account/cas/logout',
        ],
        'ldap' => [],
    ],


);
