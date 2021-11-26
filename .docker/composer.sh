#!/usr/bin/env bash

pushd "$(dirname "$0")" > /dev/null
pushd .. > /dev/null

docker-compose exec apache-php composer "$@"

popd > /dev/null
popd > /dev/null
