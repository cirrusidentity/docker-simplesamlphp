<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [SimpleSAMLphp](#simplesamlphp)
  - [Defaults](#defaults)
  - [Production](#production)
- [Ports Matter - Use a Proxy](#ports-matter---use-a-proxy)
  - [Usage Examples](#usage-examples)
    - [Default Install](#default-install)
    - [Use Env variables](#use-env-variables)
    - [Installing and Enable modules at runtime](#installing-and-enable-modules-at-runtime)
    - [Test IdP + Setting Configuration Files](#test-idp--setting-configuration-files)
    - [Local Module Development](#local-module-development)
- [Environmental variables](#environmental-variables)
- [Build Image](#build-image)
  - [Adding to Docker Repo](#adding-to-docker-repo)

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
path `/simplesaml` to the SSP's `www` folder. You can adjust the
Apache mapping with `SSP_APACHE_ALIAS` environmental variable. The
default document root is `/var/www`

## Production

We'll be iterating on this image and making breaking changes. Currently this image is useful for testing/learning SSP and testing custom modules.

# Ports Matter - Use a Proxy

Port information is important in SAML metadata. If the metadata says your service is on port 443 then your docker container won't work correctly if its running on port 47651. We recommend using [`jwilder/nginx-proxy`](https://hub.docker.com/r/jwilder/nginx-proxy/) image simplifies your life. The proxy listens on port 443 and routes traffice to the appropriate SSP image.

## Usage Examples

### Default Install

You can run SSP

    docker run --name ssp-default -p 443:443 cirrusid/simplesamlphp

then navigate to https://localhost/simplesaml/ (and accept the certificate) and you can
see the welcome page and navigate to some of the menus. Functionality is limited since
no admin password has been set.

You can view the logs

    docker logs ssp-default

### Use Env variables

A number of settings can be set with `env` variables to allow you to explore functionality.
This example will set the admin account secret, use the new UI and run SSP under `/altinstall/`

```bash
docker run --name ssp-env \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -e SSP_NEW_UI=true \
  -e SSP_APACHE_ALIAS=altinstall/ \
   -p 443:443 cirrusid/simplesamlphp
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
   -p 443:443 cirrusid/simplesamlphp
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
  --mount type=bind,source="$(pwd)/samples/idp/saml2-idp-hosted.php",target=/var/simplesamlphp/metadata/saml2-idp-hosted.php,readonly \
  -e SSP_ADMIN_PASSWORD=secret1 \
  -e SSP_SECRET_SALT=mysalt \
  -e SSP_APACHE_ALIAS=sample-idp/ \
   -p 443:443 cirrusid/simplesamlphp
```

You can view the [IdP metadata](https://localhost/sample-idp/saml2/idp/metadata.php?output=xhtml)
and [test authentication](https://localhost/sample-idp/module.php/core/authenticate.php?as=example-userpass). Credentials
are username `student` and password `studentpass`. See the `authsources.php` for how this is configured.

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
  -p 443:443 cirrusid/simplesamlphp
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
 * SSP_NEW_UI Set to true to enable the new twig UI
 * SSP_SECRET_SALT Set a secret salt
 
 

# Build Image

This will build an image called `cirrusid/simplesamlphp` and tag it with the ssp version `1`.19.0`

    cd docker
    SSPV=1.19.0
    docker build -t cirrusid/simplesamlphp:$SSPV -f Dockerfile .

If you are building the latest version of ssp, then you can tag it with *latest* to make certain things easier in the future.

    docker tag cirrusid/simplesamlphp:$SSPV cirrusid/simplesamlphp:latest

You can see the images

```
docker images cirrusid/simplesamlphp
REPOSITORY          TAG                 IMAGE ID            CREATED              VIRTUAL SIZE
cirrusid/ssp          1.13.2              97cf0a208322        About a minute ago   535.8 MB
cirrusid/ssp          latest              97cf0a208322        About a minute ago   535.8 MB
```

## Adding to Docker Repo

TODO
