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

$nume_copil = get_nume_copil($_SESSION['grupa_clasa_copil']);
$file_name = create_file_name($nume_copil, $_SESSION['grupa_clasa_copil']);
?>
<script>
    const generatedFileName = "<?php echo $file_name; ?>"
</script>

<!--adaugare pachet javascript pentru gesturi tip touch screen-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>

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
        <a href="/pages/grupa_clasa_copil.php"> <!--Grupa Mica este si link de intoarcere in pagina principala-->
            <h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil'] ?? 'Nedefinit'); ?></h1>
        </a>

        <!--logo este si link de intoarcere in pagina principala Grupa Mica-->
        <a href="/pages/grupa_clasa_copil.php" target="_blank"><div class="logo"></div></a>

    </div>
    <div class="profesori-search-container">
        <div class="profesori-container"></div>
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Căutare...">
        </div>
        <div class="separator"></div>
    </div>
</header>

<!--acesta este codul pentru afisarea grilei de 3 x 3 imagini, in ordine descrescatoare-->
  <div id="image_grid_container" class="image-grid-container"></div>

<!--acesta este codul pentru fereastra de previzualizare cu elemente de navigare-->
<div id="preview-container" class="preview-container-hidden">
    <div class="preview-image-box">
            <div class="buttons-container ">
    <form action="upload.php" method="post" enctype="multipart/form-data" id="upload_form">
      <!--  <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;" />
        <label for="fileToUpload" class="upload_button">Upload</label>-->
        <button id="downloadButton" class="download_button" >Download</button>
    </form>
            </div>
        <span id="close-preview" class="close-preview">&#10008;</span>
        <span id="preview-navigation-top" class="preview-navigation preview-navigation-top">&#2017;</span>
        <img id="preview-image" class="preview-image" src="" alt="Preview">
        <span id="preview-navigation-bottom" class="preview-navigation preview-navigation-bottom">&#2012;</span>
    </div>
</div>

 <!--apelurile urmatoare asigura fluxul de : selectie, redenumire si editare nume si inregistrarea fisierelor alese de utilizator-->
     <?php echo getShowFilePreview(); ?>

    <?php echo convertAppleFileScript(); ?>

    <?php echo configurePreviewStyleScript(); ?>

    <?php echo showRenameModalScript(); ?>

    <?php echo rezultateCautareScript(); ?>


    <div class="coloane-img-prof-dvs-container">
      <div class="col-img-stanga">
          <div id="file_list_profesor">
              <!-- Aici vor fi afișate fișierele profesorilor -->
          </div>
      </div>
      <div class="col-img-dreapta">
          <div id="file_list_utilizator">
              <!-- Aici vor fi afișate fișierele dumneavoastră -->
          </div>
      </div>
    </div>



  <footer class="footer-container">
    <p>TID4K &copy; 2023 - Gradinita 65</p>
  </footer>

  <script src="imagini_activitati.js"></script>

</body>
</html>
