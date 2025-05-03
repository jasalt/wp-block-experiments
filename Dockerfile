# Add tools to official Docker image
FROM wordpress:latest

RUN apt-get update && \
	apt-get install -y less

WORKDIR /usr/local/bin
RUN curl -o wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp

RUN curl -o ~/.wp-completion.bash https://raw.githubusercontent.com/wp-cli/wp-cli/main/utils/wp-completion.bash
RUN echo "source ~/.wp-completion.bash" >> ~/.bashrc
RUN echo "alias wp='wp --allow-root'" >> ~/.bashrc

# Change back to original workdir
WORKDIR /var/www/html
