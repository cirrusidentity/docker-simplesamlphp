#!/bin/bash
#
# Startup apache/ssp and enable specific SSP things, like modules 

echo "Modules to enable: $SSP_ENABLED_MODULES"
for module in $SSP_ENABLED_MODULES
do
   echo "Enabling $module";
   touch $SSP_DIR/modules/$module/enable;
done

# FIXME: add new repos for composer
if [ ! -z "$COMPOSER_REQUIRE" ]; then
    # FIXME: what user should this run as?
    #export COMPOSER_HOME=/root
    cd $SSP_DIR
    composer require "$COMPOSERREQUIRE"
fi

apache2-foreground
