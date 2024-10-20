<?php
// Conexiunea la baza de date
$servername = "localhost";
$username = "id4k";
$password = "Infodisplay4K";

// Crearea conexiunii
$conn = new mysqli($servername, $username, $password);

// Verificarea conexiunii
if ($conn->connect_error) {
    die("Conexiunea a eșuat: " . $conn->connect_error);
}

// Crearea bazei de date dacă nu există
$conn->query("CREATE DATABASE IF NOT EXISTS tid4k");
$conn->select_db("tid4k");

// Structura fiecărui tabel tip sablon
$tables = [
    'utilizatori' => "CREATE TABLE IF NOT EXISTS `utilizatori` (
                        `id_utilizator` int(11) NOT NULL AUTO_INCREMENT,
                        `nume_prenume` varchar(255) DEFAULT NULL,
                        `telefon` varchar(255) DEFAULT NULL,
                        `email` varchar(255) DEFAULT NULL,
                        `id_cookie` varchar(255) DEFAULT NULL,
                        `status` varchar(20) DEFAULT 'parinte',
                        `ultima_activitate` timestamp NULL DEFAULT NULL,
                        `temp_path` varchar(255) DEFAULT NULL,
                        `CANCELARIE` tinyint(1) DEFAULT 0,
                        `ADMINISTRATIV` tinyint(1) DEFAULT 0,
                        PRIMARY KEY (`id_utilizator`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'copii' => "CREATE TABLE IF NOT EXISTS `copii` (
                  `id_copil` int(11) NOT NULL AUTO_INCREMENT,
                  `id_utilizator` int(11) NOT NULL,
                  `nume_copil` varchar(255) DEFAULT NULL,
                  `varsta_copil` varchar(255) DEFAULT NULL,
                  `grupa_clasa_copil` varchar(255) DEFAULT NULL,
                  `avatar_utilizator` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`id_copil`),
                  KEY `id_utilizator` (`id_utilizator`),
                  KEY `grupa_clasa_copil` (`grupa_clasa_copil`),
                  CONSTRAINT `copii_ibfk_1` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'asociere_multipla' => "CREATE TABLE IF NOT EXISTS `asociere_multipla` (
                              `id_asociativ` int(11) NOT NULL AUTO_INCREMENT,
                              `id_utilizator` int(11) DEFAULT NULL,
                              `id_copil` int(11) DEFAULT NULL,
                              `grupa_clasa_copil` varchar(255) DEFAULT NULL,
                              `index_grupa_clasa_curenta` int(11) DEFAULT NULL,
                              `numar_grupe_clase_utilizator` int(11) DEFAULT NULL,
                              PRIMARY KEY (`id_asociativ`),
                              KEY `id_utilizator` (`id_utilizator`),
                              KEY `id_copil` (`id_copil`),
                              KEY `asociere_multipla_ibfk_3` (`grupa_clasa_copil`),
                              CONSTRAINT `asociere_multipla_ibfk_1` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`),
                              CONSTRAINT `asociere_multipla_ibfk_2` FOREIGN KEY (`id_copil`) REFERENCES `copii` (`id_copil`),
                              CONSTRAINT `asociere_multipla_ibfk_3` FOREIGN KEY (`grupa_clasa_copil`) REFERENCES `copii` (`grupa_clasa_copil`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    'sesiuni_utilizatori' => "CREATE TABLE IF NOT EXISTS `sesiuni_utilizatori` (
                                `id_sesiune` int(11) NOT NULL AUTO_INCREMENT,
                                `id_utilizator` int(11) NOT NULL,
                                `inceput_sesiune` datetime DEFAULT NULL,
                                `dispozitiv` varchar(255) DEFAULT NULL,
                                PRIMARY KEY (`id_sesiune`)
                              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'contributia_grupa_mica' => "CREATE TABLE IF NOT EXISTS `contributia_grupa_mica` (
                                    `id` int(11) NOT NULL AUTO_INCREMENT,
                                    `id_copil` int(11) DEFAULT NULL,
                                    `luna` varchar(20) DEFAULT NULL,
                                    `numar_prezente` int(11) DEFAULT NULL,
                                    `contributia_stabilita` int(11) DEFAULT NULL,
                                    `contributia` int(11) DEFAULT NULL,
                                    `contributia_platita` int(11) DEFAULT NULL,
                                    `numar_chitanta` varchar(50) DEFAULT NULL,
                                    `data_platii` timestamp NULL DEFAULT current_timestamp(),
                                    `diferenta_contributie` int(11) DEFAULT 0,
                                    PRIMARY KEY (`id`),
                                    UNIQUE KEY `id_copil` (`id_copil`,`luna`),
                                    CONSTRAINT `contributia_grupa_mica_ibfk_1` FOREIGN KEY (`id_copil`) REFERENCES `copii` (`id_copil`)
                                  ) ENGINE=InnoDB AUTO_INCREMENT=141909 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
 'documente_grupa_mica_vizualizate' => "CREATE TABLE IF NOT EXISTS `documente_grupa_mica_vizualizate` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_document` int(11) DEFAULT NULL,
        `id_utilizator` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `id_info` (`id_document`),
        KEY `id_utilizator` (`id_utilizator`),
        CONSTRAINT `documente_grupa_mica_vizualizate_ibfk_1` FOREIGN KEY (`id_document`) REFERENCES `informatii_grupa_mica` (`id_info`),
        CONSTRAINT `documente_grupa_mica_vizualizate_ibfk_2` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`)
    ) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
'imagini_grupa_mica_vizualizate' => "CREATE TABLE IF NOT EXISTS `imagini_grupa_mica_vizualizate` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `id_imagine` int(11) DEFAULT NULL,
                                        `id_utilizator` int(11) DEFAULT NULL,
                                        PRIMARY KEY (`id`),
                                        KEY `id_info` (`id_imagine`),
                                        KEY `id_utilizator` (`id_utilizator`),
                                        CONSTRAINT `imagini_grupa_mica_vizualizate_ibfk_1` FOREIGN KEY (`id_imagine`) REFERENCES `informatii_grupa_mica` (`id_info`),
                                        CONSTRAINT `imagini_grupa_mica_vizualizate_ibfk_2` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`)
                                      ) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
'informatii_grupa_mica' => "CREATE TABLE IF NOT EXISTS `informatii_grupa_mica` (
                                `id_info` int(11) NOT NULL AUTO_INCREMENT,
                                `id_utilizator` int(11) NOT NULL,
                                `nume_fisier` varchar(255) DEFAULT NULL,
                                `extensie` varchar(255) DEFAULT NULL,
                                `tip_fisier` varchar(255) DEFAULT NULL,
                                `continut` longblob DEFAULT NULL,
                                `data_upload` timestamp NULL DEFAULT current_timestamp(),
                                `thumbnail` mediumblob DEFAULT NULL,
                                `afisat` tinyint(1) DEFAULT 0,
                                PRIMARY KEY (`id_info`),
                                KEY `id_utilizator` (`id_utilizator`),
                                CONSTRAINT `informatii_grupa_mica_ibfk_1` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`)
                              ) ENGINE=InnoDB AUTO_INCREMENT=420 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
'mesaje_grupa_mica' => "CREATE TABLE IF NOT EXISTS `mesaje_grupa_mica` (
                           `id_mesaj` int(11) NOT NULL AUTO_INCREMENT,
                           `id_expeditor` int(11) NOT NULL,
                           `id_destinatar` int(11) NOT NULL,
                           `mesaj` varchar(255) DEFAULT NULL,
                           `data_trimitere` datetime DEFAULT NULL,
                           `citit` timestamp NULL DEFAULT NULL,
                           PRIMARY KEY (`id_mesaj`),
                           KEY `id_expeditor` (`id_expeditor`),
                           KEY `fk_mesaje_grupa_mica_utilizatori` (`id_destinatar`),
                           CONSTRAINT `fk_mesaje_grupa_mica_utilizatori` FOREIGN KEY (`id_destinatar`) REFERENCES `utilizatori` (`id_utilizator`),
                           CONSTRAINT `mesaje_grupa_mica_ibfk_1` FOREIGN KEY (`id_expeditor`) REFERENCES `utilizatori` (`id_utilizator`),
                           CONSTRAINT `mesaje_grupa_mica_ibfk_2` FOREIGN KEY (`id_destinatar`) REFERENCES `utilizatori` (`id_utilizator`)
                         ) ENGINE=InnoDB AUTO_INCREMENT=325 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
'mesaje_grupa_mica_vizualizate' => "CREATE TABLE IF NOT EXISTS `mesaje_grupa_mica_vizualizate` (
                                        `id` int(11) NOT NULL AUTO_INCREMENT,
                                        `id_mesaj` int(11) DEFAULT NULL,
                                        `id_utilizator` int(11) DEFAULT NULL,
                                        PRIMARY KEY (`id`),
                                        KEY `id_mesaj` (`id_mesaj`),
                                        KEY `id_utilizator` (`id_utilizator`),
                                        CONSTRAINT `mesaje_grupa_mica_vizualizate_ibfk_1` FOREIGN KEY (`id_mesaj`) REFERENCES `mesaje_grupa_mica` (`id_mesaj`),
                                        CONSTRAINT `mesaje_grupa_mica_vizualizate_ibfk_2` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`)
                                      ) ENGINE=InnoDB AUTO_INCREMENT=272 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
'prezenta_grupa_mica' => "CREATE TABLE IF NOT EXISTS `prezenta_grupa_mica` (
                              `id_prezenta` int(11) NOT NULL AUTO_INCREMENT,
                              `id_copil` int(11) DEFAULT NULL,
                              `nume_copil` varchar(255) DEFAULT NULL,
                              `prezenta_stare` enum('prezent','absent') DEFAULT 'absent',
                              `prezenta_data` timestamp NULL DEFAULT current_timestamp(),
                              `confirmata_la` time DEFAULT NULL,
                              `confirmata_de` varchar(255) DEFAULT NULL,
                              `confirmata_parinte` tinyint(1) DEFAULT 0,
                              `contributia_stabilita` int(11) DEFAULT 25,
                              PRIMARY KEY (`id_prezenta`),
                              UNIQUE KEY `copil_data_uniq` (`id_copil`,`prezenta_data`),
                              CONSTRAINT `prezenta_grupa_mica_ibfk_1` FOREIGN KEY (`id_copil`) REFERENCES `copii` (`id_copil`) ON DELETE CASCADE
                            ) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

foreach ($tables as $tableName => $createQuery) {
    $conn->query($createQuery);
    echo "Tabelul '$tableName' a fost verificat/creat.\n";
}

// Șabloane pentru categorii de tabele
$sabloane = [
    'contributia_' => 'contributia_grupa_mica',
    'documente_' => 'documente_grupa_mica_vizualizate',
    'imagini_' => 'imagini_grupa_mica_vizualizate',
    'informatii_' => 'informatii_grupa_mica',
    'mesaje_' => 'mesaje_grupa_mica',
    'mesaje_vizualizate_' => 'mesaje_grupa_mica_vizualizate',
    'prezenta_' => 'prezenta_grupa_mica'
];

// Lista tuturor tabelelor care trebuie create, folosind șabloanele
$titluri_tabele = [
    // Tabelele cu prefix "contributia_"
    'contributia_clasa_pregatitoare', 'contributia_grupa_mijlocie', 'contributia_grupa_mare','contributia_clasa_I', 'contributia_clasa_II', 'contributia_clasa_III', 'contributia_clasa_IV', 'contributia_clasa_V', 'contributia_clasa_VI', 'contributia_clasa_VII', 'contributia_clasa_VIII', 'contributia_clasa_IX', 'contributia_clasa_X', 'contributia_clasa_XI', 'contributia_clasa_XII', //toate clasele necesare mai putin sablonul "contributia_grupa_mica"
    // Tabelele cu prefix "documente_"
    'documente_grupa_mijlocie_vizualizate', 'documente_grupa_mare_vizualizate', 'documente_clasa_pregatitoare_vizualizate', 'documente_clasa_I_vizualizate', 'documente_clasa_II_vizualizate', 'documente_clasa_III_vizualizate', 'documente_clasa_IV_vizualizate', 'documente_clasa_V_vizualizate', 'documente_clasa_VI_vizualizate', 'documente_clasa_VII_vizualizate', 'documente_clasa_VIII_vizualizate', 'documente_clasa_IX_vizualizate', 'documente_clasa_X_vizualizate', 'documente_clasa_XI_vizualizate', 'documente_clasa_XII_vizualizate', // toate clasele mai putin sablonul "documente_grupa_mica_vizualizate"
     // Tabelele cu prefix "imagini_"
    'imagini_clasa_I_vizualizate',
    'imagini_grupa_mijlocie_vizualizate', 'imagini_grupa_mare_vizualizate', 'imagini_clasa_pregatitoare_vizualizate', 'imagini_clasa_I_vizualizate', 'imagini_clasa_II_vizualizate', 'imagini_clasa_III_vizualizate', 'imagini_clasa_IV_vizualizate', 'imagini_clasa_V_vizualizate', 'imagini_clasa_VI_vizualizate', 'imagini_clasa_VII_vizualizate', 'imagini_clasa_VIII_vizualizate', 'imagini_clasa_IX_vizualizate', 'imagini_clasa_X_vizualizate', 'imagini_clasa_XI_vizualizate', 'imagini_clasa_XII_vizualizate', // toate clasele mai putin sablonul "imagini_grupa_mica_vizualizate"
     // Tabelele cu prefix "informatii_"
    'informatii_clasa_I',
    'informatii_grupa_mijlocie', 'informatii_grupa_mare', 'informatii_clasa_pregatitoare', 'informatii_clasa_I', 'informatii_clasa_II', 'informatii_clasa_III', 'informatii_clasa_IV', 'informatii_clasa_V', 'informatii_clasa_VI', 'informatii_clasa_VII', 'informatii_clasa_VIII', 'informatii_clasa_IX', 'informatii_clasa_X', 'informatii_clasa_XI', 'informatii_clasa_XII'
    , // toate clasele mai putin sablonul "informatii_grupa_mica"
     // Tabelele cu prefix "mesaje_ ..._vizualizate"
    'mesaje_clasa_I_vizualizate',
    'mesaje_grupa_mijlocie_vizualizate', 'mesaje_grupa_mare_vizualizate', 'mesaje_clasa_pregatitoare_vizualizate', 'mesaje_clasa_I_vizualizate', 'mesaje_clasa_II_vizualizate', 'mesaje_clasa_III_vizualizate', 'mesaje_clasa_IV_vizualizate', 'mesaje_clasa_V_vizualizate', 'mesaje_clasa_VI_vizualizate', 'mesaje_clasa_VII_vizualizate', 'mesaje_clasa_VIII_vizualizate', 'mesaje_clasa_IX_vizualizate', 'mesaje_clasa_X_vizualizate', 'mesaje_clasa_XI_vizualizate', 'mesaje_clasa_XII_vizualizate', // toate clasele mai putin sablonul "mesaje_grupa_mica_vizualizate"
     // Tabelele cu prefix "mesaje_"
    'mesaje_clasa_I',
    'mesaje_grupa_mijlocie', 'mesaje_grupa_mare', 'mesaje_clasa_pregatitoare', 'mesaje_clasa_I', 'mesaje_clasa_II', 'mesaje_clasa_III', 'mesaje_clasa_IV', 'mesaje_clasa_V', 'mesaje_clasa_VI', 'mesaje_clasa_VII', 'mesaje_clasa_VIII', 'mesaje_clasa_IX', // // toate clasele mai putin sablonul "mesaje_grupa_mica"
     // Tabelele cu prefix "prezenta_"
    'prezenta_clasa_I',
    'prezenta_grupa_mijlocie', 'prezenta_grupa_mare', 'prezenta_clasa_pregatitoare', 'prezenta_clasa_I', 'prezenta_clasa_II', 'prezenta_clasa_III', 'prezenta_clasa_IV', 'prezenta_clasa_V', 'prezenta_clasa_VI', 'prezenta_clasa_VII', 'prezenta_clasa_VIII', 'prezenta_clasa_IX', // // toate clasele mai putin sablonul "mesaje_grupa_mica"
];

// Crearea tabelelor folosind șabloanele
foreach ($titluri_tabele as $titlu_tabel) {
    foreach ($sabloane as $prefix => $sablon) {
        if (strpos($titlu_tabel, $prefix) === 0) {
            $createQuery = "CREATE TABLE IF NOT EXISTS `$titlu_tabel` LIKE `$sablon`";
            $conn->query($createQuery);
            echo "Tabelul '$titlu_tabel' a fost creat folosind șablonul '$sablon'.\n";
        }
    }
}

$conn->close();
?>
