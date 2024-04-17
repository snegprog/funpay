FROM php:8.2-alpine3.18

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync

RUN mkdir -p /var/www/app  \
    && apk update  \
    && apk add bash vim net-tools tzdata openssh \
    && echo "set number" >> /etc/vim/vimrc

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer

RUN install-php-extensions mysqli

RUN ln -snf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

WORKDIR /var/www/app
