#!/bin/bash
set -eox pipefail # should stop script if something fails


if [ -f "/COMPOSE_INITIALIZED" ]; then
	echo "Already initialized. Skipping."
else
	echo "Running post-init.sh"

	echo `whoami`

	sleep 8
	# TODO should wait for db?

	wp core install --allow-root --url=localhost:8080 --title="Scittle Gutenberg Block Demo Site" --admin_user=admin --admin_password=password --admin_email=example@example.com

	echo `date` > /COMPOSE_INITIALIZED
fi

apache2-foreground  # run original CMD # TODO shows default apache page
