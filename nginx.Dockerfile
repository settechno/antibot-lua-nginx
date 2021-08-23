FROM fabiocicerchia/nginx-lua:alpine

RUN mkdir -p /etc/nginx/lua
COPY antibot.lua /etc/nginx/lua
COPY json.lua /etc/nginx/lua

#RUN mkdir -p /var/run/nginx
#COPY nginx/default.conf.template /etc/nginx/templates

CMD ["nginx", "-g", "daemon off;"]