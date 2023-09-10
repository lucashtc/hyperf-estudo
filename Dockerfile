FROM hyperf/hyperf:8.2-alpine-v3.18-swoole-v5

ARG timezone

ENV TIMEZONE=${timezone:-"America/Sao_Paulo"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

RUN apk update && apk upgrade
# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php* \
    # - config PHP
    && { \
    echo "upload_max_filesize=128M"; \
    echo "post_max_size=128M"; \
    echo "memory_limit=1G"; \
    echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /app

EXPOSE 9501

# ENTRYPOINT ["php", "/app/bin/hyperf.php", "start"]
