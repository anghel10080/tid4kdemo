<!DOCTYPE html>
<html>
<head>
    <title>Pre-autorizare</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background-color: white;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .talk-to-text {
            font-size: 26px;
            color: #2b516a; /* main blue */
            position: relative;
            left: -150px;
            transform: translateY(174px);
            text-align: left;
        }
        .logo-and-qr {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px; /* Adăugat pentru a evita suprapunerea cu butonul */
        }
        .logo {
            height: 170px;
        }
        .qr-code {
            position: absolute;
            width: 100px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            left: 50%;
            margin-left: -35px;
            transform: translateY(7%);
        }
        .qr-code img {
            width: 100%;
            height: auto;
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            margin-left: 26px;
            z-index: 1;
        }

        .disabled {
        background-color: grey;
        cursor: not-allowed;
    }
        .contact-button {
    background-color: #2b516a; /* main blue */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    margin-left: 30px; /* Ajustează această valoare pentru a deplasa butonul spre dreapta */
}

        /* Stil pentru fereastra modală */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="header-content">
        <div class="talk-to-text">talk-to</div>
        <div class="logo-and-qr">
            <img class="logo" src="tid4k_cu_umbra.png" alt="Logo TID4K">
            <div class="qr-code">
                <a href="http://tid4kdemo.ro/config_sesiuni.php" target="_blank">
                    <img src="url_qr.png" />
                </a>
            </div>
        </div>
        <!--<a href="http://82.77.117.4:4173/" class="button">Înscrierea la Grădinița DEMO</a>-->
        <!-- Butonul pentru contact -->
        <!--<button class="contact-button" onclick="openModal()">Contact</button>-->
    </header>

 <!-- Fereastra modală pentru informațiile de contact -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Project Antrepreneur</h2>
        <!-- Imaginea personală deja urcată în directorul părinte -->
        <img src="imaginea_mea.png" alt="Cornel Ilie" style="width:100px;height:auto;border-radius:50%;">
        <p>Anghel Cornel Ilie</p>
        <p>Telefon: +40758797979</p>
    </div>
</div>


    <script>
        // Funcție pentru deschiderea ferestrei modale
        function openModal() {
            document.getElementById("contactModal").style.display = "flex";
        }

        // Funcție pentru închiderea ferestrei modale
        function closeModal() {
            document.getElementById("contactModal").style.display = "none";
        }
    </script>
</body>
</html>
