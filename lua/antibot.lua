package.path = package.path .. ";/etc/nginx/lua/?.lua"

local ip_blacklist = ngx.shared.antibot
local last_update_time = ip_blacklist:get("last_update_time");

local bit = require "bit"
local lshift = bit.lshift
local bnot = bit.bnot
local band = bit.band

local function ip2long( ip )
  if ip == nil then
    return nil
  end

  local o1,o2,o3,o4 = ip:match("(%d+)%.(%d+)%.(%d+)%.(%d+)")
  if o1 == nil or o2 == nil or o3 == nil or o4 == nil then
    return nil
  end

  return 2^24*o1 + 2^16*o2 + 2^8*o3 + o4
end

local function unsign(bin)
  if bin < 0 then
    return 4294967296 + bin
  end
  return bin
end

local function ip_in_cidr(ip, cidr)
  local ip_ip = ip2long(ip)
  local net, mask = string.match(cidr, "(.*)%/(.*)")
  if net == nil then net = cidr end
  local ip_net = ip2long(net)
  if mask then
    local mask_num  = tonumber(mask)
    if mask_num > 32 or mask_num < 0 then
      return nil, "Invalid prefix: /"..tonumber(mask)
    end
    local ip_mask = bnot(lshift(1, 32 - mask) - 1)
    local ip_ip_net = unsign(band(ip_ip, ip_mask))
    return ip_ip_net == ip_net
  else
    return ip_ip == ip_net
  end

end

-- only update ip_blacklist from Redis once every cache_ttl seconds:
if last_update_time == nil or last_update_time < ( ngx.now() - ngx.var.cache_ttl ) then

  local redis = require "resty.redis"
  local red = redis:new()

  red:set_timeout(ngx.var.redis_connection_timeout)

  local ok, err = red:connect(ngx.var.redis_host, tonumber(ngx.var.redis_port))
  if not ok then
    ngx.log(ngx.ERROR, "Redis connection error while retrieving ip_blacklist: " .. err)
  else
    local new_ip_blacklist, err = red:get(ngx.var.redis_key)

    if err then
      ngx.log(ngx.ERROR, "Redis read error while retrieving ip_blacklist: " .. err)
    else
      -- replace the locally stored ip_blacklist with the updated values:
      local json = require "json"
      ip_blacklist:flush_all()

      if type(new_ip_blacklist) == "string" then
          for banned_ip, banned_data in pairs(json.decode(new_ip_blacklist)) do
            -- if ban time did not expired
            if banned_data["time"] > ngx.now() then
                local url = banned_data["url"]
                if url == nil then
                    url = false
                end

                ip_blacklist:set(banned_ip, url, banned_data["time"] - ngx.now())
            end
          end
      end

      -- update time
      ip_blacklist:set("last_update_time", ngx.now())
    end
  end
end

-- check URL and IP
for _,cidr in ipairs(ip_blacklist:get_keys(0)) do
    if ip_in_cidr(ngx.var.remote_addr, cidr) then
        local url = ip_blacklist:get(cidr)
        if url == false or string.find(ngx.var.uri, url) ~= nil then
            ngx.log(ngx.NOTICE, "Banned IP detected and refused access: " .. ngx.var.remote_addr)

            ngx.status = ngx.HTTP_TOO_MANY_REQUESTS
            ngx.say("Too many requests")
            return ngx.exit(ngx.HTTP_OK)
        end
    end
end