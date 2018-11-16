<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [SimpleSAMLphp](#simplesamlphp)
  - [Defaults](#defaults)
  - [Production](#production)
- [Ports Matter - Use a Proxy](#ports-matter---use-a-proxy)
  - [Usage](#usage)
- [Environmental variables](#environmental-variables)
- [Sample Usage](#sample-usage)
  - [Use Local Config folder](#use-local-config-folder)
- [Browser access](#browser-access)
- [Build Image](#build-image)
  - [Adding to Docker Repo](#adding-to-docker-repo)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# SimpleSAMLphp

This image is pre-configured with Apache 2.4, php 7.2 and SSP.
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

## Usage

[Proxy setup is its own README](nginx-proxy/README.md)


# Environmental variables

 * SSP_ADMIN_PASSWORD The admin password. Defaults to '123' (same as regular SSP). For production you should use a hash or other authsource.
 * SSP_APACHE_ALIAS The apache path to map to simplesamlphp. Defaults to '/simplesaml'
 * SSP_ENABLED_MODULES The SSP modules that should be enabled. Example: 'cron metarefresh' will enable cron and metarefresh modules
 * APACHE_CERT_NAME The certificate name used for SSL. Apache expects to find `/etc/ssl/certs/${APACHE_CERT_NAME}.pem` and `/etc/ssl/private/${APACHE_CERT_NAME}.key`
 * COMPOSER_REQUIRE Any additional composer modules to install. This should be a space seperated list.
 * SSP_LOG_HANDLER The log handler to use. Defaults to `errorlog`
 * SSP_LOG_LEVEL The log level to use. Must be numeric value. Default is `6` (`INFO`)
  * 3: SimpleSAML_Logger::ERR          No statistics, only errors
  * 4: SimpleSAML_Logger::WARNING      No statistics, only warnings/errors
  * 5: SimpleSAML_Logger::NOTICE       Statistics and errors
  * 6: SimpleSAML_Logger::INFO         Verbose logs
  * 7: SimpleSAML_Logger::DEBUG        Full debug logs - not recommended for production

# Sample Usage

There are a number of samples in the samples folder. [samples/testshib/README.md](testshib) is a good starting point.

## Use Local Config folder

To run SSP against local files, no nginx proxy and listening on port 443

    docker run -d -p 443:443 -v $PWD/ssp/config:/var/simplesamlphp/config cirrusid/ssp-base

*note*: the folder used in the above example does not contain and config files. It is just an example of how to set a folder.

# Browser access

A lot depends on how you configure SSP, if you are using a proxy, and
what environmental variables you set, but you should be able to access
the install with a URL such as https://127.0.0.1:1660/simplesaml/
where port 1660 is the port picked by Docker to map to 443. That port will be different for you

# Build Image

This will build an image called cirrusid/ssp-base and tag it with the ssp version 1.16.1

    docker build -t cirrusid/ssp-base:1.16.1 -f ssp-base/Dockerfile .

If you are building the latest version of ssp, then you can tag it with *latest* to make certain things easier in the future.

    docker tag cirrusid/ssp-base:1.16.1 cirrusid/ssp-base:latest

You can see the images

```
docker images cirrusid/ssp
REPOSITORY          TAG                 IMAGE ID            CREATED              VIRTUAL SIZE
cirrusid/ssp          1.13.2              97cf0a208322        About a minute ago   535.8 MB
cirrusid/ssp          latest              97cf0a208322        About a minute ago   535.8 MB
```

## Adding to Docker Repo

TODO
