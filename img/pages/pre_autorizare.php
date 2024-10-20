<!DOCTYPE html>
<html>
<head>
    <title>Pre-autorizare</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<header>
  <h1 class="talk-to">
    <span>talk-to-</span>
    <img src="./tid4k.png" alt="Logo TID4K">
  </h1>
</header>
<body>
    <div class="formular">
        <div class="parinti-info">
            <p>Acesta este un proiect de comunicare digitală offline, securizată, prin care puteți accesa în detaliu informațiile legate de copilul dumneavoastră și activitățile Grădiniței 65.</p>
        </div>
        <form id="formStep1" method="POST">
            <div class="input-group">
                <label for="telefon_de_verificat">Telefon:</label>
                <input type="tel" id="telefon_de_verificat" name="telefon_de_verificat" required>
            </div>
            <button type="submit" name="submit" value="1" class="continua-button">
                <span class="continua-text">Continuă</span>
            </button>
        </form>
        <form id="formStep2" method="POST" action="../start.php" style="display: none;">
                <div class="input-group">
    <label for="nume_prenume">Nume și prenume:</label>
    <input type="text" id="nume_prenume" name="nume_prenume" required>
    </div>
            <div class="input-group">
                <label for="telefon">Telefon:</label>
                <input type="tel" id="telefon" name="telefon" required readonly>
            </div>
    <div class="input-group">
    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" required>
</div>
<div class="input-group">
    <label for="nume_copil">Numele copilului:</label>
    <input type="text" id="nume_copil" name="nume_copil">
</div>
<div class="input-group">
    <label for="varsta_copil">Vârsta copilului:</label>
    <input type="number" id="varsta_copil" name="varsta_copil" min="1" max="20">
</div>
<div class="input-group">
     <input type="hidden" id="id_utilizator" name="id_utilizator">
</div>
<div class="input-group">
    <label for="grupa_clasa_copil" class="clasa-copil">Selectați grupa copilului:</label>
    <select id="grupa_clasa_copil" name="grupa_clasa_copil">
        <option value="">Selectați grupa copilului</option>
        <option value="grupa mica">Grupa mică</option>
        <option value="grupa mijlocie">Grupa mijlocie</option>
        <option value="grupa mare">Grupa mare</option>
        <option value="clasa pregatitoare">Clasa Pregatitoare</option>
        <option value="clasa I">Clasa I-a</option>
        <option value="clasa II">Clasa II-a</option>
        <option value="clasa III">Clasa III-a</option>
        <option value="clasa IV">Clasa IV-a</option>
        <option value="clasa V">Clasa V-a</option>
        <option value="clasa VI">Clasa VI-a</option>
        <option value="clasa VII">Clasa VII-a</option>
        <option value="clasa VIII">Clasa VIII-a</option>
        <option value="clasa IX">Clasa IX-a</option>
        <option value="clasa X">Clasa X-a</option>
        <option value="clasa XI">Clasa XI-a</option>
        <option value="clasa XII">Clasa XII-a</option>
    </select>
</div>
<button type="submit" name="submit" value="1" class="continua-button">
    <span class="continua-text">Continuă</span>
</button>
        </form>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $("#formStep1").submit(function(e){
        e.preventDefault();
        // verificați numărul de telefon
        var telefon = $("#telefon_de_verificat").val();
        var regexTelefon = /^[0-9]+$/;
        if (!regexTelefon.test(telefon) || telefon.length < 10 || telefon.length > 13) {
            alert("Verificati daca numarul este corect !");
            return;
        }

        // verificăm numărul de telefon în baza de date
       $.post("verificare_telefon.php", { telefon: telefon })
.done(function(data) {
    console.log(data);
    try {
        /*var response = JSON.parse(data); */// varianta asta uneori functioneaza si ea cand continutul nu este trimis prin "application/json"
        var response = data; // varianta asta functioneaza cand continutul este trimis prin "application/json"
        if (response.error) {
            alert(response.error);
        } else {
            // umple formularul cu datele returnate
            $("#telefon").val(telefon);
            $("#nume_prenume").val(response.nume_prenume);
            $("#email").val(response.email);
            $("#nume_copil").val(response.nume_copil);
            $("#varsta_copil").val(response.varsta_copil);
            $("#grupa_clasa_copil").val(response.grupa_clasa_copil);


            // arata urmatorul pas al formularului
            $("#formStep2").show();
            $("#formStep1").hide();
            $("#id_utilizator").val(response.id_utilizator); // asta va fi ascunsa, dar imi trebuie pentru a pastra acelasi id_utilizator deja inregistrat
        }
    } catch (e) {
        alert("Eroare la parsarea răspunsului JSON: " + e.message);
    }
})
.fail(function(jqXHR, textStatus, errorThrown) {
    alert("A apărut o eroare: " + textStatus + ", " + errorThrown);
});

    });
});


</script>

</body>
</html>
