FROM nginx:latest
WORKDIR /var/www/html
ARG DOMAIN

COPY ./deployment/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./deployment/nginx/application.conf /etc/nginx/conf.d/default.conf

RUN sed -E -i -e "s/domain.com/${DOMAIN}/g" /etc/nginx/conf.d/default.conf

