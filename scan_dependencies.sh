#!/bin/bash

# Locația codului sursă al aplicației
APP_PATH="/srv/http/tid4k/"

# Fișier pentru a stoca dependențele identificate
DEPENDENCIES_FILE="dependencies.txt"

# Funcție pentru a căuta dependențe PHP
function check_php() {
    echo "Checking for PHP..."
    if command -v php >/dev/null 2>&1; then
        php --version
    else
        echo "PHP is not installed."
    fi
}

# Funcție pentru a căuta biblioteci JavaScript
function check_javascript_libraries() {
    echo "Checking for JavaScript libraries..."
    grep -rhoP "src=.*\.js" $APP_PATH --include \*.html | grep -v 'http' | sort | uniq
}

# Funcție pentru a verifica MariaDB/MySQL
function check_mariadb() {
    echo "Checking MariaDB/MySQL..."
    if command -v mariadb >/dev/null 2>&1; then
        mariadb --version
    elif command -v mysql >/dev/null 2>&1; then
        mysql --version
    else
        echo "MariaDB/MySQL is not installed."
    fi
}

# Funcție pentru a verifica Apache
function check_apache() {
    echo "Checking Apache..."
    if command -v apache2 >/dev/null 2>&1; then
        apache2 -v
    elif command -v httpd >/dev/null 2>&1; then
        httpd -v
    else
        echo "Apache is not installed."
    fi
}

# Funcție pentru a verifica Imagick
function check_imagick() {
    echo "Checking for Imagick..."
    if php -m | grep -q 'imagick'; then
        echo "Imagick is installed for PHP."
    else
        echo "Imagick is not installed for PHP."
    fi
}

# Curățăm fișierul de dependențe existent
> $DEPENDENCIES_FILE

# Apelăm funcțiile de scanare și salvăm rezultatele
{
    check_php
    check_javascript_libraries
    check_mariadb
    check_apache
    check_imagick
} >> $DEPENDENCIES_FILE

# Afișăm dependențele identificate
cat $DEPENDENCIES_FILE
