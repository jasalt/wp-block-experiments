#!/usr/bin/env bash
set -Eeoxu pipefail

# Wrapper around original entrypoint running extra initialization logic before
# starting the web server

# Ref: https://github.com/docker-library/wordpress/blob/b8721d7271bf763d999285985277d61e78c584aa/latest/php8.3/apache/docker-entrypoint.sh

# Hack avoiding original entrypoint starting the server while passing it's
# checks for expected CMD
echo "echo 'Apache2 doing nothing yet...'" > /usr/local/bin/apache2-noop
chmod +x /usr/local/bin/apache2-noop

# Run the original entrypoint
/usr/local/bin/docker-entrypoint.sh apache2-noop

# After the original entrypoint completes successfully, run custom logic
echo "Running post-entrypoint tasks..."

if [ -f "/COMPOSE_INITIALIZED" ]; then
	echo "Already initialized. Skipping."
else
	echo "Running post-init.sh"

	echo "Waiting 10 seconds for MariaDB before installation"
	sleep 10  # TODO more elegant ways?

	echo "Setting up WP installation with demo credentials"

	wp core install --allow-root --url=localhost:8080 \
	   --title="Scittle WP Block Demo Site" --admin_user=admin \
	   --admin_password=password --admin_email=example@example.com

	# Not necessary but added so Apache doesn't make noise on startup
	echo "ServerName localhost" >> /etc/apache2/apache2.conf

	# Symlink plugin folder to correct place inside container
	if [ ! -L "/var/www/html/wp-content/plugins/scittle-wp-block" ]; then
		ln -s /mnt/scittle-wp-block /var/www/html/wp-content/plugins/
		echo "Created symlink"
	else
		echo "Symlink already exists"
	fi

	wp plugin activate scittle-wp-block --allow-root

	# TODO Put plugin into template / a page (set to be home page)


	date > /COMPOSE_INITIALIZED



	# echo "                    _         _            , __    , __  _             _ "
	# echo "  ()      o        | |       (_|   |   |_//|/  \  /|/  \| |           | |"
	# echo "  /\  __    _|__|_ | |  _      |   |   |   |___/   | __/| |  __   __  | |"
	# echo " /  \/    |  |  |  |/  |/      |   |   |   |       |   \|/  /  \_/    |/_)"
	# echo "/(__/\___/|_/|_/|_/|__/|__/     \_/ \_/    |       |(__/|__/\__/ \___/| \_/"
	# echo ""
	# echo "Setup completed! See the site at http://localhost:8080/"
	# echo ""
	# echo "Scittle gutenberg block should render on the front-page."
	# echo ""
    # echo "Edit it by first logging in to admin:"
    # echo "http://localhost:8080/wp-admin/"
	# echo ""
	# echo "Username: admin"
	# echo "Password: password"

fi

# Run server or given arguments like original entrypoint
if [ $# -eq 0 ]; then
    exec apache2-foreground
else
    exec "$@"
fi
