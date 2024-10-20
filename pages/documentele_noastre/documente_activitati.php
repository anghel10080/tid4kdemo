<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Accesarea directorului părinte și adăugarea căii la config.php
require_once(dirname(__DIR__, 2) . '/config.php'); // Acesta va defini ROOT_PATH

require_once(ROOT_PATH . 'pages/functii_si_constante.php');
require_once('redenumire_fisiere.php');

  // Apelarea functiei pentru a umple variabilele de sesiune, inclusa in functii_si_constante.php
  determina_variabile_utilizator($conn);

$id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];

$nume_copil = get_nume_copil($_SESSION['grupa_clasa_copil']);
$file_name = create_file_name($nume_copil, $_SESSION['grupa_clasa_copil']);
?>
<script>
    const generatedFileName = "<?php echo $file_name; ?>"
</script>
<!DOCTYPE html>
<html>
<head>
  <title>TID4K - <?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>">
  <link rel="icon" type="image/png" href="/favicon.ico">
  <script src="heic2any.min.js"></script>

</head>
<body>

<header class="header-container">
    <div class="grupa-clasa-copil-wrapper">
        <a href="/pages/grupa_clasa_copil.php">
            <h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></h1>
        </a>

        <a href="/pages/grupa_clasa_copil.php" ><div class="logo"></div></a>
    </div>
    <div class="profesori-search-container">
        <div class="profesori-container"></div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Căutare...">
        </div>
        <div class="separator"></div>
    </div>
</header>

<!--aici este codul HTML pentru coloana din stanga, care contine fereastra de afisare a continutului fisierului selectat si butoanele de upload si download-->
    <div class="column-left">
       <h2 class="col-stanga-titlu" id="col-stanga-titlu">Ce am făcut de curând:</h2>
       <!--afiseaza titlul continutului peste fereastra de afisare iframe sau img-->
           <div id="file-title-container" style="position: absolute; display: flex; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.95); z-index: 3; left: 50%; transform: translateX(-50%); <!--border: 2px solid; border-color: yellow;-->">
            <p id="file-title"></p>
            </div>


         <iframe id="last_uploaded_file" class="fixed-iframe" style="display: none; width: 100%; height: 460px; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);"></iframe>
    <?php
    echo genereazaOverlayIframe(); // Apelul funcției pentru generarea overlay-ului pentru iframe
    echo overlayIframe(); //apelul functiei pentru ascultarea de clic pe overlay, download sau delete
    ?>
         <img id="last_uploaded_image" class="fixed-img" src="" style="display: none; width: 100%; height: auto; border: 1px solid orange; box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);">
    <?php
    echo genereazaOverlayImg(); // Apelul funcției pentru generarea overlay-ului pentru img
    echo overlayImg(); //apelul functiei pentru ascultarea de clic pe overlay, download sau delete
    ?>
  
    </div>

<!--   aici este codul HTML pentru coloana din dreapta care afiseaza intreaga lista a fisierelor de la utilizator si profesori-->
    <div class="column-right">
      <h2 class="col-dreapta-titlu" id="col-dreapta-titlu">Toate documentele:</h2>
        <div id="files-column" class="files-column"> <!--acest div se ocupa in style.css de adaptarea automata a fontului la browserulmobil-->
            <div id="file_list_utilizator"></div>
            <div id="file_list_ceilalti"></div>
        </div>
    </div>

  <footer class="footer-container">
    <p>TID4K &copy; 2024</p>
  </footer>

<script src="documente_activitati.js"></script>

</body>
</html>
