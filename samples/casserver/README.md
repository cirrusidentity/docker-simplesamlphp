* Out of date content*

# CAS Server

This SSP configuration is setup to be a CAS server. Users are authenticated from a SAML IdP and the assertions are converted into CAS

# Running

Run the SSP image with the given certs and remote idp file.
Most environmental variables are defined in `env.list`.
`SSP_DELETE_MODULES` is used to remove pre-installed modules. `COMPOSER_REQUIRE` is used to install a newer version of the `casserver` module.
We use a custom `apache.conf` to add better rewrite rules for CAS.

    docker run -d -P  --name sample-casserver-proxy \
      --env-file env.list \
      -e SSP_APACHE_ALIAS=/ \
      -e SSP_DELETE_MODULES=casserver \
      -e SSP_ENABLED_MODULES=casserver \
      -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-casserver:dev-master" \
      -v $PWD/../cert:/var/simplesamlphp/cert \
      -v $PWD/ssp-apache.conf://etc/apache2/sites-available/ssp.conf \
      -v $PWD/authsources.php:/var/simplesamlphp/config/authsources.php \
      -v $PWD/module_casserver.php:/var/simplesamlphp/config/module_casserver.php \
      -v $PWD/../saml20-idp-remote.php:/var/simplesamlphp/metadata/saml20-idp-remote.php \
      cirrusid/ssp-base:1.14.3

# Testing

There seems to be some issues with the standalone casserver module. It appears to have been written against an older SSP interface, and errors with `SimpleSAML_Session::getInstance()`