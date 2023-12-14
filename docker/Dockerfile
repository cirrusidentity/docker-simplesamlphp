FROM php:7.4-apache

# Simplify installing php extensions. See
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

# Install the various packages needed by ssp and composer
RUN apt-get update -y && apt-get install -y \
  git \
  file \
  ssl-cert \
  patch \
  curl \
  zip \
  vim \
  unzip

RUN install-php-extensions gmp ldap soap apcu memcached zip


# Download and enable composer. See 'Manual Download' https://getcomposer.org/download/
ENV COMPOSER_HASH=d6eee0d4637f4bd82bdae098fceda300dcb3ec35bf502604fbe7510933b8f952 \
    COMPOSER_VERSION=2.0.11

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar \
  &&  echo "$COMPOSER_HASH /usr/local/bin/composer" | sha256sum -c - \
  && chmod a+x  /usr/local/bin/composer

ENV APACHE_CERT_NAME=ssl-cert-snakeoil \
    COMPOSER_REQUIRE="" \
    SSP_ADMIN_PASSWORD=123 \
    SSP_APACHE_ALIAS="simplesaml/" \
    SSP_DELETE_MODULES="" \
    SSP_DIR=/var/simplesamlphp \
    SSP_ENABLED_MODULES="" \
    SSP_HASH=2457dd6f18a457e7bc291566123fc60595efb6ee18c1ffc9823223644c2b87e2 \
    SSP_LOG_HANDLER=errorlog \
    SSP_LOG_LEVEL=6 \
    SSP_NEW_UI=false \
    SSP_VERSION=1.19.7 \
    COMPOSER_REQUIRE=""


# Add default index page 
RUN echo "You should access SSP on https.<p/>If you are using the nginx docker proxy make sure to set 'VIRTUAL_PORT=443' and 'VIRTUAL_PROTO=https'" > /var/www/html/index.html \
    && echo 'By default SSP is on <a href="/simplesaml">/simplesaml</a>' > /var/www/index.html

# Enable Apache modules
RUN a2enmod ssl rewrite
EXPOSE 443

# Sites must end with .conf
ADD ssp-apache.conf /etc/apache2/sites-available/ssp.conf
RUN a2ensite ssp
# Make directories often required by commonly used modules
RUN mkdir -p $SSP_DIR/data \
    && mkdir $SSP_DIR/ticketcache \
    && chown -R www-data $SSP_DIR

USER www-data
RUN curl -L -o /tmp/ssp.tar.gz https://github.com/simplesamlphp/simplesamlphp/releases/download/v$SSP_VERSION/simplesamlphp-$SSP_VERSION.tar.gz \
  &&  echo "$SSP_HASH  /tmp/ssp.tar.gz" | sha256sum -c - \
  && tar xvzf /tmp/ssp.tar.gz --strip-components 1 -C $SSP_DIR

ADD ssp/config/* /var/simplesamlphp/config/
RUN echo 'require "config-override-base.php";' >> /var/simplesamlphp/config/config.php

USER root
COPY module-setup.sh /opt/simplesaml/
COPY staging-install.sh /opt/simplesaml/
RUN chmod a+x /opt/simplesaml/staging-install.sh && \
    chmod a+x /opt/simplesaml/module-setup.sh

COPY ssp-startup.sh /opt/simplesaml/ssp-startup.sh
RUN chmod u+x /opt/simplesaml/ssp-startup.sh
CMD /opt/simplesaml/ssp-startup.sh
