#!/bin/bash
# ================================================================
# GAMBADEN HOTSPOT — Portal Install Script
# Run this on Kali Linux: sudo bash install-portal.sh
# ================================================================

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}Installing GAMBADEN HOTSPOT Portal...${NC}"

# Install Apache + PHP
apt-get update -y
apt-get install -y apache2 php php-mysql libapache2-mod-php

# Enable rewrite
a2enmod rewrite

# Create portal directory
mkdir -p /var/www/html/portal

# Copy portal file + shared assets (Bootstrap theme CSS/JS)
cp index.php /var/www/html/portal/index.php
cp -r assets /var/www/html/portal/assets

# Set permissions
chown -R www-data:www-data /var/www/html/portal
chmod -R 755 /var/www/html/portal

# Configure Apache
cat > /etc/apache2/sites-available/gambaden.conf <<'APACHE'
<VirtualHost *:80>
    ServerName 172.16.0.50
    DocumentRoot /var/www/html/portal

    <Directory /var/www/html/portal>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gambaden_error.log
    CustomLog ${APACHE_LOG_DIR}/gambaden_access.log combined
</VirtualHost>
APACHE

a2ensite gambaden.conf
a2dissite 000-default.conf 2>/dev/null || true
systemctl restart apache2
systemctl enable apache2

echo -e "${GREEN}"
echo "╔══════════════════════════════════════════════════════╗"
echo "║     ✅ GAMBADEN HOTSPOT PORTAL INSTALLED!            ║"
echo "║                                                      ║"
echo "║  Portal URL: http://172.16.0.50/                     ║"
echo "║  Test URL:   http://172.16.0.50/                     ║"
echo "║                                                      ║"
echo "║  NOW CONFIGURE EAC200:                               ║"
echo "║  Auth Internet Access → Portal Auth → Local Portal   ║"
echo "║  Auth success URL: http://172.16.0.50/               ║"
echo "╚══════════════════════════════════════════════════════╝"
echo -e "${NC}"
