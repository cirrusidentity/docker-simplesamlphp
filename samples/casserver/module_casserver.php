<?php
/* 
 * Configuration for the module casserver.
 * 
 */
$config = array(
    'authsource' => 'default-sp',
    'legal_service_urls' => array( //Any service url string matching any of the following prefixes is accepted
        'http://localhost/cas-example',
    ),
    'ticketstore' => array( //defaults to filesystem ticket store using the directory 'ticketcache'
        'class' => 'casserver:FileSystemTicketStore', //Not intended for production
        'directory' => 'ticketcache',
    ),
    'attrname' => 'eduPersonPrincipalName', // 'eduPersonPrincipalName',
    'attributes' => true, // enable transfer of attributes, defaults to false
    'attributes_to_transfer' => array('eduPersonPrincipalName'), // set of attributes to transfer, defaults to all

    'service_ticket_expire_time' => 300, //5 minutes until tickets expire. This 
);