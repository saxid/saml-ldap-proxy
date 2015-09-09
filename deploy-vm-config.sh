pscp -r -p app/config/* root@SaxID-LDAP-Proxy:/srv/www/htdocs/saxid-ldap-proxy/app/config
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console assets:install --symlink -- /srv/www/htdocs/saxid-ldap-proxy/web
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=prod