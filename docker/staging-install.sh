#!/bin/bash
cd $SSP_DIR
for repo in $STAGINGCOMPOSERREPOS
  do
    echo "Adding repo $repo";
    REPO_NAME=`php -r "print urlencode('$repo');"`
    composer config repositories.$REPO_NAME path $SSP_DIR/staging-modules/$repo
done
