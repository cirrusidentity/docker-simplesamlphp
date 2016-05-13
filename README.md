# SimpleSAMLphp

This image is pre-configured with Apache, php5.6 and SSP.
The behavior of the image can be controlled with 
 * environmental variables
 * mounting custom configuration and certificates
 * enabling SSP modules at run time
 * installing composer modules at run time

These run time configurations options are useful for experimenting and
development. However if you want to distribute a specific
preconfigured image then you should build on top of this image and
perform these customizations in your `Dockerfile`


# Defaults

SSP is installed into the `/var/simplesamlphp` and Apache aliases the
path `/simplesaml` to the SSP's `www` folder. You can adjust the
Apache mapping with `SSP_APACHE_ALIAS` environmental variable. The
default document root is `/var/www`

# Ports Matter - Use a Proxy

Port information is important in SAML metadata. If the metadata says your service is on port 443 then your docker container won't work correctly if its running on port 47651. We recommend using (TODO: add link) `jwilder/nginx-proxy` image simplifies your life. The proxy listens on port 443 and routes traffice to the appropriate SSP image.

## Setup

TODO: give instructions on `nginx-proxy`. TODO: build custom nginx-proxy that is pre-configured with an SSL cert.


# Environmental variables

 * SSP_APACHE_ALIAS The apache path to map to simplesamlphp. Defaults to '/simplesaml'
 * SSP_ENABLED_MODULES The SSP modules that should be enabled. Example: 'cron metarefresh' will enable cron and metarefresh modules
 * APACHE_CERT_NAME The certificate name used for SSL. Apache expects to find `/etc/ssl/certs/${APACHE_CERT_NAME}.pem` and `/etc/ssl/private/${APACHE_CERT_NAME}.key`
 * COMPOSER_REQUIRE Any additional composer modules to install. This should be a space seperated list.


# Sample Usage

## Use Local Config folder

To run SSP against local files, no nginx proxy and listening on port 443

    docker run -d -v -p 443:443 $PWD/ssp/config:/var/simplesamlphp/config cirrus/ssp-base


# Browser access

A lot depends on how you configure SSP, if you are using a proxy, and
what environmental variables you set, but you should be able to access
the install with a URL such as https://192.168.99.100:1660/simplesaml/
where port 1660 is the port picked by Docker to map to 443.

# Build Image

This will build an image called cirrus/ssp-base and tag it with the ssp version 1.13.2

    docker build -t cirrus/ssp-base:1.13.2 .

If you are building the latest version of ssp, then you can tag it with *latest* to make certain things easier in the future.

    docker tag cirrus/ssp-base:1.13.2 cirrus/ssp-base:latest

You can see the images

```
docker images cirrus/ssp
REPOSITORY          TAG                 IMAGE ID            CREATED              VIRTUAL SIZE
cirrus/ssp          1.13.2              97cf0a208322        About a minute ago   535.8 MB
cirrus/ssp          latest              97cf0a208322        About a minute ago   535.8 MB
```

## Adding to Docker Repo

TODO
