apt update

# install php
apt -y install lsb-release apt-transport-https ca-certificates 
wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/php.list
apt update

apt -y install php7.4-fpm php7.4-cli php7.4-{bcmath,bz2,intl,gd,mbstring,zip,sqlite} nginx

cp /grocy/buildfiles/grocy.nginx /etc/nginx/sites-enabled/default

systemctl enable nginx.service
systemctl restart nginx.service