#!/bin/sh
########################################
# Deploy Script
# Zipping and Uploading Symfony Contents to Webserver. Setting up Owner, clearing cache
# an installing Assets with symlink.
# TODO: make host variable for easy deployment
#
# Author: Jan Frömberg
# Date:   Mai 2016
########################################

if [ $# -gt 0 ]; then
    UPLOAD=$1
else
    UPLOAD=false
fi

uploadfile="saxidprox_depl.tgz"

echo "Creating Archive ..."
rm -f $uploadfile
# alternative: composer archive | gzip > $uploadfile
tar --exclude='.git' --exclude='*.DS_Store' -cf - . | pv -s $(($(du -sk . | awk '{print $1}') * 1024)) | gzip > $uploadfile
du -h $uploadfile
#bzip2 saxidprox_depl.tar
#tar uf saxidprox_depl.tar *
if [ "$UPLOAD" = true ]; then
  echo "Deploying Archive to VM ..."
  echo "Backing up old Content on VM"
  echo "... creating backup"
  ssh root@saxid.zih.tu-dresden.de /root/BackupSaxProxy.sh
  echo "... deleting old content on server"
  ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/*
  # -r recursive and -p preserve -> time...
  echo "... copy new stuff to server"
  scp $uploadfile root@saxid:/srv/www/htdocs/saxid-ldap-proxy
  echo "Configuring VM ..."
  echo "... extracting"
  ssh root@saxid.zih.tu-dresden.de tar xzf /srv/www/htdocs/saxid-ldap-proxy/$uploadfile -C /srv/www/htdocs/saxid-ldap-proxy
  echo "... removing Archive on Server"
  ssh root@saxid.zih.tu-dresden.de rm -f /srv/www/htdocs/saxid-ldap-proxy/$uploadfile
  #echo "... removing .git"
  #ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/.git
  echo "... set owner to wwwrun:www"
  ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy
  ssh root@saxid.zih.tu-dresden.de chmod u+x /srv/www/htdocs/saxid-ldap-proxy/app/console
  echo "... removing Logs and Cache"
  ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/logs/*
  ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/cache/*
  #echo "... installing Server Certificate for SimpleSAML"
  #ssh root@saxid.zih.tu-dresden.de mkdir /srv/www/htdocs/saxid-ldap-proxy/app/config/simplesamlphp/cert
  #ssh root@saxid.zih.tu-dresden.de cp /etc/apache2/ssl.key/saxid.zih.tu-dresden.de.nocrypt.key.pem /srv/www/htdocs/saxid-ldap-proxy/app/config/simplesamlphp/cert/saml.pem.key
  #ssh root@saxid.zih.tu-dresden.de cp /etc/apache2/ssl.crt/saxid.zih.tu-dresden.de.pem /srv/www/htdocs/saxid-ldap-proxy/app/config/simplesamlphp/cert/saml.crt
  echo "... installing assets & clearing cache for prod,dev + setting user to wwwrun:www"
  ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console assets:install --symlink -- /srv/www/htdocs/saxid-ldap-proxy/web
  ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=prod
  ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=dev
  ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy/

  rm -f $uploadfile
else
  echo " "
  echo "No upload! Use Command Line Parameter: './deploy true'"
  echo " "
fi
echo "ALL Done!"

#HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
#setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
#setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
