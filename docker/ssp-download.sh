#!/bin/bash
set -e
if [ -z "$SSP_COMPOSER_VERSION" ]
then
  # Download a release
  curl -L -o /tmp/ssp.tar.gz https://github.com/simplesamlphp/simplesamlphp/releases/download/v$SSP_VERSION/simplesamlphp-$SSP_VERSION.tar.gz
  echo "$SSP_HASH  /tmp/ssp.tar.gz" | sha256sum -c -
  tar xvzf /tmp/ssp.tar.gz --strip-components 1 -C $SSP_DIR
  echo $SSP_VERSION > $SSP_DIR/ssp_release_version
else
   # Download with composer
  composer create-project simplesamlphp/simplesamlphp:$SSP_COMPOSER_VERSION $SSP_DIR
  echo $SSP_COMPOSER_VERSION > $SSP_DIR/ssp_composer_version
  # TODO: decide if we should just run https://github.com/simplesamlphp/simplesamlphp/blob/master/bin/build-release.sh
  # rather than replicating similar steps here...
  cd $SSP_DIR
  mkdir -p "$SSP_DIR/config" "$SSP_DIR/metadata" "$SSP_DIR/cert" "$SSP_DIR/log" "$SSP_DIR/data"
  cp -rv "$SSP_DIR/config-templates/"* "$SSP_DIR/config/"
  cp -rv "$SSP_DIR/metadata-templates/"* "$SSP_DIR/metadata/"
fi
