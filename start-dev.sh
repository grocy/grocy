#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
DATA_UID=$UID
CLEAN=

while [ "$1" != "" ]; do
  case $1 in
    -u | --uid )      shift       # Override the UID override passed for www-data
                      DATA_UID=$1
                      ;;
    -c | --clean )    shift       # Delete all cache and vendor files
                      CLEAN=1
                      ;;
    * )               usage
                      exit 1
  esac
  shift
done

docker build -t grocy:dev -f ./Dockerfile.dev .
docker run -p 8000:80 -v $DIR:/var/www/html -e DATA_UID=$DATA_UID -e CLEAN=$CLEAN grocy:dev