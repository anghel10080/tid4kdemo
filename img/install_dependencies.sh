#!/bin/bash

# Testarea conexiunii la internet
echo "Verificarea conexiunii la internet..."
if ping -c 1 google.com &> /dev/null
then
    echo "Conexiunea la internet este activă. Continuăm instalarea."
else
    echo "Conexiunea la internet nu este disponibilă. Verificați conexiunea și încercați din nou."
    exit 1
fi
sleep 1

# Actualizarea listei de pachete și upgrade-ul sistemului
echo "Actualizarea sistemului..."
sudo pacman -Syu
echo "Sistemul a fost actualizat."
sleep 1

# Instalarea PHP și extensiilor necesare
echo "Instalarea PHP și extensiilor..."
sudo pacman -S php php-apache php-gd php-imagick
echo "PHP și extensiile necesare au fost instalate."
sleep 1

# Instalarea serverului web Apache
echo "Instalarea Apache..."
sudo pacman -S apache
echo "Apache a fost instalat."
sleep 1

# Activarea și pornirea serviciului Apache
echo "Activarea și pornirea serviciului Apache..."
sudo systemctl enable httpd
sudo systemctl start httpd
echo "Serviciul Apache a fost activat și pornit."
sleep 1

# Instalarea MariaDB (MySQL)
echo "Instalarea MariaDB..."
sudo pacman -S mariadb
echo "MariaDB a fost instalat."
sleep 1

# Inițializarea bazei de date MariaDB
echo "Inițializarea bazei de date MariaDB..."
sudo mysql_install_db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
echo "Baza de date MariaDB a fost inițializată."
sleep 1

# Activarea și pornirea serviciului MariaDB
echo "Activarea și pornirea serviciului MariaDB..."
sudo systemctl enable mariadb
sudo systemctl start mariadb
echo "Serviciul MariaDB a fost activat și pornit."
sleep 1

# Configurarea securității inițiale pentru MariaDB
echo "Execută 'sudo mysql_secure_installation' pentru a configura securitatea bazei de date MariaDB."

# Verificarea și crearea utilizatorului pentru baza de date
echo "Verificarea existenței utilizatorului pentru baza de date..."
EXISTA_UTILIZATOR=$(echo "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = 'id4k');" | sudo mysql -uroot -p)
if [ "$EXISTA_UTILIZATOR" -eq 0 ]; then
    echo "Crearea utilizatorului pentru baza de date..."
    sudo mysql -e "CREATE USER 'id4k'@'localhost' IDENTIFIED BY 'Infodisplay4K';"
    sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'id4k'@'localhost' WITH GRANT OPTION;"
    echo "Utilizatorul pentru baza de date a fost creat."
else
    echo "Utilizatorul pentru baza de date există deja."
fi
sleep 1

# Inițializarea structurii bazei de date
echo "Verificarea și inițializarea structurii bazei de date..."
sudo php /srv/http/tid4k/tabelele_tid4k.php
echo "Structura bazei de date a fost inițializată."
sleep 1

echo "Instalarea dependențelor a fost finalizată!"
