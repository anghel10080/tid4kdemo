<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';
require_once 'functii_si_constante.php';

// Aceasta parte gestioneaza cererile GET pentru verificarea numarului de telefon si trimiterea rezultatelor inapoi in functia de verificareTelefon pentru popularea cu date a formularului de introducere
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_phone'])) {
    $telefon = $_GET['check_phone'];
    $sql_check = "SELECT * FROM utilizatori WHERE telefon = ?";

    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $telefon);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row['status'] !== 'parinte') {
                $sql_check_association = "SELECT grupa_clasa_copil FROM asociere_multipla WHERE id_utilizator = ? LIMIT 1";

                if ($stmt_check_association = $conn->prepare($sql_check_association)) {
                    $stmt_check_association->bind_param("i", $row['id_utilizator']);
                    $stmt_check_association->execute();
                    $result_association = $stmt_check_association->get_result();

                    if ($result_association->num_rows > 0) {
                        $row_association = $result_association->fetch_assoc();
                        $row['grupa_clasa_copil'] = $row_association['grupa_clasa_copil'];
                    }

                    $stmt_check_association->close();
                }

                echo json_encode($row);

            } else {
                $sql_child = "SELECT * FROM copii WHERE id_utilizator = ?";

                if ($stmt_child = $conn->prepare($sql_child)) {
                    $stmt_child->bind_param("i", $row['id_utilizator']);
                    $stmt_child->execute();
                    $result_child = $stmt_child->get_result();

                    if ($result_child->num_rows > 0) {
                        $row_child = $result_child->fetch_assoc();
                        $row = array_merge($row, $row_child);
                    }

                    $stmt_child->close();
                }

                echo json_encode($row);
            }
        } else {
            echo json_encode(["error" => "Numarul de telefon nu a fost gasit."]);
        }

        $stmt_check->close();
    }
    exit; // Important să încheiem execuția aici pentru a nu procesa și partea de POST
}

//aceasta parte gestioneaza cererile GET pentru listarea utilizatorilor
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list_users'])) {
    $response = [];

    // Selectăm doar câmpurile necesare pentru profesori
    $sql_profesori = "SELECT id_utilizator, nume_prenume, email, telefon FROM utilizatori WHERE status = 'profesor'";
    if ($result_profesori = $conn->query($sql_profesori)) {
        $profesori = $result_profesori->fetch_all(MYSQLI_ASSOC);
        $response['profesori'] = $profesori;
    }

    // Selectăm doar câmpurile necesare pentru părinți și facem JOIN cu tabelul copii pentru a obține informații despre copii
    $sql_parinti = "SELECT u.id_utilizator, u.nume_prenume, u.email, u.telefon, c.nume_copil, c.varsta_copil, c.grupa_clasa_copil
                    FROM utilizatori u
                    JOIN copii c ON u.id_utilizator = c.id_utilizator
                    WHERE u.status = 'parinte'";
    if ($result_parinti = $conn->query($sql_parinti)) {
        $parinti = $result_parinti->fetch_all(MYSQLI_ASSOC);
        $response['parinti'] = $parinti;
    }

    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_copil_curent = NULL;
    $nume_prenume = $_POST['nume_prenume'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $nume_copil = $_POST['nume_copil'];
    $varsta_copil = $_POST['varsta_copil'];
    $grupa_clasa_copil = $_POST['grupa_clasa_copil'];
    $avatar = $_FILES['avatar'];
    //si datelele trimise prin semiformularul pentru Elev
    $telefon_elev = $_POST['telefon_elev'];
    $nume_prenume_elev = $_POST['nume_prenume_elev'];
    $email_elev = $_POST['email_elev'];
    $status_elev = $_POST['status_elev'];

     ?> <!--aici se trimite $status catre codul javascript care va popula formularul cu datele din baza de date-->
    <script type="text/javascript">
    var status = "<?php echo isset($status) ? $status : 'default'; ?>";
    </script>
    <?php

      $id_cookie = '';
    // Verifică dacă numărul de telefon există deja în baza de date
    $sql_check = "SELECT * FROM utilizatori WHERE telefon = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("s", $telefon);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_cookie = $row['id_cookie'];
            $id_utilizator = $row['id_utilizator'];
            $id_utilizator_curent = $id_utilizator;
            $status = $row['status'];

             // Actualizăm datele utilizatorului
    $sql_update = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ?, status = ? WHERE id_utilizator = ?";
    if ($stmt_update = $conn->prepare($sql_update)) {
        $stmt_update->bind_param("ssssi", $nume_prenume, $telefon, $email, $status, $id_utilizator);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // Verificăm dacă copilul este deja înregistrat pentru acest utilizator
if ($status === 'parinte') {
    $id_copil = NULL;  // Inițializează id_copil cu NULL
    $sql_check_child = "SELECT * FROM copii WHERE id_utilizator = ?";
    if ($stmt_check_child = $conn->prepare($sql_check_child)) {
        $stmt_check_child->bind_param("i", $id_utilizator);
        $stmt_check_child->execute();
        $result_child = $stmt_check_child->get_result();

        if ($result_child->num_rows > 0) {
            $row = $result_child->fetch_assoc();
            $id_copil_curent = $row['id_copil'];
            // Actualizăm datele copilului
            $sql_update_child = "UPDATE copii SET nume_copil = ?, varsta_copil = ?, grupa_clasa_copil = ? WHERE id_utilizator = ?";
            if ($stmt_update_child = $conn->prepare($sql_update_child)) {
                $stmt_update_child->bind_param("sisi", $nume_copil, $varsta_copil, $grupa_clasa_copil, $id_utilizator);
                $stmt_update_child->execute();
                $stmt_update_child->close();
            }
        }
        $stmt_check_child->close();
    }
}

//aici este nevoie de asociere_multipla si se apeleaza functia gestioneaza_asocierea_multipla()
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);

   // Actualizare avatar
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];

    // Calea către directorul unde este deja salvat avatarul
    $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";


    // Determinăm numele avatarului în funcție de $status
    $nume_avatar = $status === "parinte" ? 'avatar_copil.png' : 'avatar_utilizator.png';


    // Numele fișierului avatar va rămâne constant
    $avatar_path = $cale_avatar . $nume_avatar;

    // Mutăm fișierul încărcat în locația existentă, suprascriind fișierul vechi
    if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
        echo "Avatar actualizat cu succes.";
    } else {
        echo "Eroare la încărcarea avatarului.";
    }
}

echo "Utilizatorul ".$nume_prenume." a fost updatat !";

//daca au fost introduse datele elevului aici sunt inregistrate in baza de date
if (isset($status_elev) && $status_elev == 'elev') {
    // Verifică dacă telefonul_elev există în baza de date
    $sql_check_elev = "SELECT id_utilizator FROM utilizatori WHERE telefon = ?";
    $stmt_check_elev = $conn->prepare($sql_check_elev);
    $stmt_check_elev->bind_param("s", $telefon_elev);
    $stmt_check_elev->execute();
    $stmt_check_elev->bind_result($id_utilizator_elev);
    $stmt_check_elev->fetch();
    $stmt_check_elev->close();

    if (isset($id_utilizator_elev)) {
        // Actualizează datele pentru elev
        $sql_update_elev = "UPDATE utilizatori SET nume_prenume = ?, telefon = ?, email = ?, status = ? WHERE id_utilizator = ?";
        $stmt_update_elev = $conn->prepare($sql_update_elev);
        $stmt_update_elev->bind_param("ssssi", $nume_prenume_elev, $telefon_elev, $email_elev, $status_elev, $id_utilizator_elev);
        $stmt_update_elev->execute();
        $stmt_update_elev->close();
    } else {
        // Inserarea noilor date pentru un elev nou
        $sql_insert_elev = "INSERT INTO utilizatori (nume_prenume, telefon, email, status) VALUES (?, ?, ?, ?)";
        $stmt_insert_elev = $conn->prepare($sql_insert_elev);
        $stmt_insert_elev->bind_param("ssss", $nume_prenume_elev, $telefon_elev, $email_elev, $status_elev);
        $stmt_insert_elev->execute();
        $stmt_insert_elev->close();
    }
}
            }

     else {

    // Dacă id_cookie nu există, generăm unul nou
    if (empty($id_cookie)) {
        $id_cookie = uniqid();
        setcookie('id_cookie', $id_cookie, time() + 60 * 60 * 24 * 365, "/"); // Setează cookie-ul pentru 1 an
    }


    // Creați o interogare SQL pentru a introduce datele în tabela utilizatori
    $sql = "INSERT INTO utilizatori (nume_prenume, telefon, email, status, id_cookie) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nume_prenume, $telefon, $email, $status, $id_cookie);

    if ($stmt->execute()) {
        // Obțineți id-ul utilizatorului introdus
        $id_utilizator = $conn->insert_id;
        $id_utilizator_curent = $id_utilizator;

        // Creare temp_path
       $temp_path = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/";

// Creare director dacă nu există
if (!is_dir($temp_path)) {
    mkdir($temp_path, 0777, true);
}

    // Adăugare temp_path în utilizatori
    $sql_temp_path = "UPDATE utilizatori SET temp_path = ? WHERE id_utilizator = ?";
    $stmt_temp_path = $conn->prepare($sql_temp_path);
    $stmt_temp_path->bind_param("si", $temp_path, $id_utilizator);


        if ($stmt_temp_path->execute()) {
            // Creați calea către avatarul utilizatorului
            $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";
 // Determinăm numele avatarului în funcție de $status
    $nume_avatar = $status === "parinte" ? 'avatar_copil.png' : 'avatar_utilizator.png';

            // Creați un director dacă nu există deja
            if (!is_dir($cale_avatar)) {
                $oldmask = umask(0); // Salvăm masca curentă și setăm umask la 0
                if (mkdir($cale_avatar, 0777, true)) { // Creăm directorul cu permisiunile dorite
                    umask($oldmask); // Revenim la vechea mască
                } else {
                    echo "Eroare la crearea directorului: " . $cale_avatar;
                    umask($oldmask); // Revenim la vechea mască chiar și în caz de eroare
                    exit;
                }
            }

 if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];

    // reluam calea către avatarul utilizatorului
    $cale_avatar = "/home/tid4kdem/public_html/sesiuni/tid4K_" . $id_cookie . "/avatar_utilizator/";

    // Numele fișierului avatar va rămâne constant
    $avatar_path = $cale_avatar . $nume_avatar;

    // Mutăm fișierul încărcat în locația destinată
    if (move_uploaded_file($avatar_tmp_name, $avatar_path)) {
        echo "Avatar încărcat cu succes.";
    } else {
        echo "Eroare la încărcarea avatarului.";
        // exit;
    }
} else {
    // Dacă nu s-a încărcat niciun avatar, folosim unul predefinit
     // Numele fișierului avatar va rămâne constant
    $avatar_path = $cale_avatar . $nume_avatar;
    $content = file_get_contents("/home/tid4kdem/public_html/pages/avatar_copil.png");
    $result = file_put_contents($avatar_path . 'avatar_copil.png', $content);
    if ($result === false) {
        echo "Eroare la copierea fișierului avatar_copil.png.";
        exit;
    }
}
            if ($result === false) {
                echo "Eroare la copierea fișierului avatar_copil.png.";
                exit;
            }

            // Introduceți datele în tabela copii
            if ($status == 'parinte') {
    $sql = "INSERT INTO copii (id_utilizator, nume_copil, varsta_copil, grupa_clasa_copil, avatar_utilizator) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $id_utilizator, $nume_copil, $varsta_copil, $grupa_clasa_copil, $cale_avatar);

    if ($stmt->execute()) {
        $id_copil_curent = $conn->insert_id;
        // Redirecționați utilizatorul către pagina de succes dacă datele au fost introduse cu succes
        echo "Datele au fost introduse complet: parinte si copil";
        sleep(3);
        //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
        exit;
    }
}
 else {
     //algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);
            }
        } else {
            echo "Eroare la adăugarea temp_path: " . $conn->error;
        }
        $stmt_temp_path->close();
        $stmt->close();
    } else {
        echo "Eroare la introducerea datelor utilizatorului: " . $conn->error;
    }
    }$stmt_check->close();

}

//algoritmul care leaga utilizatorii multiplii (profesori, administrativ) de copii
asociere_multipla_profesori_administrativ_la_copii($conn, $status, $id_copil_curent, $id_utilizator_curent, $grupa_clasa_copil);

}
?>
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
<script>
    // Funcția originală pentru verificarea telefonului
    function verificaTelefon() {
        var telefon = document.getElementById("telefon").value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var resp = JSON.parse(this.responseText);
                document.getElementById("nume_prenume").value = resp.nume_prenume || "";
                document.getElementById("email").value = resp.email || "";
                document.getElementById("status").value = resp.status || "";
                document.getElementById("nume_copil").value = resp.nume_copil || "";
                document.getElementById("varsta_copil").value = resp.varsta_copil || "";
                document.getElementById("grupa_clasa_copil").value = resp.grupa_clasa_copil || "grupa mica";
                document.getElementById("nume_prenume_elev").value = resp.nume_copil || "";
                var status = document.getElementById("status").value || "";
                // Dacă calea 'temp_path' există, actualizăm previzualizarea avatarului
             if (resp.temp_path) {
                 console.log("Status este: ", status);

    // Tăiem partea absolută a căii
    var relativePath = resp.temp_path.replace("/home/tid4kdem/public_html", "");

    // Determinăm numele avatarului în funcție de status
    var numeAvatar = (status === "parinte") ? 'avatar_copil.png' : 'avatar_utilizator.png';

    // Concatenăm cu restul căii pentru a ajunge la imaginea avatarului
    var avatarPath = relativePath + "./avatar_utilizator/" + numeAvatar;

    // Setăm sursa pentru previzualizarea avatarului
    document.getElementById('avatarPreview').src = avatarPath;
                        }

        // Verificarea și afișarea semiformularului pentru elev
            toggleElevForm(resp.grupa_clasa_copil || 'grupa', resp.status);
            }
        };
        xhttp.open("GET", "introdu_utilizatori.php?check_phone=" + telefon, true);
        xhttp.send();
    }
// Adăugarea listener-ului pentru selectul 'grupa_clasa_copil'
document.addEventListener('DOMContentLoaded', function() {
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
</script>

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
    <?php GrupeClaseDisponibile(); ?>
</select>
    <button type="button" onclick="listeazaUtilizatori()" style="background-color: orange; color: white;">Listează utilizatorii</button>
  </div>
  <br>
 <input type="submit" value="Înscrie datele" style="background-color: #3498db; color: white;">
<!--afiseaza semiformularul pentru elev, daca elevul exista-->
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
