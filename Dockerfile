# Add tools to official WordPress image based on:
# - bookworm-slim https://hub.docker.com/_/debian
# - https://github.com/docker-library/php/blob/master/8.3/bookworm/apache/Dockerfile
# - https://github.com/docker-library/wordpress/blob/master/latest/php8.3/apache/Dockerfile

FROM wordpress:latest


# General dependencies & utilities
# NOTE: vim-tiny does not support syntax highlighting

RUN apt-get update && \
	apt-get install -y less vim-tiny
	# && rm -rf /var/lib/apt/lists/* # clears package index to free space


# Install XDebug https://xdebug.org/docs/install#pecl

RUN pecl channel-update pecl.php.net
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug


# Install WP-CLI
# https://make.wordpress.org/cli/handbook/guides/installing/

WORKDIR /usr/local/bin

RUN curl -o wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp

RUN curl -o ~/.wp-completion.bash https://raw.githubusercontent.com/wp-cli/wp-cli/main/utils/wp-completion.bash
RUN echo "source ~/.wp-completion.bash" >> ~/.bashrc
RUN echo "alias wp='wp --allow-root'" >> ~/.bashrc


# Change back to original workdir
WORKDIR /var/www/html
