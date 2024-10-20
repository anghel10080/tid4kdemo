<!DOCTYPE html>
<html>
<head>
<style>
        th {
            padding: 10px 20px;
        }

        thead th {
            border-bottom: 2px solid #000;
        }
        th, td {
      text-align: center;
    }
    </style>
</head>
<body>

<h2>Formular de înscriere date utilizatori :</h2>

<form action="introdu_utilizatori.php" method="post" enctype="multipart/form-data">
  <label for="telefon">Telefon:</label><br>
  <input type="text" id="telefon" name="telefon">
 <button type="button" onclick="verificaTelefon()" style="background-color: green; color: white;">Verifică</button>
  <span>Verifică dacă este înregistrat utilizatorul</span><br>
  <label for="nume_prenume">Nume și prenume:</label><br>
  <input type="text" id="nume_prenume" name="nume_prenume"><br>
  <label for="email">Email:</label><br>
  <input type="text" id="email" name="email"><br>
  <label for="status">Status:</label><br>
  <input type="text" id="status" name="status"><br>
  <label for="nume_copil">Nume copil:</label><br>
  <input type="text" id="nume_copil" name="nume_copil"><br>
  <label for="varsta_copil">Vârsta copil:</label><br>
  <input type="text" id="varsta_copil" name="varsta_copil"><br>
  <label for="avatar">Încarcă avatar:</label><br>
  <input type="file" id="avatar" name="avatar" accept=".jpg,.png"><br>
  <img id="avatarPreview" src="" alt="Avatar" style="width:100px;height:100px;"><br>
  <div style="display: flex; align-items: center;">
    <label for="grupa_clasa_copil" style="margin-right: 10px;">Grupă/Clasă copil:</label>
   <select id="grupa_clasa_copil" name="grupa_clasa_copil" style="margin-right: 10px;">
  <?php include 'grupe_clase_existente.php';  ?>
</select>
    <button type="button" onclick="listeazaUtilizatori()" style="background-color: orange; color: white;">Listează utilizatorii</button>
  </div>
  <br>
 <button type="button" onclick="confirmSubmit()" style="background-color: #3498db; color: white;">Înscrie datele</button>

<!--afiseaza semiformularul pentru elev, daca acesta exista-->
<div id="elevForm" style="display:none;">
    <h2>Formular de înscriere pentru elev:</h2>
        <label for="telefon_elev">Telefon (Elev):</label><br>
        <input type="text" id="telefon_elev" name="telefon_elev"><br>
        <label for="nume_prenume_elev">Nume și prenume (Elev):</label><br>
        <input type="text" id="nume_prenume_elev" name="nume_prenume_elev"><br>
        <label for="email_elev">Email (Elev):</label><br>
        <input type="text" id="email_elev" name="email_elev"><br>
        <input type="hidden" id="status_elev" name="status_elev" value="elev">
  </div> <!--aici se termina semiformularul pentru date elev-->
</form>
<script>
function toggleElevForm(grupa_clasa_copil, status) {
    console.log("toggleElevForm a fost apelată cu: ", grupa_clasa_copil, status);  // pentru debugging
    if (grupa_clasa_copil.substring(0, 5) !== 'grupa' && status === 'parinte') {
        document.getElementById('elevForm').style.display = 'block';
    } else {
        document.getElementById('elevForm').style.display = 'none';
    }
}
</script>
<div id="divTabele">
    <!-- Aici vor fi încărcate tabelele -->
</div>
</body>
</html>
<script>
//functia care selecteaza grupa_clasa_copil din caseta de optiuni cu valoare deja inregistrata
function updateSelectOptions(selectId, value) {
    var select = document.getElementById(selectId);
    var options = select.options;
    var trimmedValue = value.trim();

    console.log("Trying to set value:", trimmedValue);
    console.log("Available options in the select:");

    for (var i = 0; i < options.length; i++) {
        console.log(i, options[i].value);
        if (options[i].value === trimmedValue) {
            select.selectedIndex = i;
            console.log("Match found - option set to index:", i);
            break;
        }
    }
}

    // Funcția originală pentru verificarea telefonului
  function verificaTelefon() {
    var telefon = document.getElementById("telefon").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log("Răspuns de la server: " + this.responseText);
            alert("Răspuns primit: " + this.responseText);

            try {
                var resp = JSON.parse(this.responseText);
                document.getElementById("nume_prenume").value = resp.nume_prenume || "";
                document.getElementById("email").value = resp.email || "";
                document.getElementById("status").value = resp.status || "";
                document.getElementById("nume_copil").value = resp.nume_copil || "";
                document.getElementById("varsta_copil").value = resp.varsta_copil || "";
                document.getElementById("nume_prenume_elev").value = resp.nume_copil || "";
                var status = document.getElementById("status").value || "";

                // Actualizăm selecția în elementul select pentru grupa/clasa copilului
                updateSelectOptions("grupa_clasa_copil", resp.grupa_clasa_copil);

                if (resp.temp_path) {
                    var relativePath = resp.temp_path.replace("/home/tid4kdem/public_html", "");
                    var numeAvatar = (status === "parinte") ? 'avatar_copil.png' : 'avatar_utilizator.png';
                    var avatarPath = relativePath + "./avatar_utilizator/" + numeAvatar;
                    document.getElementById('avatarPreview').src = avatarPath;
                }

                toggleElevForm(resp.grupa_clasa_copil || 'grupa', resp.status);
            } catch (e) {
                console.error("Eroare la parsarea JSON-ului: ", e);
            }
        }
    };
    xhttp.open("GET", "verifica_utilizatorul.php?check_phone=" + telefon, true);
    xhttp.send();
}

// Adăugarea listener-ului pentru selectul 'grupa_clasa_copil'
document.addEventListener('DOMContentLoaded', function() {function updateSelectOptions(selectId, value) {
    var select = document.getElementById(selectId);
    var options = select.options;

    for (var i = 0; i < options.length; i++) {
        if (options[i].value === value.trim()) {
            select.selectedIndex = i;
            break;
        }
    }
}
    document.getElementById('grupa_clasa_copil').addEventListener('change', function() {
        toggleElevForm(this.value);
    });
});

// Funcția pentru listarea utilizatorilor
function listeazaUtilizatori() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var tableHTML = "";

            // Crearea tabelei pentru profesori
            tableHTML += "<h2>Profesori</h2><table><thead><tr><th>Nr. crt.</th><th>ID</th><th>Nume</th><th>Email</th><th>Telefon</th><th>Grupa/Clasa</th><th>Temp_path</th><th>Avatar</th></tr></thead><tbody>";
            for (var i = 0; i < data.profesori.length; i++) {
                tableHTML += "<tr><td>" + (i + 1) + "</td><td>" + data.profesori[i].id_utilizator + "</td><td>" + data.profesori[i].nume_prenume + "</td><td>" + data.profesori[i].email + "</td><td>" + data.profesori[i].telefon + "</td><td>" + data.profesori[i].grupa_clasa_copil + "</td><td>" + data.profesori[i].temp_path + "</td><td>" + data.profesori[i].avatar + "</td></tr>";
            }
            tableHTML += "</tbody></table>";

            // Crearea tabelei pentru părinți
            tableHTML += "<h2>Părinți</h2><table><thead><tr><th>Nr. crt.</th><th>ID</th><th>Nume</th><th>Email</th><th>Telefon</th><th>Nume Copil</th><th>Vârsta</th><th>Grupă/Clasă</th><th>Temp_path</th><th>Avatar</th></tr></thead><tbody>";
            for (var i = 0; i < data.parinti.length; i++) {
                tableHTML += "<tr><td>" + (i + 1) + "</td><td>" + data.parinti[i].id_utilizator + "</td><td>" + data.parinti[i].nume_prenume + "</td><td>" + data.parinti[i].email + "</td><td>" + data.parinti[i].telefon + "</td><td>" + data.parinti[i].nume_copil + "</td><td>" + data.parinti[i].varsta_copil + "</td><td>" + data.parinti[i].grupa_clasa_copil + "</td><td>" + data.parinti[i].temp_path + "</td><td>" + data.parinti[i].avatar + "</td></tr>";
            }
            tableHTML += "</tbody></table>";

            document.getElementById("divTabele").innerHTML = tableHTML;
        }
    };
    xhttp.open("GET", "verifica_utilizatorul.php?list_users=true", true);
    xhttp.send();
}


//codul pentru previzualizarea imaginii avatar la incarcarea acesteia sau verificarea dupa numarul de telefon
document.addEventListener('DOMContentLoaded', function() {
    var avatarInput = document.getElementById('avatar');
    var avatarPreview = document.getElementById('avatarPreview');

    avatarInput.addEventListener('change', function() {
        var reader = new FileReader();

        reader.onload = function(e) {
            avatarPreview.src = e.target.result;
        };

        reader.readAsDataURL(avatarInput.files[0]);
    });
});

   function confirmSubmit() {
    var form = document.querySelector('form');
    var formData = new FormData(form);
    var text = 'Vă rugăm să confirmați informațiile:\n';
    for (var [key, value] of formData.entries()) {
        text += `${key}: ${value}\n`;
    }

    if (confirm(text)) {
        verificaInainteDeTrimitere(formData);
    } else {
        alert('Corectați datele după necesitate.');
    }
}

function verificaInainteDeTrimitere(formData) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "inregistreaza_utilizatorul.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            console.log("Răspuns de la server: " + this.responseText);
            var response = JSON.parse(this.responseText);
            alert("Mesaj de la server: " + response.message);
        }
    };
    xhr.send(new URLSearchParams(formData).toString());
}


function inregistreazaUtilizator(formData) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "inregistreaza_utilizatorul.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            alert(response.message);
        } else {
            alert('Eroare la server: ' + xhr.status);
        }
    };
    xhr.send(new URLSearchParams(formData).toString());
}

document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevenirea trimiterii formularului
    confirmSubmit();
});

</script>
