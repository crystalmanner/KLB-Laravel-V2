# List of all supported tags and respective Dockerfile links for PHP images
# https://github.com/docker-library/docs/tree/master/php
# Documentation on official PHP Docker images
# https://hub.docker.com/_/php
FROM php:7.4-fpm

LABEL maintainer="Z. Patrick Lewis <zpatricklewis@gmail.com>"

ENV DEBIAN_FRONTEND=noninteractive
# Timezone, defaulting to UTC
# https://docs.docker.com/engine/reference/builder/#environment-replacement
ENV TZ=${TZ:-UTC}
# Set so that you can use the "clear" command
# ENV TERM=linux
ENV TERM=xterm-256color
# Configure additional path for MSSQL tools.
ENV PATH=$PATH:/opt/mssql-tools/bin
ENV APP_HOME /var/www

# Install the install-php-extensions script from a Docker image
# https://github.com/mlocati/docker-php-extension-installer#copying-the-script-from-a-docker-image
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
# Optimal - create your own build image and install Composer inside it
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set the correct timezone
# https://serverfault.com/a/683651
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# The set command prints each command in red in the log, making it easier to see
# where one operation starts and the next one ends
RUN set -eux \
    # Get major Debian version number
    && COMPLETE_VERSION=$(cat /etc/debian_version) \
    # Keep the part of the complete version before the period
    # https://stackoverflow.com/a/19482947/1620794
    && MAJOR_VERSION=${COMPLETE_VERSION%.*} \
    && apt-get update && apt-get upgrade -y && apt-get install -y \
        # Installs the ps command
        procps \
        # nano \
        git \
        unzip \
        libicu-dev \
        zlib1g-dev \
        libxml2 \
        libxml2-dev \
        libreadline-dev \
        supervisor \
        cron \
        libzip-dev \
        # Packages needed for LDAP support
        libjpeg-dev \
        libldap2-dev \
        libpng-dev \
        # Needed for the PHP GD extension
        libfreetype6-dev \
        libwebp-dev \
        # Needed for MSSQL
        gnupg \
        lsb-release \
        unixodbc \
        unixodbc-dev \
        vim \
        wget \
        zip \
    # Configure the GD extension; this changed with PHP 7.4
    # https://github.com/docker-library/php/issues/931#issuecomment-568658449
    # --with-png may not be an option any more
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    # Configure LDAP extension
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    # Install Microsoft SQL ODBC Driver + Server Tools (requires gnupg package installed first)
    # https://docs.microsoft.com/en-us/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-2017#debian-8-and-9
    # Use the "odbcinst" command on this page to confirm that the driver is installed:
    # odbcinst -q -d -n "ODBC Driver 17 for SQL Server"
    # 1. Import the public repository GPG keys needed for MSSQL ODBC Driver
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    # 2. Register the Microsoft Debian repository for the current version (Buster = 10)
    && echo "https://packages.microsoft.com/config/debian/${MAJOR_VERSION}/prod.list" \
    && curl "https://packages.microsoft.com/config/debian/${MAJOR_VERSION}/prod.list" > /etc/apt/sources.list.d/mssql-release.list \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list \
    # 3. Update the sources list and run the installation command with the unixODBC developer package.
    && apt-get update -yqq \
    # 4. Uninstall possibly conflicting packages for Microsoft SQL ODBC Driver + Server Tools
    && apt-get remove --purge msodbcsql17 unixodbc-utf16 \
    # 5. Install the actual packages for Microsoft SQL server tools
    # Installing msodbcsql installs version 13.1 and 17 (needs debian 8 prod.list);
    && ACCEPT_EULA=Y apt-get -y install odbcinst1debian2 msodbcsql17 mssql-tools  \
    # 6. Add Microsoft Server Tools to PATH; for bcp and sqlcmd
    && echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile \
    && echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc \
    # 7. Source the .bashrc file (needed for Microsoft SQL ODBC Driver + Server Tools)
    && . ~/.bashrc \
    # List of extensions required by Laravel can be found here:
    # https://laravel.com/docs/8.x/deployment#server-requirements
    # Only certain extensions are supported with this install script
    && docker-php-ext-install \
      bcmath \
    # This extension is already loaded
    #   ctype \
    # This extension is already loaded
    #   fileinfo \
      gd \
      intl \
    # This extension is already loaded
    #   json \
      ldap \
    # mbstring extension is already installed and configured as of PHP v5.4
    # https://stackoverflow.com/a/59253249/1620794
    #   mbstring \
      mysqli \
      opcache \
      pdo_mysql \
    #   pdo_sqlsrv \
      sockets \
    # This extension is already loaded
    #   tokenizer \
      zip \
    && install-php-extensions apcu pdo_sqlsrv sqlsrv xdebug yaml \
    # Install latest stable ShellCheck for Linux (https://www.shellcheck.net/)
    && wget https://github.com/koalaman/shellcheck/releases/download/stable/shellcheck-stable.linux.x86_64.tar.xz \
    && tar -xf ./shellcheck-stable.linux.x86_64.tar.xz \
    && cp ./shellcheck-stable/shellcheck /usr/local/bin/ \
    && chmod gou+x /usr/local/bin/shellcheck \
    # ShellCheck cleanup
    && rm shellcheck-stable.linux.x86_64.tar.xz \
    && rm -rf ./shellcheck-stable \
    # Install the current version of Node.js
    && curl -sL https://deb.nodesource.com/setup_current.x | bash - \
    && apt-get install -y nodejs \
    # Clean up the APT cache so it isn't stored in the layer
    # You'll need to run apt-get -yqq update to update the lists for apt-get if you
    # open a shell into Docker
    && apt-get -y autoremove \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# create document root
RUN mkdir -p $APP_HOME/public

# change owner
RUN chown -R www-data:www-data $APP_HOME

# set working directory
WORKDIR $APP_HOME

# Set permissions for composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN chmod gou+x /usr/bin/composer \
    # create composer folder for user www-data
    && mkdir -p /var/www/.composer && chown -R www-data:www-data /var/www/.composer \
    && mkdir -p /var/www/.npm \
    && chown -R www-data:www-data /var/www/.npm \
    # Make IPv4 traffic have a higher priority than IPv6 for Composer
    # https://getcomposer.org/doc/articles/troubleshooting.md#operation-timed-out-ipv6-issues-
    && echo 'precedence ::ffff:0:0/96 100' >> /etc/gai.conf

# Put PHP config for Laravel
COPY ./php/php.ini /usr/local/etc/php/php.ini
# Copy additional config for PHP-FPM; the default config looks in /usr/local/etc/php-fpm.d/*.conf
# COPY ./php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Switch to the non-root user setup for php-fpm
USER www-data

# Port for HTTP
EXPOSE 80
# Port for HTTPS
EXPOSE 443
# Remote debugging port to use with Visual Studio Code
EXPOSE 3000
# MySQL Port
EXPOSE 3306
# PHP-FPM port
EXPOSE 9000
