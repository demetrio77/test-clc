FROM php:7.0.9-apache
MAINTAINER Dmitry Karpov

RUN	apt-get update && apt-get install -y \
		curl \
		git \
		unzip \
		netcat-openbsd \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libmcrypt-dev \
		libpng12-dev \
		libgif-dev \
	&& rm -r /var/lib/apt/lists/* \
	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install pdo \
	&& docker-php-ext-install pdo_mysql \
	&& docker-php-ext-install zip \
	&& docker-php-ext-install mbstring \
	&& docker-php-ext-install fileinfo \
	&& docker-php-ext-install ctype \
	&& docker-php-ext-install json

# Dom
RUN \
	apt-get	update && \
	apt-get install -y libxml2-dev && \
	rm -rf /var/lib/apt/lists/* && \
	docker-php-ext-install dom && \
	apt-get clean

# Imagick
RUN \
	apt-get	update && \
	apt-get install -y libmagickwand-dev && \
	rm -rf /var/lib/apt/lists/* && \
	pecl install imagick && \
	docker-php-ext-enable imagick && \
	apt-get clean

# Imap
RUN	\
	apt-get	update && \
	apt-get -y install libssl-dev libc-client2007e-dev libkrb5-dev && \
	rm -rf /var/lib/apt/lists/* && \
	docker-php-ext-configure imap --with-imap-ssl --with-kerberos && \
	docker-php-ext-install imap && \
	apt-get clean

# Curl
RUN	\
	apt-get	update && \
	apt-get install -y libcurl4-openssl-dev && \
	rm -rf /var/lib/apt/lists/* && \
	docker-php-ext-install curl && \
	apt-get clean

# Intl
RUN	\
	apt-get	update && \
	apt install -y libicu-dev && \
	rm -rf /var/lib/apt/lists/* && \
	docker-php-ext-install -j$(nproc) intl && \
	apt-get clean

# Install Composer
RUN	\
	cd /tmp && \
	curl -sS https://getcomposer.org/installer | php && \
	mv composer.phar /usr/local/bin/composer

RUN touch /etc/apache2/envmy && \
	echo ". /etc/apache2/envmy" >> /etc/apache2/envvars

# Memory Limit
RUN echo "memory_limit=512M" > $PHP_INI_DIR/conf.d/memory-limit.ini

# Settings session
RUN mkdir /session && \
	chown www-data:www-data /session && \
	echo "session.save_handler=files" >  $PHP_INI_DIR/conf.d/session.ini && \
	echo "session.save_path=/session" >> $PHP_INI_DIR/conf.d/session.ini

COPY ./docker/startup.sh  /usr/local/bin/startup
COPY ./docker/apache.conf /etc/apache2/sites-available/

# ---/ Web /---
RUN	a2enmod rewrite && \
	rm /etc/apache2/sites-enabled/* && \
	mkdir /var/www/clc && \
	mkdir /var/www/clc/web && \
	mkdir /var/www/clc/web/u && \
	mkdir /log && \
	chown www-data:www-data /log && \
	chown www-data:www-data /var/www/clc/web/u && \
	chmod +x /usr/local/bin/startup

EXPOSE 80
WORKDIR /var/www/clc
CMD ["/usr/local/bin/startup"]