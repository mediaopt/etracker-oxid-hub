#!/usr/bin/env bash
project=`basename $PWD`
name=${project//[- ]/_}
sed -i "s/\"platform\": \"\"/\"platform\": \"Oxid\"/" project.json
sed -i "s/\"projectName\": \"\"/\"projectName\": \"$project\"/" project.json
sed -i "s/\"prefix\": \"\"/\"prefix\": \"mo_\"/" project.json
sed -i "s/\"pathToModule\": \"\"/\"pathToModule\": \"copy_this\/modules\/mo\/$name\"/" project.json
cp -Rf skeleton/oxid/* .
sed -i "s/##name##/$name/" src/metadata.php
sed -i "s/##name##/$name/" CustomBundleMaker.php
sed -i "s/mo_NAME/mo_$name/" src/classes/install.php
mv src/classes/install.php src/classes/mo_${name}__install.php