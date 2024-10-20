<?php

//acest cod creeaza tabelele corespunzatoarea grupei introduse (atentie la numele acesteia, spre exemplu : grupa_mijlocie); acest cod se ruleaza direc din linia de comanda : "php creare_structura_baza_date.php"
require_once 'config.php';

echo "Introduceți numele grupei: ";
$grupa = trim(fgets(STDIN));

$tabele = [
    // "copii" => "(
    //     id_copil int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    //     id_utilizator int(11) NOT NULL,
    //     nume_copil varchar(255),
    //     varsta_copil varchar(255),
    //     grupa_clasa_copil varchar(255),
    //     avatar_utilizator varchar(255),
    //     prezenta_stare varchar(10),
    //     prezenta_data date
    // )",
    "documente_".$grupa."_vizualizate" => "(
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_document int(11),
        id_utilizator int(11)
    )",
    "imagini_".$grupa."_vizualizate" => "(
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_imagine int(11),
        id_utilizator int(11)
    )",
    "informatii_".$grupa => "(
        id_info int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_utilizator int(11) NOT NULL,
        nume_fisier varchar(255),
        extensie varchar(255),
        tip_fisier varchar(255),
        continut longblob,
        data_upload timestamp DEFAULT CURRENT_TIMESTAMP,
        thumbnail mediumblob
    )",
    "mesaje_".$grupa => "(
        id_mesaj int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_expeditor int(11) NOT NULL,
        id_destinatar int(11) NOT NULL,
        mesaj varchar(255),
        data_trimitere datetime
    )",
    "mesaje_".$grupa."_vizualizate" => "(
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_mesaj int(11),
        id_utilizator int(11)
    )",
    "prezenta_".$grupa => "(
        id_prezenta int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_copil int(11) NOT NULL,
        data_prezenta date,
        stare_prezenta varchar(10)
    )",
    // "sesiuni_utilizatori" => "(
    //     id_sesiune int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    //     id_utilizator int(11) NOT NULL,
    //     creare_sesiune datetime DEFAULT CURRENT_TIMESTAMP,
    //     expirare_sesiune datetime DEFAULT CURRENT_TIMESTAMP,
    //     temp_path varchar(255),
    //     sesiune varchar(255)
    // )",
    // "utilizatori" => "(
    //     id_utilizator int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    //     nume_prenume varchar(255),
    //     telefon varchar(255),
    //     email varchar(255),
    //     id_cookie varchar(255) NOT NULL,
    //     status varchar(20) DEFAULT 'parinte',
    //     ultima_activitate timestamp
    // )",
];

foreach ($tabele as $nume_tabel => $structura) {
    $sql = "CREATE TABLE $nume_tabel $structura";
    if ($conn->query($sql) === TRUE) {
        echo "Tabela $nume_tabel a fost creata cu succes.\n";
    } else {
        echo "Eroare creare tabela: " . $conn->error . "\n";
    }
}

$conn->close();
?>
