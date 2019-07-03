#!/bin/bash
# Copyright 2018 Tobias Weber weber@lrz.de
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.

#sed -i 's#/var/www/html#/app/web#g' /etc/apache2/sites-available/000-default.conf
#sed -i 's#/var/www#/app/web#g' /etc/apache2/apache2.conf
a2enmod rewrite
chown -R www-data:www-data /var/www
rm -r /var/www/html
ln -s /var/www/web/ /var/www/html
cd /var/www
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install
/var/www/bin/console cache:clear --env=prod
/var/www/bin/console cache:warmup --env=prod
