FROM cespi/php-5.3:modules-apache-latest

ENV APACHE_DOCUMENT_ROOT /app/web

WORKDIR /app
ADD . ./
ADD docker/kimkelen-entrypoint /usr/local/bin/kimkelen-entrypoint
VOLUME [ "/uploads" ]

RUN apk add -U --no-cache \
        gettext \
        xvfb \
        ttf-freefont \
        fontconfig \
        dbus \
    && apk add qt5-qtbase \
        wkhtmltopdf \
        --no-cache \
        --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing/ \
        --allow-untrusted \
    # Wrapper for xvfb
    && mv /usr/bin/wkhtmltopdf /usr/bin/wkhtmltopdf-origin && \
    rm -fr /app/cache && mkdir /app/cache && \
    rm -fr /app/log && mkdir /app/log && \
    cp config/propel.ini.docker config/propel.ini && \
    php -d memory_limit=512M -d date.timezone='America/Argentina/Buenos_Aires' symfony propel:build-all -C && \
    php -d memory_limit=512M -d date.timezone='America/Argentina/Buenos_Aires' symfony plugin:publish-assets && \
    php -d memory_limit=512M symfony project:permissions && \
    php symfony cc && \
    echo $REVISION > VERSION

ADD docker/wkhtmltopdf /usr/bin/wkhtmltopdf

CMD ["kimkelen-entrypoint"]
