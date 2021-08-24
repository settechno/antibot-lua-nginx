FROM fabiocicerchia/nginx-lua:alpine

RUN mkdir -p /etc/nginx/lua
COPY ../lua/antibot.lua /etc/nginx/lua

CMD ["nginx", "-g", "daemon off;"]