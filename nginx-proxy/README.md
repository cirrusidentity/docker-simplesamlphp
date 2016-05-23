<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [Docker Proxy](#docker-proxy)
  - [SSL](#ssl)
  - [Run Proxy](#run-proxy)
  - [Run a Proxialble Container](#run-a-proxialble-container)
    - [Set VHOST name](#set-vhost-name)
    - [SSL](#ssl-1)
    - [Sample](#sample)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

# Docker Proxy

It can be challenging to run multiple docker images that want to listen on port 80 or 443.
Docker needs each container to use different ports - this can conflict with testing IdPs or SPs locally since the metadata contains port information.
An HTTP proxy can solve this issue: the proxy listens on port 80 and 443 and proxies the connection to our other containers.

See more details and options for the proxy here: https://hub.docker.com/r/jwilder/nginx-proxy/

*remember* On Mac OS X and Windows, docker can't mount your file system - only stuff in /Users/[username]. So if you want to use volumes, make sure they are on that path

## SSL
You should use `https` for your services but this makes it harder to proxy.  Currently we use the nginx proxy with a self-signed cert, and you'll need to accept the browser warnings.

## Run Proxy

Use the below syntax to run the proxy with tls support

```
docker run --name nginx-proxy -d -p 80:80 -p 443:443 -v $PWD/certs:/etc/nginx/certs -v /var/run/docker.sock:/tmp/docker.sock:ro jwilder/nginx-proxy
```

## Run a Proxialble Container

There are a couple of secrets for running the containers that will get
proxied by nginx. These settings should be applied to the proxied
container, and not to the nginx image.

### Set VHOST name

nginx will look at the VIRTUAL_HOST environment variable in your
container to know which host names should be forwared where.  You will
then need to make sure (with /etc/hosts) that the VIRTUAL_HOST name
gets resolved to your docker instance (`docker-machine ip default`).

Note: VIRTUAL_HOST supports multiple values and wildcards. Example: `foo.bar.com,baz.bar.com,bar.com`

### SSL

If you want:

* 80 -> 80 proxying: This is the default
* 443 -> 80 proxying: Add `CERT_NAME=default` as an environmental variabled to your container
* 443 -> 80 proxying: Add `CERT_NAME=default`, `VIRTUAL_PORT=443` and `VIRTUAL_PROTO=https`

`CERT_NAME` maps to the mounted certs from the `certs` directory.

### Sample

This runs an image as the host `sample-sp.ci-local.com` on random
ports. The nginx container detects these ports, and introsepcts the
environmental variables and sees the proxy should listen on 443 and
forward connections to the container. The DNS record
`*.docker.testm.es` resolves to ` 192.168.99.100` (the most common
docker-machine ip).

    docker run -d -P \
      --name sample-sp-proxy \
      -e VIRTUAL_HOST=sample-sp.docker.testm.es \
      -e CERT_NAME=default \
      -e VIRTUAL_PORT=443 \
      -e VIRTUAL_PROTO=https \
      cirrusid/ssp-base:1.14.3


And you can view the logs with

    docker logs -f sample-sp-proxy

And visit the site in your browser at
`https://sample-sp.docker.testm.es/simplesaml`. Currently the proxy
uses a self-signed cert, so you must ignore the warnings.