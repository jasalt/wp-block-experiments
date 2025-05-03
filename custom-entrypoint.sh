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
	sleep 10
	# TODO should wait for db?

	echo "Installing WP"

	wp core install --allow-root --url=localhost:8080 --title="Scittle Gutenberg Block Demo Site" --admin_user=admin --admin_password=password --admin_email=example@example.com

	# Symlink plugin folder to correct place inside container
	if [ ! -L "/var/www/html/wp-content/plugins/scittle-gutenberg-block" ]; then
		ln -s /mnt/scittle-gutenberg-block /var/www/html/wp-content/plugins/
		echo "Created symlink"
	else
		echo "Symlink already exists"

	fi

	# 	wp plugin activate scittle-gutenberg-block --allow-root

	echo `date` > /COMPOSE_INITIALIZED

	# echo ""
    # echo " __               __                       _          "
    # echo "(_  _o_|__|_| _  /__  _|_ _ ._ |_  _ .__  |_)| _  _|  "
    # echo "__)(_| |_ |_|(/_ \_||_||_(/_| ||_)(/_|(_| |_)|(_)(_|< "
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

# Add your custom logic here
# For example:
# - Install plugins
# - Configure settings
# - Run database operations
# etc.

# If the original command was to start apache/php-fpm, you'll need to exec it again
# because the original entrypoint would have exec'd it (replacing itself)
# if [[ "${ORIGINAL_ARGS[0]}" == apache2* ]] || [ "${ORIGINAL_ARGS[0]}" = 'php-fpm' ]; then
#   echo "Starting web server..."
#   exec "${ORIGINAL_ARGS[@]}"
# fi

if [ $# -eq 0 ]; then
    exec apache2-foreground
else
    exec "$@"
fi
