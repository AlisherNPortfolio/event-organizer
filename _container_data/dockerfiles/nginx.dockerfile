FROM nginx:stable-alpine
# productionda quyidagi qatorlar commentdan chiqariladi
WORKDIR /etc/nginx

COPY _container_data/nginx/nginx.conf .

RUN mv nginx.conf nginx.conf

WORKDIR /var/www/html

COPY . .
