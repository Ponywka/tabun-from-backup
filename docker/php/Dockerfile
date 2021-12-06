FROM php:7.4-fpm AS development

ENV COMPOSER_VERSION="1.10.17"
ENV COMPOSER_PATH="/usr/local/bin/composer"
ENV COMPOSER_BUILD_DEPS="libzip-dev libonig-dev libcurl4-openssl-dev libmagickwand-6.q16-dev libmagickcore-6.q16-dev"
ENV COMMON_RUNTIME_DEPS="libzip4 libonig5 libcurl4 imagemagick locales unzip"
ENV PECL_EXTENSIONS="imagick redis xdebug"
ENV PHP_CORE_EXTENSIONS="mysqli gettext bcmath zip"
ENV TZ=Europe/Moscow

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Composer dependencies
COPY composer.* ./

RUN apt-get update \
    && apt-get install -y ${COMPOSER_BUILD_DEPS} ${COMMON_RUNTIME_DEPS} \
    && pecl install ${PECL_EXTENSIONS} \
    && docker-php-ext-enable ${PECL_EXTENSIONS} \
    && docker-php-ext-install ${PHP_CORE_EXTENSIONS} \
    && curl -fLsS https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar -o ${COMPOSER_PATH} \
    && chmod +x ${COMPOSER_PATH} \
    && composer install --no-dev --no-interaction --optimize-autoloader \
    && composer clearcache \
    && rm composer.* \
    && rm ${COMPOSER_PATH} \
    && apt-get remove --purge --auto-remove -y ${COMPOSER_BUILD_DEPS} \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-source delete \
    && sed -i -e 's/# ru_RU.UTF-8 UTF-8/ru_RU.UTF-8 UTF-8/' /etc/locale.gen \
    && locale-gen

# Pool configuration
COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/

# Uploads
VOLUME /storage

FROM development AS production

# Application
COPY classes .
COPY settings .
COPY engine .
COPY templates .
COPY index.php .
