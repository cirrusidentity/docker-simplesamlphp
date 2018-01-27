FROM php:5.6-apache
RUN apt-get update -y
# Install the various packages needed by ssp and composer
RUN apt-get install -y \
  file \
  libgmp-dev \
  libmcrypt-dev \
  libmhash-dev \
  re2c \
  zlib1g-dev

# Enable php extensions
# gmp: ?
# mcrypt: need for decrypting encrypted assertions
# mbstring: need in various comparison operations. `simplesamlphp/composer-module-installer` won't work without it (reports "ssp-mixedcase-module-name" issues - newer version of composer include polyfill-mbstring which fixes this)
# zip: needed by composer
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/local/include/ \
  && docker-php-ext-configure gmp \
  && docker-php-ext-install gmp \
  && docker-php-ext-configure mcrypt \
  && docker-php-ext-install mcrypt \
  && docker-php-ext-configure mbstring \
  && docker-php-ext-install mbstring \
  && docker-php-ext-configure zip \
  && docker-php-ext-install zip


# Download and enable composer
ENV COMPOSER_HASH=c0a5519c768ef854913206d45bd360efc2eb4a3e6eb1e1c7d0a4b5e0d3bbb31f \
    COMPOSER_VERSION=1.5.2

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar \
  &&  echo "$COMPOSER_HASH /usr/local/bin/composer" | sha256sum -c - \
  && chmod u+x  /usr/local/bin/composer

# TODO: Have composer do a pre-fetch of packagist so future build are faster?

ENV SSP_ADMIN_PASSWORD=123 \
    SSP_DIR=/var/simplesamlphp \
    SSP_HASH=436e73170732929715cf59a0f472f591c0f791b26e5ff02909d4c5113a8c9308 \
    SSP_LOG_HANDLER=errorlog \
    SSP_LOG_LEVEL=6 \
    SSP_VERSION=1.15.1
    
RUN mkdir $SSP_DIR \
  && curl -L -o /tmp/ssp.tar.gz https://github.com/simplesamlphp/simplesamlphp/releases/download/v$SSP_VERSION/simplesamlphp-$SSP_VERSION.tar.gz \
  &&  echo "$SSP_HASH  /tmp/ssp.tar.gz" | sha256sum -c - \
  && tar xvzf /tmp/ssp.tar.gz --strip-components 1 -C $SSP_DIR

# make any additional dirs required by common modules
RUN mkdir $SSP_DIR/data \
  && chown -R www-data $SSP_DIR \
  && mkdir $SSP_DIR/ticketcache \
  && chown -R www-data $SSP_DIR/ticketcache

# Add default index page 
RUN echo "You should access SSP on https.<p/>If you are using the nginx docker proxy make sure to set 'VIRTUAL_PORT=443' and 'VIRTUAL_PROTO=https'" > /var/www/html/index.html \
    && echo 'By default SSP is on <a href="/simplesaml">/simplesaml</a>' > /var/www/index.html

# Enable Apache modules
RUN a2enmod ssl rewrite
EXPOSE 443
# Default certs
RUN apt-get install ssl-cert
# Default basepath in apache config
ENV APACHE_CERT_NAME=ssl-cert-snakeoil \
    SSP_APACHE_ALIAS=simplesaml
# Sites must end with .conf
ADD ssp-apache.conf /etc/apache2/sites-available/ssp.conf
RUN a2ensite ssp


# Customize php.ini??


# Customize SSP files for better docker compatability
# TODO: update the default config.php to just include an override file
RUN sed -i.bak \
    -e "s|^.*'baseurlpath'.*=>.*$|'baseurlpath' => getenv('SSP_APACHE_ALIAS') . '/',|" \
    -e "s/^.*'auth\.adminpassword'.*=>.*$/'auth.adminpassword' => getenv('SSP_ADMIN_PASSWORD'),/" \
    -e "s/^.*'logging\.level'.*=>.*$/'logging.level' => (int) getenv('SSP_LOG_LEVEL'),/" \
    -e "s/^.*'logging\.handler'.*=>.*$/'logging.handler' => getenv('SSP_LOG_HANDLER'),/" \
    $SSP_DIR/config/config.php

COPY ssp-startup.sh /opt/simplesaml/ssp-startup.sh
RUN chmod u+x /opt/simplesaml/ssp-startup.sh
CMD /opt/simplesaml/ssp-startup.sh
