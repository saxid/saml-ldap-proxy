# SaxID-LDAP-Proxy #

Dieses Repository beinhaltet den Quellcode und Anleitungen zur Installation und Einrichtung eines LDAP-Proxies. Dieser agiert als Shibboleth ServiceProvider und schreibt Benutzerattribute in ein LDAP.

## Installation ##

Im folgenden wird als Hostsystem ein SLES 12.02 (x64) angenommen. Die Systemkonfiguration kann bei anderen Systemen entsprechend abweichen.

1. [Systemeinrichtung](#system)
1. [Installtion benötigter Pakete](#pakete)
1. [Apache-Konfiguration](#apache)
1. [LDAP-Konfiguration](#ldap)
1. [Installation PHP-Repository](#php)
1. [Optionale Installationen](#optional)
1. [Known Bugs](#bugs)

### <a name="system"></a>Systemeinrichtung ###

1. Wenn als VM
   1. Oracle [Virtual Box](https://www.virtualbox.org/) installieren
   1. SLES 12.02 installieren
   1. Netzwerk in Virtual Box einrichten (2 Adapter: 1x NAT, 1x Host only), dann in SLES einrichten (statische IP empfohlen)

### <a name="pakete"></a>Installation benötigter Pakete ###

1. Apache2 installieren: `zypper in apache2`
1. PHP7 installieren: `zypper in php7`
1. Apache2 als Service einrichten und Port(s) in Firewall freigeben
1. [`mod_rewrite` auf Apache2 aktivieren](http://askubuntu.com/questions/48362/how-to-enable-mod-rewrite-in-apache)
1. ZLIB-Library installieren: `zypper in php7-zlib`
1. intl-Library installieren: `zypper in php7-intl`
1. LDAP installieren: `zypper in openldap2`
1. OpenLDAP Pakete für PHP7 installieren: `zypper in php7-ldap`

### <a name="apache"></a>Apache-Konfiguration ###

Die Anpassungen an der Apache-Konfiguration befinden sich in der Datei
        /etc/apache2/httpd.conf.local

1. Virtual Host einrichten: die Environment-Variable `SIMPLESAMLPHP_CONFIG_DIR` mit Verweis auf die entsprechenden Konfigurationsdateien für SimpleSAMLphp ist obligatorisch.

        <FilesMatch '^\.[Dd][Ss]_[Ss]'>
        Require all denied
        </FilesMatch>

        TraceEnable off

        <IfDefine SSL>
        <IfDefine !NOSSL>

        <VirtualHost *:443>

        DocumentRoot /path/to/web
        Redirect permanent /path/to/web/web /path/to/web
        ServerAdmin server.admin@domain.tld
        ServerName saxid.zih.tu-dresden.de

        SSLEngine on
        SSLCertificateKeyFile /path/to/ssl/keyfile.pem
        SSLCertificateFile /path/to/ssl/certfile.pem
        SSLCertificateChainFile /path/to/ssl/chainfile.txt

        Alias /simplesamlphp /path/to/vendor/simplesamlphp/simplesamlphp/www

        SetEnv SIMPLESAMLPHP_CONFIG_DIR /path/to/app/config/simplesamlphp/config

        # HSTS (mod_headers is required) (15768000 seconds = 6 months)
        Header always set Strict-Transport-Security "max-age=15768000"

        # to avoid clickjacking
        Header always append X-Frame-Options SAMEORIGIN

        </VirtualHost>

        # modern configuration, tweak to your needs
        SSLProtocol             all -SSLv3 -TLSv1 -TLSv1.1
        SSLCipherSuite          ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA256
        SSLHonorCipherOrder     on
        SSLCompression          off

        # OCSP Stapling, only in httpd 2.3.3 and later
        SSLUseStapling          on
        SSLStaplingResponderTimeout 5
        SSLStaplingReturnResponderErrors off
        SSLStaplingCache        shmcb:/var/run/ocsp(128000)

        </IfDefine>
        </IfDefine>


#### Hinweise ####

* Apache2 benötigt für die `SetEnv`-Anweisung das Modul [`mod_env`](http://httpd.apache.org/docs/2.2/mod/mod_env.html).
* Alternativ zu der `Alias`-Anweisung kann natürlich auch ein Symlink (`ln -s`) gesetzt werden.

### <a name="ldap"></a>LDAP-Konfiguration ###

Es gibt Probleme bei der TLS-Verbindung. Um diese zu Umgehen benötigt der LDAP die Anweisung TLS_REQCERT=never (normalerweise auf 'demand')

1. /etc/openldap/ldap.conf

        host	localhost
        base	dc=sax-id,dc=de
        binddn	cn=readuser,dc=sax-id,dc=de

        TLS_REQCERT never
        TLS_CACERT /path/to/certs/ca_cert_chain.pem

Hinweis: Die LDIF-Dateien liegen im Repository-Folder `src/Saxid/SaxidLdapProxyBundle/Resources/schema`.

1. LDAP-Fremdschemata importieren ([mindestens `eduPerson`, `SCHAC`, `dfnEduPerson`, `nis`](https://www.aai.dfn.de/der-dienst/attribute/))

        ldapadd -Q -Y EXTERNAL -H ldapi:/// -W -f file.ldif

1. SaxID-Hochschulen (= LDAP-Organisationen) importieren

        ldapadd -Q -Y EXTERNAL -H ldapi:/// -W -f saxid-organisations.ldif

1. core-Schema um neue ObjektKlasse und neue Attribute erweitern :
{55}( 2.6.0.5 NAME ( 'lastAcademyUIDNumber' ) DESC 'The last used UIDNumber for a academy' SUP name )
{27}( 2.6.0.1 NAME 'saxID' DESC 'SaxID attributes' SUP top AUXILIARY MAY (uidNumberPrefix $ lastUserUIDNumber $ lastAcademyUIDNumber $ userServices))

        ldapadd -Q -Y EXTERNAL -H ldapi:/// -W -f new_attrs.ldif

### <a name="php"></a>Installation PHP-Repository ###

1. SaxID-LDAP-Proxy-Repository beziehen (z.B. gitlab@TUC) und installieren (`composer install`)
1. Shibboleth ServiceProvider beim lokalen Shibboleth IdP bekannt machen

# Cache

Prüfen bzw setzen, so dass der Cache vom Webserver beschreibbar ist!

    chown -R wwwrun:www app/cache

### <a name="optional"></a>Optionale Installationen ###

[Apache Directory Studio](https://directory.apache.org/studio/) installieren

# SSH-Zugang

Eigenes SSH-Zertifikat muss an Matthias Jurenz geschickt werden, um Zugriff auf die SaxID-LDAP-Proxy-VM zu erhalten sowie Info bzgl. Freischaltung IP

# Deployment

Ich (Moritz) habe stets lokal entwickelt, und dann per (P)SCP auf die VM übertragen. Hierfür habe ich 3 verschiedene Shell-Scripte verwendet:

* `deploy-vm.sh`: Deployment gesamter Folder
* `deploy-vm-src.sh`: Deployment nur `src/`-Folder
* `deploy-vm-config.sh`: Deployment nur `app/config/`-Folder

Ich (Jan) habe ein neues deploy-Script geschrieben, bei der das gesamte Verzeichnis vor der Übertragung komprimiert wird und auf dem Server wieder entpackt wird. Alternativ kann die git pull routine auf dem Server genutzt werden.

* `./deploy.sh true` : Deployment zipped (gz) Folder

# Composer

Paket- und Dependencymanagement für PHP. Ist auf VM unter `/opt/composer` derzeit nicht installiert. Nach clonen des git-Repositories müssen die externen Pakete mit `composer install` hinzugefügt werden.

# Cron-Job für Webserver zu Deprovisionierung einrichten

    crontab -e -u wwwrun
    # alle 8 Stunden Abgleich von LDAP mit SaxAPI
    1 */8 * * * php /path/to/your/webfolder/app/console --env=prod saxid_ldap_proxy:cleanup-users

# Assets

Roh-Assets sind in `src/Saxid/SaxidLdapProxyBundle/Resource/public` und werden per Befehl

    php app/console assets:install --symlink path_to_installfolder/web

in den Order `web/bundles/...` compiliert. Wenn die Assets ohne `--symlink` installiert werden, muss der Befehl nach jeder Änderung in `src/` aufgerufen werden.

### <a name="bugs"></a>Known Bugs ###

Das Anpassen des Knotens psr-4 mit "Checking composer.json: WARNING
Defining autoload.psr-4 with an empty namespace prefix is a bad idea for performance"

in der `composer.json`

führt zu einer ClassNotFoundException! Leave empty or try to fix this.
