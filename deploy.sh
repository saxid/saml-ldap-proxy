#!/bin/sh
########################################
# Deploy Script
# Zipping and Uploading Symfony Contents to Webserver. Setting up Owner, clearing cache
# an installing Assets with symlink.
# the Command PV is needed for progress visualization
# TODO: ...
#
# Author: Jan Frömberg
# Date:   Mai 2016-Mrz 2017
########################################

if [ $# -gt 0 ]; then
    UPLOAD=$1
else
    UPLOAD=false
fi

# config to your needs, check for neccessary rights
env=''
server="saxid.zih.tu-dresden.de"
serverusr='root'
installpath="/srv/www/htdocs/saxid-ldap-proxy"$env
uploadfile="saxidprox_depl_tmp.tgz"
pathtosamlkey="/etc/apache2/ssl.key/saxid.zih.tu-dresden.de.nocrypt.key.pem"
pathtosamlcert="/etc/apache2/ssl.crt/saxid.zih.tu-dresden.de.pem"
mydatum=$(date +"%Y-%m-%d")
serverbkup=saxprox_$env_$mydatum.tar.bz2

echo "Creating Archive ..."
rm -f $uploadfile

### alternative: composer archive | gzip > $uploadfile
tar --exclude='.git' --exclude='*.DS_Store' -cf - . | gzip | pv > $uploadfile

du -h $uploadfile


if [ "$UPLOAD" = true ]; then
  echo "Deploying Archive to VM ..."
  echo "Backing up old Content on VM"
  echo "... creating backup"
  echo "... backing up into /$serverusr/: $serverbkup"

  if ssh -q $serverusr@${server} [ -f "/$serverusr/$serverbkup" ];
  then
  	echo "$serverbkup already exists. No backup needed!";
  else
  	ssh $serverusr@$server tar -cjf /$serverusr/$serverbkup $installpath;
  fi

  echo "... deleting old content on server"
  ssh $serverusr@$server rm -rf $installpath/*
  # -r recursive and -p preserve -> time...
  echo "... copy new stuff to server"
  scp $uploadfile $serverusr@$server:$installpath
  echo "Configuring VM ..."
  echo "... extracting"
  ssh $serverusr@$server tar xzf $installpath/$uploadfile -C $installpath
  echo "... removing Archive on Server"
  ssh $serverusr@$server rm -f $installpath/$uploadfile
  #echo "... removing .git"
  #ssh root@$server rm -rf $installpath/.git
  echo "... set owner to wwwrun:www"
  ssh $serverusr@$server chown -R wwwrun:www $installpath/
  ssh $serverusr@$server chmod u+x $installpath/app/console
  echo "... removing Logs and Cache"
  ssh $serverusr@$server rm -rf $installpath/app/logs/*
  ssh $serverusr@$server rm -rf $installpath/app/cache/*
  echo "... installing Server Certificate for SimpleSAML (taken from Apache-SSL)"
  #ssh root@$server mkdir $installpath/app/config/simplesamlphp/cert
  ssh $serverusr@$server cp -v $pathtosamlkey $installpath/app/config/simplesamlphp/cert/saml.pem.key
  ssh $serverusr@$server cp -v $pathtosamlcert $installpath/app/config/simplesamlphp/cert/saml.crt
  echo "... installing assets & clearing cache for prod,dev + setting user to wwwrun:www"
  ssh $serverusr@$server php $installpath/app/console assets:install --symlink -- $installpath/web
  ssh $serverusr@$server php $installpath/app/console cache:clear --env=prod
  ssh $serverusr@$server php $installpath/app/console cache:clear --env=dev
  ssh $serverusr@$server chown -R wwwrun:www $installpath/

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
