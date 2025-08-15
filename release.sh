#!/bin/bash

# Exit on any error
set -e

# --- Config ---
# Get the version from version.json
VERSION=$(grep -o '"version": "[^"]*' version.json | grep -o '[^"]*$')
RELEASE_NAME="grocy"
RELEASE_FILE_NAME="${RELEASE_NAME}_${VERSION}.zip"
RELEASE_BUILD_DIR=".release"
RELEASE_APP_DIR="${RELEASE_BUILD_DIR}/${RELEASE_NAME}"
# ---

echo "▶️ Starting release creation of version ${VERSION}..."

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 1. Initial cleanup..."

rm -f ${RELEASE_FILE_NAME}
rm -rf ${RELEASE_BUILD_DIR}
mkdir -p ${RELEASE_APP_DIR}

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 2. Installing composer dependencies..."

composer install --no-dev --optimize-autoloader

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 3. Installing npm dependencies..."

yarn install

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 4. Copying all application files..."

rsync -av . ${RELEASE_APP_DIR} \
	--exclude ".git" \
	--exclude ".github" \
	--exclude ".gitignore" \
	--exclude ".gitattributes" \
	--exclude ".devcontainer" \
	--exclude ".release" \
	--exclude "release.sh" \
	--exclude "tests" \
	--exclude ".editorconfig" \
	--exclude ".php-cs-fixer.php" \
	--exclude ".tx" \
	--exclude ".vscode" \
	--exclude ".yarnrc" \
    --exclude "yarn.lock" \
    --exclude "package.json" \
    --exclude "composer.json" \
    --exclude "composer.lock"

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 5. Creating the release ZIP archive..."

cd ${RELEASE_BUILD_DIR}
zip -r ../${RELEASE_FILE_NAME} .
cd ..

# ---------------------------------------------------------------------------------------------------------
echo "▶️ 6. Final cleanup..."

rm -rf ${RELEASE_BUILD_DIR}

# ---------------------------------------------------------------------------------------------------------
echo "✅ Release successfully created: ${RELEASE_FILE_NAME}"
