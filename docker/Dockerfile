FROM cespi/php-5.3:modules-apache-latest

ENV APACHE_RUN_USER apache
ENV APACHE_RUN_GROUP apache

ENV APACHE_DOCUMENT_ROOT /app/web

WORKDIR /app
ADD docker/kimkelen-entrypoint /usr/local/bin/kimkelen-entrypoint
ADD . ./
VOLUME [ "/uploads" ]

RUN apk add -U --no-cache wkhtmltopdf gettext \
    --repository http://dl-3.alpinelinux.org/alpine/edge/testing/  && \
    rm -fr /app/cache && mkdir /app/cache && \
    rm -fr /app/log && mkdir /app/log && \
    cp config/propel.ini.docker config/propel.ini && \
    php -d memory_limit=512M -d date.timezone='America/Argentina/Buenos_Aires' symfony propel:build-all -C && \
    php -d memory_limit=512M -d date.timezone='America/Argentina/Buenos_Aires' symfony plugin:publish-assets && \
    php -d memory_limit=512M symfony project:permissions && \
    php symfony cc && \
    echo $REVISION > VERSION

CMD ["kimkelen-entrypoint"]
