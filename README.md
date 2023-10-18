<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [SimpleSAMLphp](#simplesamlphp)
  - [Defaults](#defaults)
- [Ports Matter - Use a Proxy](#ports-matter---use-a-proxy)
  - [Usage Examples](#usage-examples)
    - [Default Install](#default-install)
    - [Use TLS certificate](#use-tls-certificate)
    - [Use Env variables](#use-env-variables)
    - [Installing and Enable modules at runtime](#installing-and-enable-modules-at-runtime)
    - [Test IdP + Setting Configuration Files](#test-idp--setting-configuration-files)
    - [Test Metadata conversion](#test-metadata-conversion)
    - [Local Module Development](#local-module-development)
    - [Using development branch of SSP](#using-development-branch-of-ssp)
    - [Apache Configuration Overrides](#apache-configuration-overrides)
- [Environmental variables](#environmental-variables)
- [Build Image](#build-image)
  - [Build from a release](#build-from-a-release)
  - [Build from composer/git branch](#build-from-composergit-branch)
  - [Viewing Images](#viewing-images)
  - [Adding to Docker Repo](#adding-to-docker-repo)
- [Using real TLS certificates](#using-real-tls-certificates)
  - [Using dns for localhost](#using-dns-for-localhost)
  - [Generate TLS key and cert](#generate-tls-key-and-cert)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# SimpleSAMLphp

This image is pre-configured with Apache 2.4, php 7.4 and SSP.
The behavior of the image can be controlled with 
 * environmental variables
 * mounting custom configuration and certificates
 * enabling SSP modules at run time
 * installing composer modules at run time

These run time configurations options are useful for experimenting and
development. However if you want to distribute a specific
preconfigured image then you should build on top of this image and
perform these customizations in your `Dockerfile`


## Defaults

SSP is installed into the `/var/simplesamlphp` and Apache aliases the
path `/simplesaml` to the SSP's `public` folder. You can adjust the
Apache mapping with `SSP_APACHE_ALIAS` environmental variable. The
default document root is `/var/www`

A number of php extensions are installed by default to simplify
integrating the image with ldap and databases.

# Ports Matter - Use a Proxy

Port information is important in SAML metadata. If the metadata says your service is on port 443 then your docker container won't work correctly if its running on port 47651. We recommend using [`jwilder/nginx-proxy`](https://hub.docker.com/r/jwilder/nginx-proxy/) image simplifies your life. The proxy listens on port 443 and routes traffice to the appropriate SSP image.

## Usage Examples

### Default Install

You can run SSP

    docker run --name ssp-default -p 443:443 cirrusid/simplesamlphp:v2.0.0

then navigate to https://localhost/simplesaml/ (and accept the certificate) and you can
see the welcome page and navigate to some of the menus. Functionality is limited since
no admin password has been set.

You can view the logs

    docker logs ssp-default

### Use TLS certificate

A wildcard TLS certificate is included for testing. You may use your own (see the `APACHE_CERT_NAME` env variable and `ssp-apache.conf`)
or you can test with the on included.

     docker run --name ssp-default -p 443:443 cirrusid/simplesamlphp:v2.0.0

And visit https://example.local.stack-dev.cirrusidentity.com/simplesaml/ to access your localhost with a valid certificate.
You may use any subdomain, not just example, for your testing. Certificates expire every 90 days so you'll need to
periodically pull new images.

### Use Env variables

A number of settings can be set with `env` variables to allow you to explore functionality.
This example will set the admin account secret and run SSP under `/altinstall/`

```bash
docker run --name ssp-env \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -e SSP_APACHE_ALIAS=altinstall/ \
   -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

The new UI does not take you from the root index directly to the front page, so visit the front page
directly: https://localhost/altinstall/module.php/core/frontpage_welcome.php  You should be able to
click the `Configuration` menu, and then `PHP Info` and authenticate as an admin.

### Installing and Enable modules at runtime

For testing purposes you can install composer dependencies at container start. Some modules also need to be enabled.

```bash
docker run --name ssp-composer \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-modinfo simplesamlphp/simplesamlphp-module-fticks:v1.1.2" \
  -e SSP_ENABLED_MODULES="modinfo metarefresh fticks" \
   -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

This should install and enable `modinfo` which will tell you the
status of installed modules. Visit
`https://localhost/simplesaml/module.php/modinfo/` to see the
list. You should see `fticks` and `metarefresh` are enabled.

For a production image you want your dependencies built into your
image, not installed at runtime. You can set these same variables in
your `Dockerfile` and call the `module-setup.sh` to install.  You can
of course install dependencies anyway you see fit.

### Test IdP + Setting Configuration Files

The image allows you to mount a `config-overide.php` into the `config` to change certain configuration settings,
including enabling idp mode and allowing `exampleauth`
In this example we mount a an override of some configuration options, and the file needed to configure an IdP.
```
docker run --name ssp-idp \
  --mount type=bind,source="$(pwd)/samples/cert",target=/var/simplesamlphp/cert,readonly \
  --mount type=bind,source="$(pwd)/samples/idp/authsources.php",target=/var/simplesamlphp/config/authsources.php,readonly \
  --mount type=bind,source="$(pwd)/samples/idp/config-override.php",target=/var/simplesamlphp/config/config-override.php,readonly \
  --mount type=bind,source="$(pwd)/samples/idp/saml20-idp-hosted.php",target=/var/simplesamlphp/metadata/saml20-idp-hosted.php,readonly \
  --mount type=bind,source="$(pwd)/samples/idp/saml20-sp-remote.php",target=/var/simplesamlphp/metadata/saml20-sp-remote.php,readonly \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -e SSP_APACHE_ALIAS=sample-idp/ \
   -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

You can view the [IdP metadata](https://localhost/sample-idp/module.php/saml/idp/metadata)
and [test authentication](https://localhost/sample-idp/module.php/admin/test/example-userpass). Credentials
are username `student` and password `studentpass`. See the `authsources.php` for how this is configured.

You can view the [admin page](https://localhost/sample-idp/module.php/core/frontpage_config.php)

### Test Metadata conversion

Test metadata conversion with metadata conversion admin web tool.
After running the below docker command,
You can view the [metadata converter page](https://localhost/simplesaml/module.php/admin/federation/metadata-converter)
(password `secret1`)

```
docker run --name ssp-metadata-convert \
   --mount type=bind,source="$(pwd)/samples/idp/authsources.php",target=/var/simplesamlphp/config/authsources.php,readonly \
   -e SSP_ADMIN_PASSWORD=secret1 \
   -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

Metadata refresh module provides a CLI tool that allows you to convert an xml file into SSP's internal format.
If nothing is outputted then the xml file may be invalid.
Unfortunately the script is not informative as to the cause of the error.

```
docker run  \
   -e SSP_ENABLED_MODULES="metarefresh" \
   -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-metarefresh" \
   --mount type=bind,source=$(pwd)/samples/metadata,target=/tmp/metadata,readonly \
   --entrypoint /var/simplesamlphp/modules/metarefresh/bin/metarefresh.php \
   cirrusid/simplesamlphp:v2.0.0 -s  /tmp/metadata/example.xml
```

### Local Module Development

If you are developing a module locally you can mount it into an SSP container to test it. If your module has
additional dependencies the you can't just mount it into the container because your dependencies won't be installed.
Instead mount it into the `staging-modules` folder and the container can add it as a composer dependency and install
it for you.

```
# pretend you are developing this module
git clone https://github.com/simplesamlphp/simplesamlphp-module-modinfo
cd simplesamlphp-module-modinfo/
# Checkout a tag compatible with the SSP version.
git checkout v1.1.1
docker run --name ssp-staging \
  --mount type=bind,source="$(pwd)",target=/var/simplesamlphp/staging-modules/modinfo,readonly \
  -e STAGINGCOMPOSERREPOS=modinfo \
  -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-modinfo:v1.1.1" \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -e SSP_APACHE_ALIAS=sample-staging/ \
  -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

In the output, you should see a line like below, indicating the module was installed.

```
  - Installing simplesamlphp/simplesamlphp-module-modinfo (v1.1.1): Symlinking from /var/simplesamlphp/staging-modules/modinfo
```

We mounted the module under development as a read only file system. This means we need to create a local `enable`
file in our writable file system. Let's make a few edits to the module to show how they are reflected into SSP.

```bash
$ touch enable
$ vim dictionaries/modinfo.definition.json
# Change `Available modules` to some other text, or edit the correct text for your language
```

Now visit https://localhost/sample-staging/module.php/core/frontpage_config.php and you should see you text change
visible.

### Using development branch of SSP

You may want to test a module against the master branch (or other git commit of SSP). Build a version of this image using
that branch (search for SSP_COMPOSER_VERSION in this document).

In this example we test the latest SSP master branch, against a specifc commit of ADFS module

```
docker run --name ssp-master \
   --mount type=bind,source="$(pwd)/samples/cert",target=/var/simplesamlphp/cert,readonly \
  --mount type=bind,source="$(pwd)/samples/adfs/authsources.php",target=/var/simplesamlphp/config/authsources.php,readonly \
  --mount type=bind,source="$(pwd)/samples/adfs/config-override.php",target=/var/simplesamlphp/config/config-override.php,readonly \
  --mount type=bind,source="$(pwd)/samples/adfs/metadata/adfs-idp-hosted.php",target=/var/simplesamlphp/metadata/adfs-idp-hosted.php,readonly \
    --mount type=bind,source="$(pwd)/samples/adfs/metadata/adfs-sp-remote.php",target=/var/simplesamlphp/metadata/adfs-sp-remote.php,readonly \
  -e SSP_ENABLED_MODULES="adfs exampleauth" \
  -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-adfs:dev-master#617e92b37d0889dd623f62821ef1aac0f8431667" \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -p 443:443 cirrusid/simplesamlphp:v2.0.0:composer-dev-master
```

You can view ADFS metadata https://adfs-sample.local.stack-dev.cirrusidentity.com/simplesaml/module.php/adfs/metadata
 or authenticate using WS-FED https://adfs-sample.local.stack-dev.cirrusidentity.com/simplesaml/module.php/adfs/prp?wa=wsignin1.0&wtrealm=urn:federation:localhost&wctx=some-context

### Apache Configuration Overrides

Some modules require additional apache configuration rules to function. In this example we install the `casserver` module.
The convention for CAS is to run at `https://hostname/cas/login`. The apache override file will create that mapping to
the path `/simplesaml/module.php/casserver/login.php`

```bash
docker run --name ssp-casserver \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e COMPOSER_REQUIRE="simplesamlphp/simplesamlphp-module-modinfo simplesamlphp/simplesamlphp-module-casserver" \
  -e SSP_ENABLED_MODULES="modinfo casserver exampleauth" \
  --mount type=bind,source="$(pwd)/samples/casserver/authsources.php",target=/var/simplesamlphp/config/authsources.php,readonly \
  --mount type=bind,source="$(pwd)/samples/casserver/module_casserver.php",target=/var/simplesamlphp/config/module_casserver.php,readonly \
  --mount type=bind,source="$(pwd)/samples/casserver/ssp-override.cf",target=/etc/apache2/sites-enabled/ssp-override.cf,readonly \
   -p 443:443 cirrusid/simplesamlphp:v2.0.0
```

Now perform a [CAS authentication](https://localhost/simplesaml/cas/login?service=http%3A%2F%2Flocalhost%2Fcas-example)
and you should authenticate and then be sent to 404 url with a ticket as a query parameter in the URL.

[Proxy setup is its own README](nginx-proxy/README.md)



# Environmental variables

 * COMPOSER_REQUIRE Any additional composer modules to install. This should be a space seperated list.
 * SSP_ADMIN_PASSWORD The admin password. Defaults to '123' (same as regular SSP). For production you should use a hash or other authsource.
 * SSP_APACHE_ALIAS The apache path to map to simplesamlphp. Defaults to '/simplesaml'
 * SSP_ENABLE_IDP: Set to 'true' to enable an IdP. You'll still need to mount certs and configure an authsource.
 * SSP_ENABLED_MODULES The SSP modules that should be enabled. Example: 'cron metarefresh' will enable cron and metarefresh modules
 * APACHE_CERT_NAME The certificate name used for SSL. Apache expects to find `/etc/ssl/certs/${APACHE_CERT_NAME}.pem` and `/etc/ssl/private/${APACHE_CERT_NAME}.key`
 * SSP_LOG_HANDLER The log handler to use. Defaults to `errorlog`
 * SSP_LOG_LEVEL The log level to use. Must be numeric value. Default is `6` (`INFO`)
    * 3: SimpleSAML_Logger::ERR          No statistics, only errors
    * 4: SimpleSAML_Logger::WARNING      No statistics, only warnings/errors
    * 5: SimpleSAML_Logger::NOTICE       Statistics and errors
    * 6: SimpleSAML_Logger::INFO         Verbose logs
    * 7: SimpleSAML_Logger::DEBUG        Full debug logs - not recommended for production
 * SSP_SECRET_SALT Set a secret salt
 
 

# Build Image

## Build from a release
This will build an image called `cirrusid/simplesamlphp` and tag it. You must edit docker/Dockerfile to set the SSP version and SSP file hash to use

    cd docker
    SSP_IMAGE_TAG=v2.0.0
    docker build --platform linux/amd64  -t cirrusid/simplesamlphp:$SSP_IMAGE_TAG \
        -f Dockerfile .
    docker tag cirrusid/simplesamlphp:$SSP_IMAGE_TAG cirrusid/simplesamlphp:$SSP_IMAGE_TAG.$(date -u +"%Y%m%dT%H%M%S")

If you are building the latest version of ssp, then you can tag it with *latest* to make certain things easier in the future.

    docker tag cirrusid/simplesamlphp:$SSP_IMAGE_TAG cirrusid/simplesamlphp:latest

## Build from composer/git branch

If you want to build from git commit then you can build using a composer to install SSP. This is useful to test
the latest version from git. It does require `npm` to be installed into the resulting image, doubling its size.

    cd docker
    SSP_COMPOSER_VERSION=dev-master
    docker build -t cirrusid/simplesamlphp:composer-${SSP_COMPOSER_VERSION} \
        --build-arg SSP_COMPOSER_VERSION=${SSP_COMPOSER_VERSION} \
        -f Dockerfile .
    docker tag cirrusid/simplesamlphp:composer-${SSP_COMPOSER_VERSION} cirrusid/simplesamlphp:composer-${SSP_COMPOSER_VERSION}.$(date -u +"%Y%m%dT%H%M%S")


## Viewing Images

You can see the images

```
docker images cirrusid/simplesamlphp
REPOSITORY               TAG                      IMAGE ID       CREATED              SIZE
cirrusid/simplesamlphp   composer-dev-master      4c26787e2f1e   About a minute ago   1.13GB
cirrusid/simplesamlphp   1.19.1                   9ed316f77cac   8 minutes ago        607MB
cirrusid/simplesamlphp   1.19.1.20210813T180530   27f0c093717c   2 weeks ago          562MB
cirrusid/simplesamlphp   latest                   27f0c093717c   2 weeks ago          562MB
```

## Adding to Docker Repo

TODO

# Using real TLS certificates

## Using dns for localhost

The dns entry `*.local.stack-dev.cirrusidentity.com` resolves to your
localhost. Using this DNS name allows us to include a real TLS cert
and key in the docker image to ease testing things. An added benefit
is that you can work around restrictions in some software that limit
redirects to localhost.

You can use any subdomain of `local.stack-dev.cirrusidentity.com` in your testing.


## Generate TLS key and cert

This needs to be done every 90 days.

Form https://certbot-dns-route53.readthedocs.io/en/stable/

```bash
# Generate TLS cets
$ docker pull certbot/dns-route53
$ AWS_KEY=secretsecretsecret
$ docker run -it --rm --name certbot --platform linux/amd64 \
  --env AWS_ACCESS_KEY_ID=AKIAYOX74EZ5CVNENIM7 \
  --env AWS_SECRET_ACCESS_KEY=$AWS_KEY \
  -v "$PWD/letsencrypt:/etc/letsencrypt" \
  certbot/dns-route53 certonly -d '*.local.stack-dev.cirrusidentity.com' -m patrick@cirrusidentity.com  --agree-tos  --dns-route53 
# Wait a while and certs should appear in letsencrypt/
# Copy the keys to the docker folder
cp letsencrypt/live/local.stack-dev.cirrusidentity.com/privkey.pem docker/tls/local-stack-dev.key
cp letsencrypt/live/local.stack-dev.cirrusidentity.com/fullchain.pem docker/tls/local-stack-dev.pem

```