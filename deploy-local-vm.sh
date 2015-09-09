pscp -r -p src/* root@SaxID-LDAP-Proxy-locale-VM:/srv/www/htdocs/saxid-ldap-proxy/src
ssh root@192.168.56.102 chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy
ssh root@192.168.56.102 php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=prod
ssh root@192.168.56.102 php /srv/www/htdocs/saxid-ldap-proxy/app/console assets:install --symlink -- /srv/www/htdocs/saxid-ldap-proxy/web