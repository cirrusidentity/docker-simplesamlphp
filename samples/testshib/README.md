<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Testshib](#testshib)
- [Running](#running)
- [Testing](#testing)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Testshib

This SSP configuration is setup to work with testshib as an SP.

# Running

Run the SSP image with the given certs and remote idp file.
Environmental variables are defined in `env.list`

    docker run -d -P  --name sample-sp-proxy \
      --env-file env.list \
      -v $PWD/../cert:/var/simplesamlphp/cert \
      -v $PWD/authsources.php:/var/simplesamlphp/config/authsources.php \
      -v $PWD/../saml20-idp-remote.php:/var/simplesamlphp/metadata/saml20-idp-remote.php \
      cirrus/ssp-base:1.14.3

# Testing

You can test the SP authentication source by visiting `https://sample-sp.docker.testm.es/simplesaml/module.php/core/authenticate.php?as=default-sp`.
You can pick `TestShib Test IdP` to use testshib as your IDP. After successful authentication you'll see the asserted attributes.