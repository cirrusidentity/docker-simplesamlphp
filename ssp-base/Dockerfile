FROM php:5.6.14-apache
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
# mcrypt: ?
# mbstring: need in various comparison operations. `simplesamlphp/composer-module-installer` won't work without it (reports "ssp-mixedcase-module-name" issues)
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
ENV COMPOSER_HASH=d8e1d21081b7076dfc9a570620e082111ab196dabd83780e50025c52d62b801c \
    COMPOSER_VERSION=1.1.0

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar \
  &&  echo "$COMPOSER_HASH /usr/local/bin/composer" | sha256sum -c - \
  && chmod u+x  /usr/local/bin/composer

# TODO: Have composer do a pre-fetch of packagist so future build are faster?

ENV SSP_ADMIN_PASSWORD=123 \
    SSP_DIR=/var/simplesamlphp \
    SSP_HASH=a7a24d4dc89819f7e53141b38ae36b092a5c1fc9cb2e3cee253c765e5942be52 \
    SSP_LOG_HANDLER=errorlog \
    SSP_LOG_LEVEL=6 \
    SSP_VERSION=1.14.7
    
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
RUN sed -i.bak \
    -e "s/^.*'baseurlpath'.*=>.*$/'baseurlpath' => getenv('SSP_APACHE_ALIAS'),/" \
    -e "s/^.*'auth\.adminpassword'.*=>.*$/'auth.adminpassword' => getenv('SSP_ADMIN_PASSWORD'),/" \
    -e "s/^.*'logging\.level'.*=>.*$/'logging.level' => (int) getenv('SSP_LOG_LEVEL'),/" \
    -e "s/^.*'logging\.handler'.*=>.*$/'logging.handler' => getenv('SSP_LOG_HANDLER'),/" \
    $SSP_DIR/config/config.php

COPY ssp-startup.sh /opt/simplesaml/ssp-startup.sh
RUN chmod u+x /opt/simplesaml/ssp-startup.sh
CMD /opt/simplesaml/ssp-startup.sh
