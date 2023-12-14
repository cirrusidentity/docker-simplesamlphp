#!/bin/bash
su www-data -s /bin/bash -c /opt/simplesaml/staging-install.sh
su www-data -s /bin/bash -c /opt/simplesaml/module-setup.sh


if [ -f /opt/simplesaml/run-on-start.sh ]; then
    /opt/simplesaml/run-on-start.sh
fi
echo "Starting apache"
apache2-foreground
