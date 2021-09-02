-- check URL and IP
local url = ngx.shared.antibot:get(ngx.var.remote_addr)

if url == true or (type(url) == "string" and string.find(ngx.var.http_host..ngx.var.uri, url) ~= nil) then
    ngx.log(ngx.NOTICE, "Banned IP detected and refused access: " .. ngx.var.remote_addr)

    ngx.status = ngx.HTTP_TOO_MANY_REQUESTS
    ngx.say("Too many requests")
    return ngx.exit(ngx.HTTP_OK)
end