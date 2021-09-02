FROM fabiocicerchia/nginx-lua:alpine

RUN mkdir -p /etc/nginx/lua
COPY ../lua/antibot-check.lua /etc/nginx/lua
COPY ../lua/antibot-update.lua /etc/nginx/lua

RUN mkdir -p /etc/nginx/lua/modules
COPY ../lua/modules/json.lua /etc/nginx/lua/modules

CMD ["nginx", "-g", "daemon off;"]