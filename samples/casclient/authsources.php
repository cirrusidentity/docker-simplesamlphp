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
        'cas' => [
            'slate.enabled' => true,
            'login'    => 'https://slate.idp.cirrusidentity.com/account/cas/login',
            'serviceValidate' => 'https://slate.idp.cirrusidentity.com/account/cas/serviceValidate',
            'logout'   => 'https://slate.idp.cirrusidentity.com/account/cas/logout',
            'attributes' => [
                // These don't map automatically
                'user'  => 'cas:user',
                'authenticationDate'  => 'cas:attributes/cas:authenticationDate',
                'longTermAuthenticationRequestTokenUsed'  => 'cas:attributes/cas:longTermAuthenticationRequestTokenUsed',
                'isFromNewLogin'  => 'cas:attributes/cas:isFromNewLogin',
            ]
        ],
        'ldap' => [],
    ],


);
