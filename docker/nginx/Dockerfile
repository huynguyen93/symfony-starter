FROM debian:jessie
ARG uid=1000
ARG gid=1000

MAINTAINER Maxence POUTORD <maxence.poutord@gmail.com>

RUN apt-get update && apt-get install -y \
    nginx

ADD nginx.conf /etc/nginx/
ADD ssl /etc/nginx/ssl
ADD symfony.conf /etc/nginx/sites-available/

RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony
RUN rm /etc/nginx/sites-enabled/default

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

RUN usermod -u $uid www-data
RUN groupmod -g $gid www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
