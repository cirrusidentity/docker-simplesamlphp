<?php

$metadata['urn:federation:localhost'] = [
    'prp' => 'https://localhost/adfs/ls/',
    'simplesaml.nameidattribute' => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
// Some WS-Fed relying parties applications set the session lifetime to the assertion lifetime
// 'assertion.lifetime' => 3600,
    'authproc' => [
        50 => [
            'class' => 'core:AttributeLimit',
            'cn',
            'mail',
            'uid',
            'eduPersonAffiliation',
        ],
    ],
];
