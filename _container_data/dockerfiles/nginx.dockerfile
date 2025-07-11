FROM nginx:stable-alpine

WORKDIR /etc/nginx

COPY _container_data/nginx/nginx.conf .

RUN mv nginx.conf nginx.conf

WORKDIR /var/www/html

COPY . .
