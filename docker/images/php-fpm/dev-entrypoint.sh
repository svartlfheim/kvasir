#! /bin/sh

if [ ! -z "$OVERRIDE_WWW_DATA_UID" ]; then
	echo "Set www-data uid to $OVERRIDE_WWW_DATA_UID"
	usermod -u $OVERRIDE_WWW_DATA_UID www-data
fi

if [ ! -z "$OVERRIDE_WWW_DATA_GID" ]; then
	echo "Set www-data gid to $OVERRIDE_WWW_DATA_GID"
	groupmod -g $OVERRIDE_WWW_DATA_GID www-data
fi

/entrypoint.sh "$@"