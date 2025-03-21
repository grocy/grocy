#!/bin/bash
echo "[$(date '+%d/%m/%Y %H:%M:%S')] Start updating Grocy"

GROCY_RELEASE_URL=https://releases.grocy.info/latest

set -e
shopt -s extglob
pushd `dirname $0` > /dev/null

backupBundleFileName="backup_`date +%Y-%m-%d_%H-%M-%S`.tgz"
echo "[$(date '+%d/%m/%Y %H:%M:%S')] Making a backup of the current installation in ./data/backups/$backupBundleFileName"
mkdir -p ./data/backups > /dev/null
touch ./data/backups/$backupBundleFileName
tar -zcvf ./data/backups/$backupBundleFileName --exclude ./data/backups . > /dev/null
find ./data/backups/*.tgz -mtime +60 -type f -delete

echo "[$(date '+%d/%m/%Y %H:%M:%S')] Deleting everything except ./data and this script"
rm -rf !(data|update.sh) > /dev/null

echo "[$(date '+%d/%m/%Y %H:%M:%S')] Downloading latest release"
rm -f ./grocy-latest.zip > /dev/null
wget $GROCY_RELEASE_URL -q --show-progress -O ./grocy-latest.zip > /dev/null

echo "[$(date '+%d/%m/%Y %H:%M:%S')] Unzipping latest release"
unzip -o ./grocy-latest.zip > /dev/null
rm -f ./grocy-latest.zip > /dev/null

popd > /dev/null

echo "[$(date '+%d/%m/%Y %H:%M:%S')] Make sure the new update.sh script is executable"
chmod +x ./update.sh

echo "[$(date '+%d/%m/%Y %H:%M:%S')] Finished updating Grocy"
