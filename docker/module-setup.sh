#!/bin/bash
#
# Remove a default module if you want to install a different version.
set -e
echo "Default Modules to remove: $SSP_DELETE_MODULES"
for module in $SSP_DELETE_MODULES
do
   mkdir /var/tmp/ssp-removed-modules
   echo "Removing $module";
   # We move the module instead of deleting. This is to avoid bad
   # surprises if someone mounted a volume into the container for the
   # module and we delete it
   mv $SSP_DIR/modules/$module /var/tmp/ssp-removed-modules
done

echo "Modules to install with composer: $COMPOSER_REQUIRE"
if [ ! -z "$COMPOSER_REQUIRE" ]; then
    echo "This can be slow. In the long run you may want to build a custom image with your modules pre-installed"
    # FIXME: what user should this run as?
    #export COMPOSER_HOME=/root
    cd $SSP_DIR
    composer require --update-no-dev $COMPOSER_REQUIRE
fi

echo "Modules to enable: $SSP_ENABLED_MODULES"
echo -e "\tModules will be enabled via 'module.enable' setting in config.php unless you override the default config.php"

