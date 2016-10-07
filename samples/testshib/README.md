<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Testshib](#testshib)
- [Running](#running)
- [Build Image](#build-image)
- [Testing](#testing)
  - [Gotchas](#gotchas)

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
      cirrusid/ssp-base:1.14.8

# Build Image

If you prefer, you can build an image that adds the volumes that were mounted in the above section. The workdir must be the one above this one.

    docker build -t cirrusid/ssp-testshib-sp -f Dockerfile ../.

and then run it with (assuming no HTTPS proxy, and port 443 is available)

    docker run -p 443:443 cirrusid/ssp-testshib-sp

# Testing

You can test the SP authentication source by visiting `https://sample-sp.docker.testm.es/simplesaml/module.php/core/authenticate.php?as=default-sp` or `https://192.168.99.100/simplesaml/module.php/core/authenticate.php?as=default-sp`

You can pick `TestShib Test IdP` to use testshib as your IDP. After successful authentication you'll see the asserted attributes.

## Gotchas

* Sometimes the metadata on testshib gets cleared out. Get you metadata from https://server-name/simplesaml/module.php/saml/sp/metadata.php/default-sp and upload to https://www.testshib.org/register.html
* Sometimes the testshib MD changes. The docker image is not setup to refresh it.