# Docker to aid local development

1. Copy `.docker/example.env` to `.env` (in project's root directory), change its contents to reflect your user's UID
   and GID respectively. This will make try to force container to not create any files as root.
2. Copy `.docker/example.docker-compose.override.yml` to `docker-compose.override.yml` in application's root directory.
   You may alter the HOST port (first one, as they're defined as `HOST:CONTAINER`) leaving container's port as `80`.
3. Run `docker-compose up` (or `start`) and wait for fetching, extracting and building process to complete (this will
   happen only once or every time you'd like to rebuild the image)
4. Open separate terminal (or not, if you used `docker-compose start` before) and install project's dependencies like
   this:

   ```shell
   $ .docker/composer.sh install
   ```
5. Visit `http://localhost` (or `http://localhost:8080` if you have altered `HOST`'s port to `8080` in step 2) to see
   Grocy ready for your hacking.
6. Every time you need Composer for something use:

   ```shell
   $ .docker/composer.sh [require|update|etc] [other arguments]
   ```
7. You may also use PHP the same way:

   ```shell
   $ .docker/php.sh -v
   ```

Enjoy!

## BONUS

Would you like to see what happens to the app when launched on PHP 7.4 or 8.1? Edit `.docker/apache-php/Dockerfile` and
change image tag in the first line from `php:8.0-apache` to `php:7.4-apache` or `php:8.1-apache`. Then make
`docker-compose` rebuild the container for you:

```shell
$ docker-compose build
```

And voila! After `docker-compose up` (or `start`) check what works and what breaks!
