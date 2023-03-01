FROM php:8.1-apache

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

RUN install-php-extensions gmp ldap soap apcu memcached zip pdo_pgsql pdo_mysql intl


# Download and enable composer. See 'Manual Download' https://getcomposer.org/download/
ENV COMPOSER_HASH=91ce6cbf9463eae86ae9d5c21d42faa601a519f3fbb2b623a55ee24678079bd3 \
    COMPOSER_VERSION=2.5.4

RUN curl -o /usr/local/bin/composer https://getcomposer.org/download/$COMPOSER_VERSION/composer.phar \
  &&  echo "$COMPOSER_HASH /usr/local/bin/composer" | sha256sum -c - \
  && chmod a+x  /usr/local/bin/composer

# Used if creating image from packagist SSP
ARG SSP_COMPOSER_VERSION=""

# Use a real cert instead of snakeoil
# APACHE_CERT_NAME=ssl-cert-snakeoil
ENV APACHE_CERT_NAME=local-stack-dev \
    COMPOSER_HOME=/opt/simplesamlphp/.composer \
    SSP_ADMIN_PASSWORD=123 \
    SSP_APACHE_ALIAS="simplesaml/" \
    SSP_DELETE_MODULES="" \
    SSP_DIR=/var/simplesamlphp \
    SSP_ENABLED_MODULES="" \
    SSP_HASH=e2a6e1d143bdb47adf068f6c066f8b69a938f07f3b98813c87dbf8dfa5a81654 \
    SSP_LOG_HANDLER=errorlog \
    SSP_LOG_LEVEL=6 \
    SSP_VERSION=2.0.0 \
    COMPOSER_REQUIRE=""


RUN cp $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini \
    && echo 'expose_php = off' >> $PHP_INI_DIR/php.ini 

# Add default index page 
RUN echo "You should access SSP on https.<p/>If you are using the nginx docker proxy make sure to set 'VIRTUAL_PORT=443' and 'VIRTUAL_PROTO=https'" > /var/www/html/index.html \
    && echo 'By default SSP is on <a href="/simplesaml/">/simplesaml/</a>' > /var/www/index.html

# Enable Apache modules
RUN a2enmod ssl rewrite
EXPOSE 443

# Sites must end with .conf
ADD ssp-apache.conf /etc/apache2/sites-available/ssp.conf
ADD tls/local-stack-dev.key  /etc/ssl/private/${APACHE_CERT_NAME}.key
ADD tls/local-stack-dev.pem  /etc/ssl/certs/${APACHE_CERT_NAME}.pem
RUN a2ensite ssp
RUN  mkdir -p $SSP_DIR && chown www-data $SSP_DIR \
     && mkdir -p $COMPOSER_HOME && chown www-data $COMPOSER_HOME
# Composer installs seem to require us to run npm
RUN if [ -n "$SSP_COMPOSER_VERSION" ]; then apt-get update -y && apt-get install -y npm; mkdir -p "/var/www/.npm" && chown www-data "/var/www/.npm" && chown -R 33:33 "/var/www/.npm"; fi

USER www-data
COPY ssp-download.sh  /opt/simplesaml/
RUN  /opt/simplesaml/ssp-download.sh

ADD ssp/config/* /var/simplesamlphp/config/
# Add config override and Make directories often required by commonly used modules
RUN cp /var/simplesamlphp/config/config.php.dist /var/simplesamlphp/config/config.php \
    && echo 'require "config-override-base.php";' >> /var/simplesamlphp/config/config.php \
    && mkdir -p $SSP_DIR/data \
    && mkdir -p $SSP_DIR/ticketcache

USER root
COPY module-setup.sh /opt/simplesaml/
COPY staging-install.sh /opt/simplesaml
RUN chmod a+x /opt/simplesaml/staging-install.sh && \
    chmod a+x /opt/simplesaml/module-setup.sh && /opt/simplesaml/module-setup.sh

COPY ssp-startup.sh /opt/simplesaml/ssp-startup.sh
RUN chmod u+x /opt/simplesaml/ssp-startup.sh
CMD /opt/simplesaml/ssp-startup.sh
