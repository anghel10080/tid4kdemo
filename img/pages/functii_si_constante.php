<?php

//verifica daca sesiunea este pornita
if (session_status() == PHP_SESSION_NONE) {
    if (isset($_SESSION['id_cookie'])) {
        session_start();
    }
}

//cere credentialele pentru baza de date si sesiuni (pentru id_utilizator curent)
require_once(ROOT_PATH . 'config.php');
require_once(ROOT_PATH . 'sesiuni.php');
// require_once '../config.php';
// require_once '../sesiuni.php';

//functia care extrage grupele si/sau clasele disponibile din baza de date
function GrupeClaseDisponibile($returnArrays = false) {
    global $conn, $database;
    $grupeOrder = ['grupa mica', 'grupa mijlocie', 'grupa mare'];
    $claseOrder = ['clasa I', 'clasa II', 'clasa III', 'clasa IV', 'clasa V', 'clasa VI', 'clasa VII', 'clasa VIII', 'clasa IX', 'clasa X', 'clasa XI', 'clasa XII'];

    $sql = "SHOW TABLES WHERE Tables_in_$database LIKE 'informatii_%'";
    $result = mysqli_query($conn, $sql);

    $grupe = [];
    $clase = [];

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_row($result)) {
            $tableName = $row[0];
            $name = substr($tableName, 10); // Remove 'informatii_'
            $formattedName = str_replace('_', ' ', $name);
            if (strpos($name, 'grupa') !== false) {
                $grupe[] = $formattedName;
            } elseif (strpos($name, 'clasa') !== false) {
                $clase[] = $formattedName;
            }
        }
    }

    // Functia de sortare inclusa direct in corpul functiei GrupeClaseDisponibile
    $sortWithSuffix = function($a, $b, $order) {
        $indexA = $indexB = PHP_INT_MAX;
        foreach ($order as $key => $value) {
            if (strpos($a, $value) === 0) {
                $indexA = $key;
                break;
            }
            if (strpos($b, $value) === 0) {
                $indexB = $key;
                break;
            }
        }
        return $indexA <=> $indexB;
    };

    usort($grupe, function($a, $b) use ($grupeOrder, $sortWithSuffix) {
        return $sortWithSuffix($a, $b, $grupeOrder);
    });

    usort($clase, function($a, $b) use ($claseOrder, $sortWithSuffix) {
        return $sortWithSuffix($a, $b, $claseOrder);
    });

    if ($returnArrays) {
        return ['grupe' => $grupe, 'clase' => $clase];
    } else {
        $options = array_merge($grupe, $clase);

        foreach ($options as $option) {
            echo "<option value='" . trim(htmlspecialchars($option)) . "'>" . trim(htmlspecialchars($option)) . "</option>";
        }
    }

    mysqli_close($conn);
}//aici se termina functia GrupeClaseDisponibile

//functia care stabileste valorile disponibile pentru informatia din cadran_simulat ID4K
function cadran_simulatValoriVariabile() {
    global $conn;
    // Lista valorilor statice
    $lista_valori_variabile_cadran_simulat = ['Meniul Saptamanii', 'Activitati', 'Administrative', 'Ministerul Educatiei', 'doar imagini', 'doar documente', 'Prezenta'];
    $lista_obligatorie_de_afisat_cadran_simulat = ['Meniul Saptamanii', 'Activitati', 'Administrative', 'Ministerul Educatiei'];

    // Apelăm funcția GrupeClaseDisponibile pentru a obține listele de grupe și clase disponibile
    $listele_disponibile = GrupeClaseDisponibile(true); // Presupunem că aceasta returnează un array cu cheile 'grupe' și 'clase'

    // Combinație într-un array asociativ pentru a returna toate listele
    return [
        'lista_valori_variabile_cadran_simulat' => $lista_valori_variabile_cadran_simulat,
        'lista_obligatorie_de_afisat_cadran_simulat' => $lista_obligatorie_de_afisat_cadran_simulat,
        'lista_grupe_disponibile' => $listele_disponibile['grupe'], // Utilizăm lista de grupe returnată de GrupeClaseDisponibile
        'lista_clase_disponibile' => $listele_disponibile['clase'], // Utilizăm lista de clase returnată de GrupeClaseDisponibile
        'surse_php' => [
            'Meniul Saptamanii' => '/pages/documentele_noastre/fetch_meniuri.php?source=infodisplay',
            'Activitati' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay',
            'doar imagini' => '/pages/fetch_images.php?source=infodisplay',
            'Activitatile noastre' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay',
            'doar documente' => '/pages/fetch_iframes.php?source=infodisplay',
            'Administrative' => '/pages/documentele_noastre/fetch_administrativ.php?source=infodisplay',
            'grupa mica A' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mica_A',
            'grupa mica B' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mica_B',
            'grupa mica C' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mica_C',
            'grupa mijlocie A' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mijlocie_A',
            'grupa mijlocie B' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mijlocie_B',
            'grupa mijlocie C' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mijlocie_C',
            'grupa mare A' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mare_A',
            'grupa mare B' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mare_B',
            'grupa mare C' => '/pages/documentele_noastre/fetch_files.php?source=infodisplay&optiuneSelectata=grupa_mare_C',
        ]
    ];
}// aici se termina functia cadran_simulatValoriVariabile

//functia care permite asocierea multipla profesori, administrativ la copii
function asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil) {
$listaProfesori = [];
$listaProfesoriEligibili =[];
$listaPersonalAdministrativ = [];
$listaCopii = [];
$listaCopiiEligibili = [];
$status = $_POST['status'];

if ($status === 'parinte') {
    $id_copil = $id_copil_curent;

    if (isset($_POST['grupa_clasa_copil'])) {
        $grupa_clasa_copil = $_POST['grupa_clasa_copil'];

        // Extragem lista profesorilor
       $sqlProfesori = "SELECT DISTINCT u.id_utilizator
                 FROM utilizatori u
                 JOIN asociere_multipla a ON u.id_utilizator = a.id_utilizator
                 WHERE a.grupa_clasa_copil = '$grupa_clasa_copil' AND u.status = 'profesor'";

        $resultProfesori = mysqli_query($conn, $sqlProfesori);
        $listaProfesori = [];
        while ($row = mysqli_fetch_assoc($resultProfesori)) {
            $listaProfesori[] = $row['id_utilizator'];
        }

        // Extragem lista personalului administrativ
        $sqlAdministrativ = "SELECT DISTINCT id_utilizator FROM utilizatori WHERE status IN ('director', 'administrator', 'secretara', 'contabil')";
        $resultAdministrativ = mysqli_query($conn, $sqlAdministrativ);
        $listaPersonalAdministrativ = [];
        while ($row = mysqli_fetch_assoc($resultAdministrativ)) {
            $listaPersonalAdministrativ[] = $row['id_utilizator'];
        }
    }
} elseif (!isset($id_copil_curent) && isset($_POST['grupa_clasa_copil'])) {
    $grupa_clasa_copil = $_POST['grupa_clasa_copil'];

    // Extragem id-urilor copiilor
    $sqlCopii = "SELECT DISTINCT id_copil FROM copii WHERE grupa_clasa_copil = '$grupa_clasa_copil'";
    $resultCopii = mysqli_query($conn, $sqlCopii);
    while ($row = mysqli_fetch_assoc($resultCopii)) {
        $listaCopii[] = $row['id_copil'];
    }
}

if ($status === 'parinte') {
      // Filtrarea profesorilor eligibili
    $listaProfesoriEligibili = [];
    if (isset($listaProfesori) && !empty($listaProfesori)) {
    foreach ($listaProfesori as $id_utilizator) {
        $sqlCheck = "SELECT * FROM asociere_multipla WHERE id_utilizator = '$id_utilizator' AND id_copil = '$id_copil'";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        if (mysqli_num_rows($resultCheck) == 0) {
            $listaProfesoriEligibili[] = $id_utilizator;
        }
    }

    // Codul de inserare pentru profesori eligibili
    foreach ($listaProfesoriEligibili as $id_utilizator) {
        $sqlInsert = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES ('$id_copil', '$id_utilizator', '$grupa_clasa_copil')";
        mysqli_query($conn, $sqlInsert);
    }
    }
    // Filtrarea personalului administrativ eligibil
    $listaPersonalAdministrativEligibil = [];
    if (isset($listaPersonalAdministrativ) && !empty($listaPersonalAdministrativ)) {
    foreach ($listaPersonalAdministrativ as $id_utilizator) {
        $sqlCheck = "SELECT * FROM asociere_multipla WHERE id_utilizator = '$id_utilizator' AND id_copil = '$id_copil'";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        if (mysqli_num_rows($resultCheck) == 0) {
            $listaPersonalAdministrativEligibil[] = $id_utilizator;
        }
    }

    // Codul de inserare pentru personalul administrativ eligibil
    foreach ($listaPersonalAdministrativEligibil as $id_utilizator) {
        $sqlInsert = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES ('$id_copil', '$id_utilizator', '$grupa_clasa_copil')";
        mysqli_query($conn, $sqlInsert);
    }
    }
}elseif ($status === 'profesor') {
    // Filtrarea copiilor eligibili
    $listaCopiiEligibili = [];
    if (isset($listaCopii) && !empty($listaCopii)) {
    foreach ($listaCopii as $id_copil) {
        $sqlCheck = "SELECT * FROM asociere_multipla WHERE id_utilizator = '$id_utilizator_curent' AND id_copil = '$id_copil' AND grupa_clasa_copil = '$grupa_clasa_copil'";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        if (mysqli_num_rows($resultCheck) == 0) {
            $listaCopiiEligibili[] = $id_copil;
        }
    }

    // Codul de inserare pentru copii eligibili
    foreach ($listaCopiiEligibili as $id_copil) {
        $sqlInsert = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES ('$id_copil', '$id_utilizator_curent', '$grupa_clasa_copil')";
        if (mysqli_query($conn, $sqlInsert)) {
            echo "Inserare reușită pentru copilul cu ID: $id_copil";
        } else {
            echo "Eroare la inserare: " . mysqli_error($conn);
        }
    }
    }
} elseif (in_array($status, ['director', 'administrator', 'secretara', 'contabil'])) {
  foreach ($listaCopii as $id_copil) {
        // Filtrarea copiilor eligibili
    $listaCopiiEligibili = [];
    if (isset($listaCopii) && !empty($listaCopii)) {
    foreach ($listaCopii as $id_copil) {
        $sqlCheck = "SELECT * FROM asociere_multipla WHERE id_utilizator = '$id_utilizator_curent' AND id_copil = '$id_copil' AND grupa_clasa_copil = '$grupa_clasa_copil'";
        $resultCheck = mysqli_query($conn, $sqlCheck);
        if (mysqli_num_rows($resultCheck) == 0) {
            $listaCopiiEligibili[] = $id_copil;
        }
    }

    // Codul de inserare pentru copii eligibili
    foreach ($listaCopiiEligibili as $id_copil) {
        $sqlInsert = "INSERT INTO asociere_multipla (id_copil, id_utilizator, grupa_clasa_copil) VALUES ('$id_copil', '$id_utilizator_curent', '$grupa_clasa_copil')";
        if (mysqli_query($conn, $sqlInsert)) {
            echo "Inserare reușită pentru copilul cu ID: $id_copil";
        } else {
            echo "Eroare la inserare: " . mysqli_error($conn);
        }
    }
    }
    }
}//aici se termina codul pentru asociere_multipla
}


//functia care determina id_utilizator, status si grupa_clasa_copil la care este asociat
function determina_variabile_utilizator(&$conn) {
    // Inițializăm $status dacă nu este deja setat în sesiune
if (!isset($_SESSION['status'])) {
    $sql = "SELECT status FROM utilizatori WHERE id_utilizator = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id_utilizator']);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();
    $_SESSION['status'] = $status;
} else {
    $status = $_SESSION['status'];
}

    // Verifică dacă id_utilizator este setat în sesiune
    if (!isset($_SESSION['id_utilizator'])) {
        return false;
    }

    // Interogare SQL pentru a obține detalii despre utilizator și copilul asociat
     if ($status == 'parinte') {
    $sql = "SELECT u.id_utilizator, u.id_cookie, u.status, u.nume_prenume, u.ultima_activitate, c.grupa_clasa_copil
            FROM utilizatori u
            JOIN copii c ON u.id_utilizator = c.id_utilizator
            WHERE u.id_utilizator = ?";
     } else if ($status != 'parinte') {
        $sql = "SELECT u.id_utilizator, u.id_cookie, u.status, u.nume_prenume, u.ultima_activitate, a.grupa_clasa_copil
                FROM utilizatori u
                JOIN asociere_multipla a ON u.id_utilizator = a.id_utilizator
                WHERE u.id_utilizator = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_utilizator']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Extrage și stochează variabilele în sesiune
    $_SESSION['id_utilizator'] = $row['id_utilizator'];
    $_SESSION['id_cookie'] = $row['id_cookie'];
    $_SESSION['status'] = $row['status'];
    $_SESSION['nume_prenume_curent'] = $row['nume_prenume'];
    $_SESSION['ultima_activitate_curent'] = $row['ultima_activitate'];
    $_SESSION['grupa_clasa_copil'] = $row['grupa_clasa_copil'];
    $_SESSION['grupa_clasa_copil_'] = str_replace(' ', '_', $row['grupa_clasa_copil']);

    // Verifica daca este utilizator multiplu  (de exemplu, poate să fie unul din 'director', 'administrator', 'secretara', 'contabil')
// Interogare pentru a obține $numar_grupe_clase_utilizator și $index_grupa_clasa_curenta
$sql = "SELECT numar_grupe_clase_utilizator, index_grupa_clasa_curenta FROM asociere_multipla WHERE id_utilizator = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id_utilizator']);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $numar_grupe_clase_utilizator = $row['numar_grupe_clase_utilizator'];
        $_SESSION['numar_grupe_clase_utilizator'] = $numar_grupe_clase_utilizator;
        $_SESSION['index_grupa_clasa_curenta'] = $row['index_grupa_clasa_curenta'];
    } else {
        // Dacă nu sunt rânduri găsite, setează valorile la 1 și 0, respectiv
        $numar_grupe_clase_utilizator = 1;
        $_SESSION['numar_grupe_clase_utilizator'] = $numar_grupe_clase_utilizator;
        $_SESSION['index_grupa_clasa_curenta'] = 0;
    }
    $stmt->close();
} else {
    echo "Eroare la interogarea numar_grupe_clase_utilizator și index_grupa_clasa_curenta.";
    $stmt->close();
}

// Logica în funcție de $numar_grupe_clase_utilizator
if ($numar_grupe_clase_utilizator > 1) {
        // Dacă nu, obține toate numele de grupe disponibile și le stochează într-un array
        $sql = "SELECT DISTINCT grupa_clasa_copil FROM asociere_multipla WHERE id_utilizator = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['id_utilizator']);
        $stmt->execute();
        $result = $stmt->get_result();

        $_SESSION['toate_grupele_clase'] = array(); // Un array pentru toate grupele și clasele
        while ($subRow = $result->fetch_assoc()) {
            $_SESSION['toate_grupele_clase'][] = $subRow['grupa_clasa_copil'];
        }
        $stmt->close();

    // Setează grupa/clasa curentă în funcție de index
    $_SESSION['grupa_clasa_copil'] = $_SESSION['toate_grupele_clase'][$_SESSION['index_grupa_clasa_curenta']];
    $_SESSION['grupa_clasa_copil_'] = str_replace(' ', '_', $_SESSION['grupa_clasa_copil']);
} else {
    // Pentru utilizatori care nu sunt multipli valorile grupa_clasa_copil sunt deja setate mai sus in grupul variabilelor de sesiune $_SESSION[]
        $numar_grupe_clase_utilizator = 1;
        $_SESSION['numar_grupe_clase_utilizator'] = $numar_grupe_clase_utilizator;
        $_SESSION['index_grupa_clasa_curenta'] = 0;
        $_SESSION['grupa_clasa_copil'] = $_SESSION['grupa_clasa_copil'];
        $_SESSION['grupa_clasa_copil_'] = str_replace(' ', '_', $_SESSION['grupa_clasa_copil']);
}

       // stabileste daca CANCELARIE va fi afisat in pagina de intampinare deasupra titlului Grupei/Clasei
    if ($_SESSION['status'] !== 'parinte' && $_SESSION['status'] !== 'elev') {
        $_SESSION['afiseaza_cancelarie'] = TRUE;
    } else {
        $_SESSION['afiseaza_cancelarie'] = FALSE;
    }

    return true;
}

// Funcție pentru a calcula numărul de sâmbete și duminici într-o lună dată
function numarDeWeekenduri() {
    $nr_weekenduri = 0;
    $total_zile = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'));
    for ($zi = 1; $zi <= $total_zile; $zi++) {
        $data = date("w", mktime(0, 0, 0, date('n'), $zi, date('Y')));
        if ($data == 0 || $data == 6) {
            $nr_weekenduri++;
        }
    }
    return $nr_weekenduri;
}

// Funcție pentru a calcula numărul de zile lucrătoare într-o lună, tinand cont de numarul de nr_weekenduri, sarbatorile legale si zilele de vacanta
function zileLucratoareDinLuna() {
    $total_zile = cal_days_in_month(CAL_GREGORIAN, date('n'), date('Y'));
    $sarbatori_legale = [
        '1' => 4,  // Ianuarie: Anul Nou (1,2), Boboteaza, Ziua Unirii
        '2' => 0,
        '3' => 0,
        '4' => 2,  // Aprilie: Paști (2 zile, poate varia)
        '5' => 1,  // Mai: Ziua Muncii (1)
        '6' => 1, // 1 Iunie , Ziua Copilului
        '7' => 0,
        '8' => 1,  // August: Sfânta Maria (15)
        '9' => 6, // Scoala incepe din 11 septembrie 2023
        '10'=> 3, // sfarsitul primului modul de scoala; 2 zile de vacanta in octombrie 2023, Ziua Educatiei
        '11'=> 3, // sfarsitul primului modul de scoala; 3 zile de vacanta in noiembrie 2023
        '12'=> 5, //  Crăciunul (25,26), dupa Craciun (27,28,29)
        // Puteți adăuga alte sărbători sau ajusta conform necesităților
    ];
    $sarbatori_in_luna = isset($sarbatori_legale[date('n')]) ?
    $sarbatori_legale[date('n')] : 0;
    $zile_weekend = numarDeWeekenduri(date('n'), date('Y'));
    $zile_lucratoare = $total_zile - $sarbatori_in_luna - $zile_weekend;
    return $zile_lucratoare;
}


// Funcția care generează HTML-ul pentru butoanele upload si download
function afiseaza_butoane_upload_download() {
    echo '
      <div class="buttons-container ">
        <form action="upload.php" method="post" enctype="multipart/form-data" id="upload_form">
            <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;" />
            <label for="fileToUpload" class="upload_button">Upload</label>
            <button id="downloadButton" class="download_button" >Download</button>
        </form>
      </div>
    </div>';
}

// Functia (cod java script) care administreaza clic pe butoanele de upload si download
function getGestioneazaUploadScript() {
    return "
    <script>
   //declararea constantelor necesare functiilor uploadFile si functiei fileToUpload care reprezinta elementele HTML relevante
const fileToUpload = document.getElementById('fileToUpload');
const uploadForm = document.getElementById('upload_form');

// functia uploadFile se ocupa de trimiterea fisierului catre baza de date dupa ce a fost validat de utilizator
function uploadFile(file) {
    console.log(file); // Aici verifici fișierul înainte de a fi trimis la server

    const formData = new FormData();
    formData.append('fileToUpload', file);

    console.log(formData.get('fileToUpload')); // Aici verifici fișierul după ce a fost adăugat în formData

    fetch('/pages/documentele_noastre/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            return response.text();
        } else {
            throw new Error('A apărut o eroare la încărcarea fișierului.');
        }
    })
    .then(text => {
        console.log('Răspuns primit de la server:', text);
        if (text.includes('Fișierul a fost încărcat cu succes!')) {
            alert('Fișierul a fost încărcat cu succes!');
        } else if (text.includes('Fișierul există deja în baza de date.')) {
            alert('Fișierul există deja în baza de date.');
        }
        setTimeout(() => {
            window.location.href = 'activitati.php';
        }, 2000);
    })
    .catch(error => {
        console.error('A apărut o eroare la încărcarea fișierului:', error);
    });
}

// Previne trimiterea formularului și reîncărcarea paginii
uploadForm.addEventListener('submit', (event) => {
    event.preventDefault(); // Previne trimiterea formularului și reîncărcarea paginii
});
    </script>
    ";
}

// functie care permite previzualizarea continutului ce urmeaza a fi incarcat
function getShowFilePreview() {
    $script = '
    <script>
    //functie de previzualizare a continutului (prima pagina) care urmeaza a fi incarcat
    function showFilePreview(file) {
        const filePreviewContainer = document.getElementById("filePreviewContainer");
        const imagePreview = document.getElementById("imagePreview");
        const videoPreview = document.getElementById("videoPreview");
        const pdfPreview = document.getElementById("pdfPreview");

        // Ascunde toate elementele de previzualizare înainte
        imagePreview.style.display = "none";
        videoPreview.style.display = "none";
        pdfPreview.style.display = "none";

        const fileURL = URL.createObjectURL(file);

        if (file.type.startsWith("image/")) {
            imagePreview.src = fileURL;
            imagePreview.style.display = "block";
        } else if (file.type.startsWith("video/")) {
            videoPreview.src = fileURL;
            videoPreview.style.display = "block";
        } else if (file.type === "application/pdf") {
            pdfPreview.src = fileURL;
            pdfPreview.style.display = "block";
        }

        // Adăugați această linie pentru a apela funcția configurePreviewStyle
        configurePreviewStyle();

        // Afișează containerul de previzualizare
        filePreviewContainer.style.display = "block";
    }
    </script>
    ';

    return $script;
}

//convertirea fisierelor apple astfel incat sa poata fi vizualizate in orice browser
function convertAppleFileScript() {
    return '
    <script>
        async function convertAppleFile(file) {
            return new Promise(async (resolve, reject) => {
                try {
                    const blob = await heic2any({ blob: file, toType: "image/jpeg", quality: 1 });
                    const convertedFile = new File([blob], generatedFileName + ".jpg", { type: "image/jpeg" });
                    resolve(convertedFile);
                } catch (error) {
                    reject(error);
                }
            });
        }
    </script>';
}

// pozitionarea ferestrei de previzualizare a continutului ce urmeaza a fi incarcat
function configurePreviewStyleScript() {
    return '
    <script>
        function configurePreviewStyle() {
            const contentElement = document.getElementById("last_uploaded_file").style.display !== "none"
                ? document.getElementById("last_uploaded_file")
                : document.getElementById("last_uploaded_image");

            const previewContainer = document.getElementById("filePreviewContainer");
            const imagePreview = document.getElementById("imagePreview");
            const videoPreview = document.getElementById("videoPreview");
            const pdfPreview = document.getElementById("pdfPreview");

            const contentRect = contentElement.getBoundingClientRect();

            // Setează dimensiunile și poziția containerului de previzualizare
            previewContainer.style.width = `${contentRect.width}px`;
            previewContainer.style.height = `${contentRect.height}px`;
            previewContainer.style.position = "absolute";
            previewContainer.style.left = `${contentRect.left}px`;
            previewContainer.style.top = `${contentRect.top}px`;

            // Setează dimensiunile și poziția elementelor de previzualizare
            [imagePreview, videoPreview, pdfPreview].forEach((previewElement) => {
                previewElement.style.width = "100%";
                previewElement.style.height = "100%";
                previewElement.style.position = "absolute";
                previewElement.style.left = "0";
                previewElement.style.top = "0";
            });
        }
    </script>';
}


// functiile de afisare si modificare a numelui continutului ce urmeaza a fi incarcat
function showRenameModalScript() {
    return '
    <script>
    // functie care-i permite utilizatorului modificarea numelui continutului ce urmeaza a fi incarcat
        function showRenameModal(originalFile, renamedFile) {
            modal = createRenameModal();
            modal.style.display = "block";
            document.getElementById("newFileName").value = renamedFile.name;

            document.getElementById("confirmRename").addEventListener("click", () => {
                let newFileName = document.getElementById("newFileName").value;
                newFileName = cleanFileName(newFileName);
                const updatedFile = new File([originalFile], newFileName, { type: originalFile.type });
                modal.remove(); // elimină modalul
                filePreviewContainer.style.display = "none"; // Ascunde previzualizarea
                uploadFile(updatedFile);
            });

            document.getElementById("cancelRename").addEventListener("click", () => {
                modal.remove(); // elimină modalul
                filePreviewContainer.style.display = "none"; // Ascunde previzualizarea
            });
        }
        // functia care preia numele modificat de utilizator
        function createRenameModal() {
            const modal = document.createElement("div");
            modal.id = "renameModal";
            modal.style.display = "none";

            const title = document.createElement("h3");
            title.innerText = "E ok acest Titlu ?";
            modal.appendChild(title);

            const input = document.createElement("input");
            input.type = "text";
            input.id = "newFileName";
            input.classList.add("file-name-input"); // Adaugă clasa CSS aici care se va ocupa de redimensionarea ferestrei/câmpului în care apare denumirea fișierului în fereastra modală
            modal.appendChild(input);

            const confirmButton = document.createElement("button");
            confirmButton.id = "confirmRename";
            confirmButton.innerText = "OK";
            modal.appendChild(confirmButton);

            const cancelButton = document.createElement("button");
            cancelButton.id = "cancelRename";
            cancelButton.innerText = "Anulează";
            cancelButton.classList.add("cancel"); // clasa butonului "Anulează" din style.css
            modal.appendChild(cancelButton);

            document.body.appendChild(modal);
            return modal;
        }
    </script>';
}

// functia de cautare
function rezultateCautareScript() {
    return '
    <script>
        function rezultateCautare() {
            const searchInput = document.querySelector(".search-input");
            const searchString = searchInput.value.toLowerCase();

            files_utilizator.forEach(file => {
                const numeFisier = file.nume_fisier.toLowerCase();
                const numeProfesor = file.nume_prenume.toLowerCase();
                const data = new Date(file.data_upload).toLocaleString("ro-RO", { year: "numeric", month: "2-digit", day: "2-digit", hour12: false, hour: "2-digit", minute: "2-digit" });

                const fileElement = document.querySelector(`#file_list_utilizator .file-item[data-id="${file.id_info}"]`);

                if (numeFisier.includes(searchString) || numeProfesor.includes(searchString) || data.includes(searchString)) {
                    fileElement.classList.remove("hidden");
                } else {
                    fileElement.classList.add("hidden");
                }
            });

            files_ceilalti.forEach(file => {
                const numeFisier = file.nume_fisier.toLowerCase();
                const numeProfesor = file.nume_prenume.toLowerCase();
                const data = new Date(file.data_upload).toLocaleString("ro-RO", { year: "numeric", month: "2-digit", day: "2-digit", hour12: false, hour: "2-digit", minute: "2-digit" });

                const fileElement = document.querySelector(`#file_list_ceilalti .file-item[data-id="${file.id_info}"]`);

                if (numeFisier.includes(searchString) || numeProfesor.includes(searchString) || data.includes(searchString)) {
                    fileElement.classList.remove("hidden");
                } else {
                    fileElement.classList.add("hidden");
                }
            });
        }

        document.querySelector(".search-input").addEventListener("input", rezultateCautare);
    </script>';
}




//sfarsitul functiilor si inceputul constantelor ------------------------------------------------------------------------------------------------------

$contributia_stabilita = 25; // Valoarea pentru g276
// $contributia_stabilita = 25; // Valoarea pentru g65
$numeUnitateScolara = "Unitatea Școlară DEMO";
$cadran_simulatTitlu1Rand2 = "importantele...";
$cadran_simulatTitlu1Rand3 = "grupele...";
$cadran_simulatTitlu2Rand3 = "clasele...";
$cadran_simulatTitlu3Rand3 = "din educatie...";

?>
