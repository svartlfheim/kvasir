# PHP-FPM

A php-fpm docker image designed to run on the openshift container platform.

---

## www.conf.template

If you look in `./php-fpm.d/www.conf.template` you'll notice that it uses an odd listen directive, something like `listen = __LISTEN_HOST__:__LISTEN_PORT__`. It is created this way so that this image is portable, we should be able to run this image in almost any environment by configuring two corresponding environment variables. Before the php-fpm process is started, the `./php-fpm.d/www.conf.template` file is run through a simple sed script which replaces the corresponding placeholders with the supplied environment variables. See the `./entrypoint.sh` script to see how this is done.

The pattern to use this is very simple, remove the prefixed and suffixed '__' from the placeholder name and that is the name of the environment variable you need to supply to override the property.

---

## Configurable environment variables

This image is aware of the following environment variables:

| Environment variable | Default value   | Description |
|----------------------|-----------------|-------------|
| `LISTEN_HOST`        | `0.0.0.0`       | Changes the host in listen directive for php-fpm to the supplied value. |
| `LISTEN_PORT`        | `9000`          | Changes the port in the listen directive for php-fpm to the supplied value. |
| `APP_DIR`            | `/opt/app-root` | This is only really required if you want to use a readiness probe. It should be the directory where you have placed the application code. |

All of these variables will be replaced (where applicable) before starting the php-fpm process.

