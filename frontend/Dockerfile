FROM nginx:stable-alpine

RUN rm -rf /usr/share/nginx/html/*

COPY --chown=nginx:nginx ./frontend /usr/share/nginx/html

COPY --chown=nginx:nginx ./docker/nginx/frontend.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]