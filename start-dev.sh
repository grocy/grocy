#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker build -t grocy:dev -f ./Dockerfile.dev .
docker run -p 8000:80 -v $DIR:/var/www/html -e DATA_UID=$UID grocy:dev