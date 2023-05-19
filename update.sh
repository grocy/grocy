#!/bin/bash

GROCY_RELEASE_URL=https://releases.grocy.info/latest


echo Start updating Grocy

set -e
shopt -s extglob
pushd `dirname $0` > /dev/null

backupBundleFileName="backup-`date +%d-%m-%Y-%H-%M-%S`.tgz"
echo Making a backup of the current installation in ./data/backups/$backupBundleFileName
mkdir -p ./data/backups > /dev/null
touch ./data/backups/$backupBundleFileName
tar -zcvf ./data/backups/$backupBundleFileName --exclude ./data/backups . > /dev/null
find ./data/backups/*.tgz -mtime +60 -type f -delete

echo Deleting everything except ./data and this script
rm -rf !(data|update.sh) > /dev/null

echo Emptying ./data/viewcache
rm -rf ./data/viewcache/* > /dev/null

echo Downloading latest release
rm -f ./grocy-latest.zip > /dev/null
wget $GROCY_RELEASE_URL -q -O ./grocy-latest.zip > /dev/null

echo Unzipping latest release
unzip -o ./grocy-latest.zip > /dev/null
rm -f ./grocy-latest.zip > /dev/null

popd > /dev/null

echo Finished updating Grocy
