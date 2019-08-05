FROM registry.gitlab.com/kimkelen/kimkelen:base-latest

RUN mv /usr/local/bin/docker-php-entrypoint /usr/local/bin/original-docker-php-entrypoint
ADD docker/entrypoint /usr/local/bin/docker-php-entrypoint
ADD docker/setenvdefaults /usr/local/bin/setenvdefaults
ADD . /app
RUN rm /app/web/*_dev.php
ENV APACHE_DOCUMENT_ROOT=/app/web/ \
    DB_ENCODING=utf8
WORKDIR /app

