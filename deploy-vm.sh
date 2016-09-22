pscp -r -p * root@SaxID-LDAP-Proxy:/srv/www/htdocs/saxid-ldap-proxy
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/.git
ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/logs/*
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/cache/*
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console assets:install --symlink -- /srv/www/htdocs/saxid-ldap-proxy/web
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=prod


#HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
#setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
#setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs