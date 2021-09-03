-- Update antibot database from redis storage

local ip_blacklist = ngx.shared.antibot
local redis = require "resty.redis"
local red = redis:new()
local json = require "json"

if ngx.var.refresh_key ~= nil and ngx.var.arg_key ~= ngx.var.refresh_key then
    ngx.status = 403
    ngx.log(ngx.ERR, "Invalid refresh key")
    ngx.say("Invalid refresh key")
    return
end

local ok, err = red:connect(ngx.var.redis_host, tonumber(ngx.var.redis_port))
if not ok then
    ngx.log(ngx.ERR, "Redis connection error while retrieving ip_blacklist: " .. err)
    ngx.say("Redis connection error while retrieving ip_blacklist: " .. err)
    return
end

if ngx.var.redis_auth ~= nil then
    local ok, err = red:auth(ngx.var.redis_auth)
    if not ok then
        ngx.log(ngx.ERR, "Failed to authenticate while retrieving ip_blacklist: " .. err)
        ngx.say("Failed to authenticate while retrieving ip_blacklist: " .. err)
        return
    end
end

local new_ip_blacklist, err = red:get(ngx.var.redis_key)
if err then
    ngx.log(ngx.ERR, "Redis read error while retrieving ip_blacklist: " .. err)
    ngx.say("Redis read error while retrieving ip_blacklist: " .. err)
    return
end

ip_blacklist:flush_all()
if type(new_ip_blacklist) == "string" then
    local stamp = ngx.now()

    for banned_ip, banned_data in pairs(json.decode(new_ip_blacklist)) do
        -- if ban time did not expired
        if banned_data["time"] > stamp then
            ip_blacklist:set(banned_ip, banned_data["url"] or true, banned_data["time"] - stamp)
        end
    end
end

red:close()
ngx.log(ngx.NOTICE, "Antibot was successfully updated")
ngx.say("Antibot was successfully updated")