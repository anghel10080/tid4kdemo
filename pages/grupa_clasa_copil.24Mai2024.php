<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }
require_once '../config.php';
// require_once '../sesiuni.php';
require_once 'creeaza_si_descarca_thumbnailuri.php';
require_once 'functii_si_constante.php';
  // Apelarea functiei pentru a umple variabilele de sesiune
  determina_variabile_utilizator($conn);

  $status = $_SESSION['status'];//status este preluat din functii_si_constante

  // segmentul de cod care actualizeaza indexul de grupa/clasa $_SESSION['index_grupa_clasa_curenta'] dupa selectia din dropdown box facuta de utilizator
if (isset($_GET['noul_index'])) {$sql_profesori = "SELECT SUM(ultima_activitate = CURDATE() AND (status = 'profesor' OR status = 'director' OR status = 'administrator' OR status = 'secretara')) as prezenti_profesori, SUM(ultima_activitate != CURDATE() AND (status = 'profesor' OR status = 'director' OR status = 'administrator' OR status = 'secretara')) as absenti_profesori FROM utilizatori";
    $noul_index = intval($_GET['noul_index']);
    $_SESSION['index_grupa_clasa_curenta'] = $noul_index;
    $_SESSION['grupa_clasa_copil'] = $_SESSION['toate_grupele_clase'][$noul_index];
    $_SESSION['grupa_clasa_copil_selectat_dropbox'] = true;

    // Codul pentru actualizarea în baza de date
    $sql = "UPDATE asociere_multipla SET index_grupa_clasa_curenta = ? WHERE id_utilizator = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_SESSION['index_grupa_clasa_curenta'], $_SESSION['id_utilizator']);
    $stmt->execute();
}

// Daca utilizatorul nu este autentificat, redirecționează către pagina de start
// if (!isset($_SESSION['id_utilizator']) || $_SESSION['rol'] !== $_SESSION['grupa_clasa_copil']) {
//     header('Location: /index.php');
//     exit();
// }

//urmeaza codul care se ocupa de mesaje pentru a fi inscrise corect la expeditor si destinatar
// Stochează valoarea actuală a $_SESSION['grupa_clasa_copil_']
$grupa_actuala = $_SESSION['grupa_clasa_copil_'];
$destinatari = isset($_POST['destinatari']) ? explode(',', $_POST['destinatari']) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mesaj = $_POST['mesaj'];
    $id_expeditor = $id_utilizator;
    date_default_timezone_set('Europe/Bucharest');
    setlocale(LC_TIME, 'ro_RO.UTF-8');
    $data_trimitere = date('Y-m-d H:i:s');

  foreach ($destinatari as $id_destinatar) {
    // Obținerea statusului destinatarului
    $sql_status_destinatar = "SELECT status FROM utilizatori WHERE id_utilizator = ?";
    $stmt_status = mysqli_prepare($conn, $sql_status_destinatar);
    mysqli_stmt_bind_param($stmt_status, 'i', $id_destinatar);
    mysqli_stmt_execute($stmt_status);
    $result_status = mysqli_stmt_get_result($stmt_status);
    $row_status = mysqli_fetch_assoc($result_status);
    $status_destinatar = $row_status['status'];

    // Verificare statut destinatar și pregătirea SQL-ului corespunzător
    if ($status_destinatar === 'parinte') {
        $sql_grupa = "SELECT grupa_clasa_copil FROM copii WHERE id_utilizator = ?";
    } else {
        $sql_grupa = "SELECT grupa_clasa_copil FROM asociere_multipla WHERE id_utilizator = ?";
    }

    $stmt_grupa = mysqli_prepare($conn, $sql_grupa);
    mysqli_stmt_bind_param($stmt_grupa, 'i', $id_destinatar);
    mysqli_stmt_execute($stmt_grupa);
    $result_grupa = mysqli_stmt_get_result($stmt_grupa);

        if ($result_grupa && $row_grupa = mysqli_fetch_assoc($result_grupa)) {
            $grupa_destinatar = str_replace(' ', '_', $row_grupa['grupa_clasa_copil']);

            // Pentru destinatar
            $sql_mesaj = "INSERT INTO mesaje_" . $grupa_destinatar . " (id_expeditor, id_destinatar, mesaj, data_trimitere) VALUES (?, ?, ?, ?)";
            $stmt_mesaj = mysqli_prepare($conn, $sql_mesaj);
            mysqli_stmt_bind_param($stmt_mesaj, "iiss", $id_expeditor, $id_destinatar, $mesaj, $data_trimitere);
            mysqli_stmt_execute($stmt_mesaj);

            // Pentru expeditor, dacă grupa expeditorului diferă de grupa destinatarului
            if ($grupa_actuala !== $grupa_destinatar) {
                $sql_mesaj_exp = "INSERT INTO mesaje_" . $grupa_actuala . " (id_expeditor, id_destinatar, mesaj, data_trimitere) VALUES (?, ?, ?, ?)";
                $stmt_mesaj_exp = mysqli_prepare($conn, $sql_mesaj_exp);
                mysqli_stmt_bind_param($stmt_mesaj_exp, "iiss", $id_expeditor, $id_destinatar, $mesaj, $data_trimitere);
                mysqli_stmt_execute($stmt_mesaj_exp);
            }
        }
    }

    header("Location: grupa_clasa_copil.php?destinatari=" . implode(',', $destinatari));
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>TID4K - <?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <link rel="icon" type="image/png" sizes="192x146" href="tid4k_icon_192_H.png">
    <link rel="apple-touch-icon" sizes="192x146" href="tid4k_icon_192_H.png">
    <script>
    var id_utilizator = <?php echo json_encode($id_utilizator);?> //id_ul de utilizator este trimis intr-o variabila tip javascript
    var statusUtilizator = "<?php echo $status; ?>"; //status utilizator este trimis de asemenea catre javascript
     var afiseazaCancelarie = <?php echo ($_SESSION['afiseaza_cancelarie'] ?? false) ? 'true' : 'false'; ?>;

    </script>


</head>
<body>
   <!--Upload la dublu-clic oriunde in pagina de browser -->
    <form id="upload_form">
        <input type="file" id="fileToUpload" style="display:none" accept="image/*,application/pdf">
    </form>

    <?php
        // Include fișierul cu funcția de gestionare a upload-ului
        require_once 'functii_si_constante.php';
        // Afișează scriptul de gestionare a upload-ului
        echo getGestioneazaUploadScript();
    ?>

<header>
    <div class="header-container">
        <!-- Afiseaza 'CANCELARIE' daca utilizatorul este un profesor -->
        <?php if ($_SESSION['afiseaza_cancelarie'] ?? false): ?>
            <h2 class="conditie-cancelarie">CANCELARIE</h2>
        <?php endif; ?>

        <div class="cancelarie-container">
            <!-- Aici vor fi inserate cardurile profesorilor -->
        </div>
<!--    afiseaza icon-urile si prezenta si absenta copiilor si profesorilor (in stanga sus pe pagina)-->
   <?php if (in_array($status, ['director', 'administrator', 'secretara', 'contabil'])): ?>
    <div id="prezenta_generala"></div>
    <!--codul care afiseasa prezenta generala pentru elevi si profesori din apelarea prezenta_generala.php-->
<script>
function fetchPrezentaGenerala() {
    fetch('prezenta_generala.php')
    .then(response => response.json())
    .then(data => {
        const prezenti_copii = data.prezenti_general_copii;
        const absenti_copii = data.absenti_general_copii;
        const prezenti_profesori = data.prezenti_general_profesori;
        const absenti_profesori = data.absenti_general_profesori;

        document.getElementById('prezenta_generala').innerHTML = `
            <div><span style="margin-right: 5px;">👶</span><span style="margin-right: 3px;">:</span><span style="color: green; width: 20px; display: inline-block; text-align: right;">${prezenti_copii}</span><span style="color: #b22222; width: 20px; display: inline-block; text-align: right;">${absenti_copii}</span></div>
            <div><span style="margin-right: 3px;">👨‍💼</span><span style="margin-right: 3px;">:</span><span style="color: green; width: 20px; display: inline-block; text-align: right;">${prezenti_profesori}</span><span style="color: #b22222; width: 20px; display: inline-block; text-align: right;">${absenti_profesori}</span></div>
        `;
    });
}

// Apelați funcția o dată la început pentru a încărca datele inițiale
fetchPrezentaGenerala();

// Apelați funcția la fiecare 3000 ms (3 secunde)
setInterval(fetchPrezentaGenerala, 3000);
</script>
    <?php endif; ?>
<!--cazul utilizatorilor multiplii (care sunt asociati la mai multe grupe/clase)-->
<?php if ($_SESSION['numar_grupe_clase_utilizator'] > 1): ?>
<div class="grupa-clasa-container">
    <?php if (!in_array($status, ['director', 'administrator', 'secretara', 'contabil'])): ?>
    <h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?><i class="clopotel_icon ascuns">&#x1F514;</i></h1>
    <?php endif; ?>
    <div id="dropdown_container">
        <select id="select_grupa_clasa">
            <!-- Opțiunile vor fi adăugate dinamic folosind JavaScript -->
        </select>
    </div>
</div>
<script>
    // Populează dropdown-ul cu grupurile/clasele pentru cazul cu mai mulți copii
    const selectElementMulti = document.getElementById('select_grupa_clasa');
    const grupuriClaseMulti = <?php echo json_encode($_SESSION['toate_grupele_clase']); ?>;
    grupuriClaseMulti.forEach((grupa, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = grupa.toUpperCase();
        if (index === <?php echo json_encode($_SESSION['index_grupa_clasa_curenta']); ?>) {
            option.selected = true;
        }
        selectElementMulti.appendChild(option);
    });
    // Oprire propagare eveniment la elementul părinte
    selectElementMulti.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    selectElementMulti.addEventListener('change', function() {
    const selectedIndex = this.value;
    location.href = 'grupa_clasa_copil.php?noul_index=' + encodeURIComponent(selectedIndex);
});
</script>

<!--cazul utilizatorilor care sunt asociati la o singura grupa/clasa-->
<?php elseif ($_SESSION['numar_grupe_clase_utilizator'] == 1): ?>
    <h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?><i class="clopotel_icon ascuns">&#x1F514;</i></h1>
<?php endif; ?>

        <?php
      $status = $_SESSION['status'];
if ($status != 'parinte' && $status != 'elev') {
?>
    <a href="/avizier/tid4k.html" class="logo-link">
        <div class="logo"></div>
    </a>
<?php
} else {
?>
    <a href="/pages/grupa_clasa_copil.php">
        <div class="logo"></div>
    </a>
<?php
}
        ?>

        <!-- Aici containerul de notificări -->
        <div id="container_general">
    <span id="container_notificari"></span>
        </div>
    </div>
</header>
	<main>
  <div class="container">
    <div class="column documente">

      <ul>
        <li class="doc-item">
        <a href="/pages/rapoarte_grupa_clasa_copil.php">Istoric prezenta si contributie</a>
        </li>
        <?php
// Verifică dacă utilizatorul are unul dintre rolurile specificate și dacă grupa_clasa_copil nu conține cuvântul 'grupa'
if (isset($_SESSION['status'], $_SESSION['grupa_clasa_copil']) &&
    in_array($_SESSION['status'], ['profesor', 'director', 'administrator']) &&
    strpos($_SESSION['grupa_clasa_copil'], 'grupa') === false) {
    // Dacă condițiile sunt îndeplinite, afișează segmentul HTML pentru Orarul Clasei
    echo '<li class="doc-item"><a href="/pages/orar.php">Orarul</a></li>';
}
?>
<li class="doc-item"><a href="/pages/documentele_noastre/activitati.php">Activitățile <?php echo strtoupper($_SESSION['grupa_clasa_copil']);?></a></li>
<li class="doc-item">
    <a href="/pages/tabel_meniu_afisat.php">Meniul,</a>
    <a href="javascript:void(0);" onclick="openAnnouncementPopup();">anunțuri</a>
    <a href="/pages/documentele_noastre/documente_meniuri.php">și administrative</a>
</li>
<script>
    function openAnnouncementPopup() {
        window.open('introdu_anuntul.php', 'popup', 'width=600,height=auto,scrollbars=no,resizable=no');
    }
</script>
    <?php
  $status = $_SESSION['status'];
  if ($status != 'parinte' && $status != 'elev') {
?>
        <li class="doc-item">
        <!--<a href="#Infodisplay" onclick="toggleDisplay()">Infodisplay</a>-->
        <a href="infodisplay_layout.php">Infodisplay</a>
        </li>
<?php
 // require_once 'infodisplay.php';
  }
?> <!--aici se termina codul pentru cadranul-simulat ID4K-->
    </ul>
    </div>

     <div class="column_pdf_uri">
      <h2><a href="/pages/documentele_noastre/documente_activitati.php" >...doar Documente </a></h2>

     <div id="iframe_grid_container" class="iframe_grid_container" ></div> <!--aici afiseaza ultimele 3 documente din documente_activitati.php-->

    </div>

    <div class="column_imagini">
      <h2><a href="/pages/documentele_noastre/imagini_activitati.php" >...doar Imagini </a></h2>

     <div id="image_grid_container" class="image_grid_container" ></div> <!--aici afiseaza ultimele 3 imagini din imagini_activitati.php-->

    </div>
    </div>

<div id="column_chat" class="column chat">
    <h2>Chat cu <?php echo ($status === 'parinte' || $status === 'elev') ? 'profesorii' : 'părinții'; ?> dumneavoastră</h2>
    <form id="formularMesaj" method="post">
        <!--<div id="destinatariSelectati"></div>-->
        <label for="mesaj">Scrieți un mesaj:</label>
        <div class="input-wrapper">
            <textarea id="mesajInput" name="mesaj"></textarea>
            <div id="fereastraSelectie" style="display: none;"></div>
        </div>
        <button id="trimiteti" type="submit">Trimiteți</button>
        <input type="hidden" id="destinatariInput" name="destinatari">
    </form>

<!--    acest cod afiseaza istoricul mesajelor cu profesorii/parintii-->
<div id="istoric_mesaje"></div>
 <!--istoricul mesajelor se incheie aici-->

<!--fereastra de afisare in lista a destinatarilor pentru selectie-->
<input type="hidden" id="destinatariInput" name="destinatari">
<div id="destinatariDisplay" class="destinatari-display"></div>

</div>
 </div>
</main>

<footer class="footer-container">
  <p>TID4K © 2024</p>
</footer>

<script src="3_documente_activitati.js"></script>
<script src="3_imagini_activitati.js"></script>

<script>
// incepe codul javascript care este inglobat intr-o functie generala DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
    let destinatari = new Set();
    let fereastraDeschisa = false;
    let numar_mesaje_nevazute = 0;
    let numar_imagini_nevazute = 0;
    let numar_documente_nevazute = 0;
    // let mesajCatre = '';

 // incarca notificarile salvate in localStorage, evitand in felul acesta resetarea gresita a valorii corespunzatoare la incarcarea paginii
function incarcaNotificariSalvate() {
  const mesajeSalvate = localStorage.getItem("numar_mesaje_nevazute");
  const imaginiSalvate = localStorage.getItem("numar_imagini_nevazute");
  const documenteSalvate = localStorage.getItem("numar_documente_nevazute");

  if (mesajeSalvate) {
    numar_mesaje_nevazute = parseInt(mesajeSalvate);
  }
  if (imaginiSalvate) {
    numar_imagini_nevazute = parseInt(imaginiSalvate);
  }
  if (documenteSalvate) {
    numar_documente_nevazute = parseInt(documenteSalvate);
  }
}

//apel din localStorage
incarcaNotificariSalvate();
getNotificari();

// Funcția getNotificari pentru a construi și afișa notificările în containerul de notificări
function getNotificari() {
  const timestamp = new Date().getTime();
  const fetchMesaje = fetch(`fetch_mesaje.php?_=${timestamp}`).then((response) => response.json());
  const fetchImagini = fetch(`fetch_imagini_vizualizate.php?_=${timestamp}`).then((response) => response.json());
  const fetchDocumente = fetch(`fetch_documente_vizualizate.php?_=${timestamp}`).then((response) => response.json());

  Promise.all([fetchMesaje, fetchImagini, fetchDocumente])
    .then(([mesajeData, imaginiData, documenteData]) => {

     // Extrageți datele din obiectele mesajeData, imaginiData și documenteData
  const numar_mesaje_nou = mesajeData.numar_mesaje_nou;
  const numar_imagini_nou = imaginiData.numar_imagini_nou;
  const numar_documente_nou = documenteData.numar_documente_nou;

  // Actualizați numărul de notificări nevăzute în funcție de datele primite
  numar_mesaje_nevazute += numar_mesaje_nou;
  numar_imagini_nevazute += numar_imagini_nou;
  numar_documente_nevazute += numar_documente_nou;

  // Salvați valorile în localStorage
  localStorage.setItem("numar_mesaje_nevazute", numar_mesaje_nevazute);
  localStorage.setItem("numar_imagini_nevazute", numar_imagini_nevazute);
  localStorage.setItem("numar_documente_nevazute", numar_documente_nevazute);

      // Codul pentru afișarea notificărilor în containerul de notificări
  const containerNotificari = document.getElementById("container_notificari");
  containerNotificari.innerHTML = `
    ${numar_mesaje_nevazute > 0 ? `<p id="mesaje"><span style="font-size: 1.3em;">&#x1F4E9;</span><span style="font-size: 1.4em;">${numar_mesaje_nevazute}</span></p>` : ''}
    ${numar_imagini_nevazute > 0 ? `<p id="imagini"><span style="font-size: 1.3em;">&#x1F4F7;</span><span style="font-size: 1.4em;">${numar_imagini_nevazute}</span></p>` : ''}
    ${numar_documente_nevazute > 0 ? `<p id="documente"><span style="font-size: 1.2em;">&#x1F4C4;</span><span style="font-size: 1.4em;">${numar_documente_nevazute}</span></p>` : ''}
  `;

//functiile de redirectionare
function scrollToChat() {
  const chatElement = document.querySelector("#column_chat");
  chatElement.scrollIntoView({ behavior: "smooth" });
}

function redirectToImagini() {
  window.location.href = "/pages/documentele_noastre/imagini_activitati.php";
}

function redirectToDocumente() {
  window.location.href = "/pages/documentele_noastre/documente_activitati.php";
}

//si ascultatori de clic pe elementele de notificare mesaje, imagini, documente din container_notificari
const mesajeElement = document.getElementById("mesaje");
const imaginiElement = document.getElementById("imagini");
const documenteElement = document.getElementById("documente");

if (mesajeElement) {
  mesajeElement.addEventListener("click", function() {
    scrollToChat();
    numar_mesaje_nevazute = 0;
     localStorage.setItem("numar_mesaje_nevazute", numar_mesaje_nevazute);
  });
}

if (imaginiElement) {
  imaginiElement.addEventListener("click", function() {
    redirectToImagini();
    numar_imagini_nevazute = 0;
     localStorage.setItem("numar_imagini_nevazute", numar_imagini_nevazute);
  });
}

if (documenteElement) {
  documenteElement.addEventListener("click", function() {
    redirectToDocumente();
    numar_documente_nevazute = 0;
     localStorage.setItem("numar_documente_nevazute", numar_documente_nevazute);
  });
}


      // Actualizarea afișării clopotelului
      const clopotelIcon = document.querySelector(".clopotel_icon");
      if (numar_mesaje_nou > 0 || numar_imagini_nou > 0 || numar_documente_nou > 0) {
        clopotelIcon.classList.remove("ascuns");
      } else {
        clopotelIcon.classList.add("ascuns");
      }
    })
    .catch((error) => console.error('Error:', error));
}

// Apelați funcția getNotificari() pentru a actualiza numărul de notificări și a construi conținutul containerului de notificări atunci când pagina se încarcă
getNotificari();

// reimprospatare notificari fara reincarcarea paginii
setInterval(getNotificari, 13 * 1000);


//urmeaza codul care se ocupa de afisarea cardurilor la clic pe CANCELARIE (profesori si administrativ)
let esteDeschisaFereastraCancelarie = false;
const cancelarieElement = document.querySelector('.conditie-cancelarie');
const cancelarieContainer = document.querySelector('.cancelarie-container');

//codul pentru afisarea la clic pe CANCELARIE
if (afiseazaCancelarie) {
  cancelarieElement.addEventListener('click', function() {
     if (esteDeschisaFereastraCancelarie) {
            cancelarieContainer.innerHTML = '';
            esteDeschisaFereastraCancelarie = false;
        } else {
    let cancelarieData = [];

    $.ajax({
        url: 'preia_avatar_copii_parinti_profesori.php?format=format_cancelarie',
        type: 'GET',
        dataType: 'json',
        success: function(Cancelariedata) {
            // Populam cancelarieData cu datele din apelul AJAX
            cancelarieData = Cancelariedata;

              // Al doilea apel AJAX pentru cardurile "ADMINISTRATIV"
            $.ajax({
                url: '/pages/documentele_noastre/fetch_administrativ.php?format=format_administrativ',
                type: 'GET',
                dataType: 'json',
                success: function(Administrativdata) {
                    // Concatenam datele din al doilea apel AJAX cu cancelarieData
                     cancelarieData = cancelarieData.concat(Administrativdata);

            cancelarieContainer.innerHTML = '';

            cancelarieData.forEach((cancelarieElement) => {
                let cardProfesor = `
                   <div class="card-profesor" card-nume-copil-profesor="${cancelarieElement.profesor.nume_prenume}">
                    <div class="info-profesor">
                        <div class="imagine-profesor">
                            <img src="${cancelarieElement.profesor.cale_avatar}" alt="Avatar" />
                            <div class="suprapunere-imagine">
                                <i class="fas fa-upload"></i>
                                <i class="fas fa-trash"></i>
                            </div>
                        </div>
                        <p><span class="${cancelarieElement.profesor.este_conectat ? 'online' : 'offline'}">${cancelarieElement.profesor.este_conectat ? '&#9679;' : ''}</span>${cancelarieElement.profesor.nume_prenume}
                        </p>
                        <div class ="email-profesor">
                            <p>${cancelarieElement.profesor.email}</p>
                        </div>
                    </div>
                </div>
                `;
    // Ascultător pentru clic pe cardul profesorului
    $(document).on('click', '.card-profesor', function() {
          // Trimite către chat
    var mesajCatre = $(this).attr('card-nume-copil-profesor');

    // Închidem fereastra cu carduri
    $('.cancelarie-container').remove();
    FereastraProfesoriSauCopiiDeschisa = false;

    // Simulăm un clic în textarea
    $('#mesajInput').focus();

    // Verificăm dacă fereastra de selecție este deja deschisă
    if (!fereastraDeschisa) {
        afiseazaFereastraSelectie(' ', mesajCatre);
    }

    // Resetăm mesajCatre
    mesajCatre = '';

    // Realizăm un smooth scroll către #column_chat
    $('html, body').animate({
        scrollTop: $('#column_chat').offset().top
    }, 1000);
    });
                cancelarieContainer.insertAdjacentHTML('beforeend', cardProfesor);
            });
                }//inchidere linie de succes apel AJAX carduri Administrativ
            });//inchidere linie de deschidere apel AJAX carduri Administrativ
        } //inchidere linie de succes apel AJAX carduri CANCELARIE
    }); //inchidere linie deschidere apel AJAX carduri CANCELARIE
    esteDeschisaFereastraCancelarie = true;
        }
  });

  // Ascundem cardurile la clic în afara containerului
  document.addEventListener('click', function(event) {
    if (!cancelarieContainer.contains(event.target) && !cancelarieElement.contains(event.target)) {
      cancelarieContainer.innerHTML = '';
    }
  });
}

//functie de ascultare la clic pe carduri Profesori sau Administrativ (*functiile pot fi chemate cu denumirea lor)
function afiseazaCarduriClicProfesori(fereastraGrid) {
    // Afisarea ferestrei grid in pagina
    $('.nume-grupa').after(fereastraGrid);

    // Ascultător pentru clic pe cardul profesorului
    $(document).on('click', '.card-profesor', function() {
          // Trimite către chat
    var mesajCatre = $(this).attr('card-nume-copil-profesor');

    // Închidem fereastra cu carduri
    $('.grid-profesori').remove();
    FereastraProfesoriSauCopiiDeschisa = false;

    // Simulăm un clic în textarea
    $('#mesajInput').focus();

    // Verificăm dacă fereastra de selecție este deja deschisă
    if (!fereastraDeschisa) {
        afiseazaFereastraSelectie(' ', mesajCatre);
    }

    // Resetăm mesajCatre
    mesajCatre = '';

    // Realizăm un smooth scroll către #column_chat
    $('html, body').animate({
        scrollTop: $('#column_chat').offset().top
    }, 1000);
    });
}

//codul AJAX pentru obtinerea datelor despre copii si parinti si afisarea acestora la clic pe titlu $grupa_clasa_copil
function afiseazaGridCopiiSauNumeProfesori() {
    // Solicitarea datelor copiilor și părinților din baza de date prin AJAX
   // Declarație variabilă pentru a stoca datele
let data = [];
let fereastraGrid = ""

// Apelul AJAX pentru cardurile de Profesori
$.ajax({
    url: 'preia_avatar_copii_parinti_profesori.php?format=format_profesori',
    type: 'GET',
    dataType: 'json',
    success: function(dataProfesori) {
        data = dataProfesori;

        // Apelul AJAX pentru cardurile Administrativ
        $.ajax({
            url: '/pages/documentele_noastre/fetch_administrativ.php?format=format_administrativ',
            type: 'GET',
            dataType: 'json',
            success: function(dataAdministrativ) {
                data = data.concat(dataAdministrativ);

                // Construirea ferestrei grid
                let fereastraGrid = "";
                console.log("status utilizator : ", statusUtilizator);

                if (statusUtilizator === 'parinte' || statusUtilizator === 'elev') {
                    fereastraGrid = '<div class="grid-profesori">';
                }

                data.forEach(element => {
                    // Verifică statusul utilizatorului pentru profesori
                    if (statusUtilizator === 'parinte' || statusUtilizator === 'elev') {
                        fereastraGrid += `
                            <div class="card-profesor" card-nume-copil-profesor="${element.profesor.nume_prenume}">
                                <div class="info-profesor">
                                    <div class="imagine-profesor">
                                        <img src="${element.profesor.cale_avatar}" alt="Avatar" />
                                        <div class="suprapunere-imagine">
                                            <i class="fas fa-upload"></i>
                                            <i class="fas fa-trash"></i>
                                        </div>
                                    </div>
                                    <p><span class="${element.profesor.este_conectat ? 'online' : 'offline'}">${element.profesor.este_conectat ? '&#9679;' : ''}</span>${element.profesor.nume_prenume}
                                    </p>
                                    <div class="email-profesor">
                                        <p>${element.profesor.email}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });

                // Închiderea div-ului pentru grid-ul profesorilor
                fereastraGrid += '</div>';

afiseazaCarduriClicProfesori(fereastraGrid);

            }//inchide linia de succes apelul AJAX data Administrativ
        });//inchide linia de inceput apelul AJAX data Administrativ
    }//inchide linia de succes apelul AJAX data Profesori
});//inchide linia de inceput apelul AJAX data Profesori



                    // apel AJAX pentru cardurile de copii
                    $.ajax({
                        url: 'preia_avatar_copii_parinti_profesori.php?format=format_copii',
                        type: 'GET',
                        dataType: 'json',
                        success: function(dataCopii) {
                            data = dataCopii;

            // Calculează înălțimea ferestrei în funcție de numărul de copii
            let numarCopii = data.length;
            let inaltimeCardCopil = 70; // Înlocuiți cu înălțimea reală a cardului copilului, inclusiv spațierea și marginile
            let numarCarduriPeRand = 5; // Numărul de carduri pe rând
            let numarRanduri = Math.ceil(numarCopii / numarCarduriPeRand); // Calculează numărul de rânduri necesar
            let inaltimeGrid = numarRanduri * inaltimeCardCopil;

if (statusUtilizator != 'parinte' && statusUtilizator != 'elev') {
    fereastraGrid = `<div class="grid-copii" style="height: ${inaltimeGrid}px">`;
    // fereastraGrid = ' <div class="cancelarie-container">';
}


data.forEach(element => {
    // Verifică statusul utilizatorului pentru copii și părinți
    if (statusUtilizator !== 'parinte' && statusUtilizator !== 'elev') {
        fereastraGrid += `
        <div class="card-copil" card-nume-copil-profesor="${element.parinte.nume_prenume}" card-nume-copil="${element.copil.nume_copil}" card-id-copil="${element.copil.id_copil}">
            <div class="imagine-copil">
                <img src="${element.copil.cale_avatar}" alt="Avatar" />
                <div class="suprapunere-imagine">
                    <i class="fas fa-upload"></i>
                    <i class="fas fa-trash"></i>
                </div>
            </div>
            <div class="info-copil">
                <p>
                    <span class="${element.copil.este_conectat ? 'online' : 'offline'}">${element.copil.este_conectat ? '&#9679;' : ''}</span>
                    <span class="${element.copil.prezenta_determinata_de_parinte ? 'limegreen' : ''}">${element.copil.nume_copil}</span>
                </p>
                <p class="info-parinte">${element.parinte.nume_prenume}</p>
                <p>${element.parinte.telefon}</p>
                <p>${element.parinte.email}</p>
            </div>
        </div>
        `;
    }
});


            fereastraGrid += '</div>';

    // Adăugarea ascultătorilor de evenimente pentru carduri care la un singur clic pe card, trimite catre "Chat cu ..." si introduce deja in destinatari nume_prenume card
// Variabile pentru a urmări numărul de clicuri și timer-ul
let clickCount = 0;
let clickTimer = null;

// Ascultător pentru clic pe cardul copilului
$(document).on('click', '.card-copil', function() {
    // Incrementează numărul de clicuri
    clickCount++;

    // Verifică dacă este primul clic
    if (clickCount === 1) {
        // Începe un nou timer
        clickTimer = setTimeout(() => {
            // Verifică dacă a fost doar un singur clic
            if (clickCount === 1) {
                // Găsim elementul de stare de conectare (bulina)
                let bulina = $(this).find('.info-copil p .online, .info-copil p .offline');
                let nume = $(this).find('.info-copil p span:last-child');

                // Toggle clasa 'online' / 'offline'
                if (bulina.hasClass('online')) {
                    bulina.removeClass('online');
                    bulina.addClass('offline');
                    bulina.html(''); // scoate bulina verde
                } else {
                    bulina.removeClass('offline');
                    bulina.addClass('online');
                    bulina.html('&#9679;'); // pune bulina verde
                }

                // Daca numele copilului este verde, il aducem la negru
                if (nume.hasClass('limegreen')) {
                    nume.removeClass('limegreen');
                }
            }

            // Resetează numărul de clicuri
            clickCount = 0;
        }, 300); // durata în milisecunde pentru cât timp așteptăm un al doilea clic
    } else if (clickCount === 2) {
        // Anulăm timerul
        clearTimeout(clickTimer);

        // Găsim elementul de nume
        let nume = $(this).find('.info-copil p span:last-child');

        // Toggle clasa 'limegreen'
        if (nume.hasClass('limegreen')) {
            nume.removeClass('limegreen');
        } else {
            nume.addClass('limegreen');
        }//corespunzator liniei de 'succes' din primul apel AJAX

        // Codul existent pentru dublu clic...
        var mesajCatre = $(this).attr('card-nume-copil-profesor');

        // Închidem fereastra cu carduri
        $('.grid-copii').remove();
        FereastraProfesoriSauCopiiDeschisa = false;

        // Simulăm un clic în textarea
        $('#mesajInput').focus();

        // Verificăm dacă fereastra de selecție este deja deschisă
        if (!fereastraDeschisa) {
            afiseazaFereastraSelectie(' ', mesajCatre);
        }

        // Resetăm mesajCatre
        mesajCatre = '';

        // Realizăm un smooth scroll către #column_chat
        $('html, body').animate({
            scrollTop: $('#column_chat').offset().top
        }, 1000);

        // Resetează numărul de clicuri$sql = "SELECT u.id_utilizator, u.id_cookie, u.status, u.temp_path
       $sql = "SELECT * FROM utilizatori WHERE id_utilizator = ?";
       clickCount = 0;
    }
});

            // Afisarea ferestrei grid in pagina
            $('.nume-grupa').after(fereastraGrid);


        },//corespunzator liniei de 'succes' din apel AJAX pentru cardurile de copii
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('A apărut o eroare la preluarea datelor: ' + textStatus + ' - ' + errorThrown);
        }
    });//corespunzator liniei de inceput apel AJAX pentru cardurile de copii
}


//ascultatori de clic pe titlul $grupa_clasa_copil pentru activarea functiei afiseazaGridCopiiSauNumeProfesori
let FereastraProfesoriSauCopiiDeschisa = false;

$(document).ready(function() {

    // Apelul funcției afiseazaGridCopiiSauNumeProfesori la clic pe titlul '$grupa_clasa' si inchide fereastra la al doilea clic pe titlul 'grupa_clasa_copil'
   $('.nume-grupa').on('click', function() {
    let tipElement = $(this).hasClass('nume-grupa') ? 'Nume-grupa' : 'CANCELARIE';
    console.log(`${tipElement} a fost DESCHISA`);

    if (!FereastraProfesoriSauCopiiDeschisa) {
        // Verificam daca grila a fost deja creata
         if (($('.grid-copii').length === 0 && $('.grid-profesori').length === 0)) {
        afiseazaGridCopiiSauNumeProfesori(); // Daca nu, o cream
        } else {
            // Daca da, doar o afisam
            $('.grid-copii').show();
            $('.grid-profesori').show();

        }
        $('body').addClass('no-scroll'); // Previne scroll-ul pe pagina principală
        FereastraProfesoriSauCopiiDeschisa = true;
    } else if (FereastraProfesoriSauCopiiDeschisa) {
        // Selecteaza toate cardurile de copii
        let carduriCopii = $('.card-copil');
        $('.grid-copii').hide();
        $('.grid-profesori').hide();
        $('body').removeClass('no-scroll')//permitem din nou scroll-ul pe pagina principală
        FereastraProfesoriSauCopiiDeschisa = false;
        console.log("Nume-grupa a fost INCHISA");
        // Apelam functia de contorizare prezenta
        contorizarePrezentaAbsenta(carduriCopii);
    }
});

$(document).on('click', function(event) {
    // Verifica daca s-a dat click pe '.nume-grupa', '.grid-copii' sau '.grid-profesori' sau pe copiii acestora
    if (!$(event.target).closest('.nume-grupa, .grid-copii, .grid-profesori').length) {
        // Daca FereastraProfesoriSauCopiiDeschisa este adevarat (fereastra este deschisa), o inchidem
        if (FereastraProfesoriSauCopiiDeschisa) {
            let carduriCopii = $('.card-copil');
            $('.grid-copii').hide();
            $('.grid-profesori').hide();
            $('body').removeClass('no-scroll')//permitem din nou scroll-ul pe pagina principală
            FereastraProfesoriSauCopiiDeschisa = false;
            console.log("Nume-grupa a fost INCHISA");
            contorizarePrezentaAbsenta(carduriCopii);
        }
    }
});


    // Functia de contorizare
function contorizarePrezentaAbsenta(carduriCopii) {
    if (carduriCopii.length === 0) {
        console.log("Niciun card cu clasa .card-copil nu a fost găsit.");
    } else {
        console.log("Numarul de carduri de copii: ", carduriCopii.length);
    }

    // Creăm un array gol pentru a colecta datele despre copii
    let dateCarduriCopii = [];

    // Parcurgem fiecare card
    carduriCopii.each(function() {
        let idCopil = $(this).attr('card-id-copil');
        let numeCopil = $(this).attr('card-nume-copil');
        let stareBulina = $(this).find('.info-copil p span').hasClass('online') ? 'prezent' : 'absent';

        // Adăugăm un obiect cu datele copilului la array
        dateCarduriCopii.push({
            id_copil: idCopil,
            nume_copil: numeCopil,
            prezenta_stare: stareBulina
        });
    });

    // Verificăm dacă există date pentru a fi trimise
    if (dateCarduriCopii.length > 0) {
        // Afișăm în consolă datele înainte de a le trimite
    console.log("Datele despre prezenta trimise la server: ", dateCarduriCopii);
        // Trimite toate datele colectate la fisierul PHP pentru a fi înregistrate în baza de date
        $.ajax({
            url: 'prezenta_grupa_clasa_copil.php',
            type: 'POST',
            data: {
                copii: dateCarduriCopii
            },
            success: function(response) {
                 // Interpretăm răspunsul ca JSON
        let numarCopii = JSON.parse(response);

        // Creăm și afișăm popup-ul
        let popup = $("<div>")
    .attr("id", "popup_message")
    .css({
        display: 'none',
        position: 'fixed',
        width: '60%',
        height: '30%',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        margin: 'auto',
        background: 'rgba(0,0,0,0.5)',
        zIndex: 1000,
        textAlign: 'center',
        color: 'white',
        fontSize: '1.5em',
        padding: '5px'
    });
let text1 = $("<div>")
    .attr("id", "text1")
    .css({marginTop: '20px'})
    .text(`Prezenta confirmata :`);
let text2 = $("<div>")
    .attr("id", "text2")
    .css({marginTop: '20px'})
    .text(`- copii prezenti = ${numarCopii.prezenti}`);
let text3 = $("<div>")
    .attr("id", "text3")
    .css({marginTop: '20px'})
    .text(`- copii absenti = ${numarCopii.absenti}`);

popup.append(text1, text2, text3);
$("body").append(popup);
popup.fadeIn(500).delay(3000).fadeOut(500, function() { $(this).remove(); });

    },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('A apărut o eroare la înregistrarea stării: ' + textStatus + ' - ' + errorThrown);
            }
        });
    } else {
        console.log("Nu există date despre copii pentru a fi trimise.");
    }
}

});


// functiile de afisare si selectare/deselectare destinatari
    function ascundeFereastraSelectie() {
        document.getElementById("fereastraSelectie").style.display = "none";
    }

    document.getElementById("mesajInput").addEventListener("input", function (event) {
        const inputText = event.target.value;

        if (inputText !== ' ' && inputText.length > 0 && !fereastraDeschisa) {
            afiseazaFereastraSelectie(inputText);
            document.getElementById("fereastraSelectie").style.display = "block";
            fereastraDeschisa = true;
        } else if (inputText.length === 0) {
            ascundeFereastraSelectie();
            fereastraDeschisa = false;
        }
    });

       // Adăugați un nou eveniment pentru butonul "Trimiteți"
document.getElementById("formularMesaj").addEventListener("submit", function(event) {
    const mesajInput = document.getElementById("mesajInput");

    // Verificați dacă caseta de textare este goală
    if (mesajInput.value.trim() === "") {
        // Anulați trimiterea formularului și afișați mesajul de avertisment
        event.preventDefault();
        alert("Incepeți să scrieți...");
    } else if (destinatari.size === 0) {
        // Verificați dacă există cel puțin un destinatar selectat
        // Anulați trimiterea formularului și afișați mesajul de avertisment
        event.preventDefault();
        alert("Selectati cel putin un destinatar !");
    } else {
        const inputDestinatari = document.createElement("input");
        inputDestinatari.type = "hidden";
        inputDestinatari.name = "destinatari";
        inputDestinatari.value = Array.from(destinatari).join(",");
        this.appendChild(inputDestinatari);
    }
});


//codul care afiseaza destinatarii prin deschiderea fesrestre de selectie
 function afiseazaFereastraSelectie(inputText, mesajCatre = '') {

    const fereastraSelectie = document.getElementById("fereastraSelectie");
    fereastraSelectie.style.display = "block";
    fereastraSelectie.innerHTML = "";

      // Dacă inputText este un spațiu, inseamna ca s-a facut clic pe un card copil sau card profesor, atunci setează fereastraDeschisa = true
    if (inputText === ' ') {
        fereastraDeschisa = true;
    }

    fetch('fetch_destinatari.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: "" // corpul trimis catre fetch_destinatari este gol pentru ca am renuntat la interpretarea primelor caractere introduse in campul de textare
    })
    .then(response => response.text())  // convert response to text
    .then(data => {

        return JSON.parse(data);  // then try to parse it as JSON
    })
    .then(data => {
        for (let utilizator of data.utilizatori_eligibili) { // modificat aici
            const p = document.createElement("p");
            p.textContent = utilizator.nume_prenume;
            p.dataset.selected = "false";
            p.classList.add("selectable-user");

            // Verifică dacă utilizatorul este conectat și adaugă bulina verde
            if (utilizator.este_conectat) {
                const onlineIndicator = document.createElement('span');
                onlineIndicator.innerHTML = '&#9679;';
                onlineIndicator.style.color = 'limegreen';
                p.appendChild(onlineIndicator);
            }

            // Dacă numele utilizatorului corespunde cu mesajCatre, selectează-l automat
            if (utilizator.nume_prenume === mesajCatre) {
                selectDestinatar(p, utilizator.id_utilizator);
            }

            p.addEventListener("click", function() {
                if (this.dataset.selected === "true") {
                    deselectDestinatar(this, utilizator.id_utilizator);
                } else {
                    selectDestinatar(this, utilizator.id_utilizator);
                }
            });
            fereastraSelectie.appendChild(p);
        }
    })
    .catch(error => console.error(error));  // log any error
}


    document.getElementById("formularMesaj").addEventListener("submit", function() {
    const inputDestinatari = document.createElement("input");
    inputDestinatari.type = "hidden";
    inputDestinatari.name = "destinatari";
    inputDestinatari.value = Array.from(destinatari).join(",");
    this.appendChild(inputDestinatari);
});

    //acest cod inchide fereastraSelectie destinatari, atunci cand se face click in afara acesteia si totodata reseteaza selectia destinatarilor
const mesajInput = document.getElementById("mesajInput");
const fereastraSelectie = document.getElementById("fereastraSelectie");

// Adăugați un nou event listener pentru caseta de textare
mesajInput.addEventListener('click', function() {
    fereastraSelectie.style.display = "block";
});

document.addEventListener('click', function(event) {
    const esteClickInFereastraSelectie = fereastraSelectie.contains(event.target);
    const esteClickInCasetaTextare = mesajInput.contains(event.target);

    // Modificați event listener-ul actual pentru a nu închide fereastra atunci când utilizatorul face clic pe caseta de textare
    if (!esteClickInFereastraSelectie && !esteClickInCasetaTextare) {
        fereastraSelectie.style.display = "none";

        const destinatariSelectati = fereastraSelectie.getElementsByClassName('selectable-user');
        for(let i = 0; i < destinatariSelectati.length; i++) {
            deselectDestinatar(destinatariSelectati[i], destinatariSelectati[i].id_utilizator);
        }
    }
});




    //functiile noi de selectare si deselectare Destinatari
    function deselectDestinatar(userElement, userId) {
    userElement.style.backgroundColor = "";
    userElement.dataset.selected = "false";
    destinatari.delete(userId);
    }

function selectDestinatar(userElement, userId) {
    userElement.style.backgroundColor = "#FFC966"; // schimbă culoarea în verde
    userElement.dataset.selected = "true";
    destinatari.add(userId);
    }


// functia care afiseaza istoricul mesajelor din chat
function afiseazaMesaje() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "preia_mesajele_chat.php", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const mesaje = JSON.parse(xhr.responseText);
            let output = "";
            for (const mesaj of mesaje) {
                const dataTrimitere = new Date(mesaj.data_trimitere);
                const dataRomana = dataTrimitere.toLocaleDateString('ro-RO', {day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false});
                output += `
                <p>
                    ${mesaj.id_expeditor == id_utilizator ? `<span style="color: gray; cursive; font-size: 18px;">Dvs.</span>` : `<span style="color: orange; cursive; font-size: 20px;">${mesaj.nume_expeditor}</span>`}
                    <span>${mesaj.mesaj}</span>
                    ${mesaj.id_destinatar == id_utilizator ? `<span style="color: gray; cursive; font-size: 18px;"> &#5139 Dvs.</span>` : `<span style="color: orange; cursive; font-size: 20px;"> &#5139; ${mesaj.nume_destinatar}</span>`}
                </p>
                <p>
                    <span style="color: gray; cursive; font-size: 14px;">Trimis la: ${dataRomana}</span>
                </p>`;
            }
            document.querySelector("#istoric_mesaje").innerHTML = output;
        }
    };
    xhr.send();
}

afiseazaMesaje(); // Apelați funcția pentru a afișa mesajele
setInterval(afiseazaMesaje, 13000); // Actualizați mesajele la fiecare 13 secunde

}); // sfarsitul functiei DOM care include intregul javascript



</script>

</body>
</html>