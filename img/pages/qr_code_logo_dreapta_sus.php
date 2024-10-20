<!DOCTYPE html>
<html>
<head>
    <title>Logo și QR în Dreapta Sus</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
            font-family: Arial, sans-serif;
        }
        .logo-and-qr {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px; /* Spațiu în jurul logo-ului și QR pentru a nu atinge marginea paginii */
        }
        .logo {
            height: auto; /* Înălțimea auto pentru a preveni deformarea */
            width: 170px; /* Lățimea stabilită a logo-ului */
            box-shadow: none; /* Asigurați-vă că nu există umbre aplicate logo-ului */
            background: none; /* Fără fundal */
            border: none; /* Fără borduri */
        }
        .qr-code {
            position: absolute;
            width: 90px; /* Lățimea QR code */
            height: auto; /* Înălțimea auto pentru a preveni deformarea */
            display: flex;
            align-items: center;
            justify-content: center;
            left: calc(50% + var(--qr-offset-x, 0px)); /* Ajustează poziția pe orizontală prin --qr-offset-x */
            top: var(--qr-offset-y, 0px); /* Ajustează poziția pe verticală prin --qr-offset-y */
        }
        .qr-code img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="logo-and-qr" style="--qr-offset-x: -129px; --qr-offset-y: 31px;"> <!-- Parametri ajustabili pentru poziționarea QR code -->
        <img class="logo" src="./tid4k.png" alt="Logo TID4K">
        <div class="qr-code">
            <a href="http://tid4kg122.ro/config_sesiuni.php" target="_blank">
                <img src="url_qr.png" alt="QR Code" />
            </a>
        </div>
    </div>
</body>
</html>
