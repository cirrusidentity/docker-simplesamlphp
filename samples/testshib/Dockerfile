FROM cirrusid/ssp-base:1.14.8
MAINTAINER Cirrus Identity

# TestShib IDP encrypts the response, so we need a cert
ADD cert /var/simplesamlphp/cert
ADD testshib/authsources.php /var/simplesamlphp/config/authsources.php
ADD saml20-idp-remote.php /var/simplesamlphp/metadata/saml20-idp-remote.php

