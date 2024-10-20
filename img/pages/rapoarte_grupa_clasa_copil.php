    <?php

    if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    require_once '../config.php';
    require_once '../sesiuni.php';
    require_once 'functii_si_constante.php';

  // Apelarea functiei pentru a umple variabilele de sesiune: id_utilizator, status, grupa_clasa_copil
  determina_variabile_utilizator($conn);
 $status = $_SESSION['status'];

 $id_cookie = $_SESSION['id_cookie'];
$status = $_SESSION['status'];
$id_utilizator = $_SESSION['id_utilizator'];
$grupa_clasa_copil = $_SESSION['grupa_clasa_copil'];
$grupa_clasa_copil_ = $_SESSION['grupa_clasa_copil_'];
$grupa_clasa_copil_curent = $_SESSION['grupa_clasa_copil_'];
$alias = 'alias';

  ?>

<!DOCTYPE html>
<html>
<head>
    <title>TID4K - <?php echo strtoupper($_SESSION['grupa_clasa_copil']); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   </head>
<body>
<header>
  <div class="header-container">
    <a href="/pages/grupa_clasa_copil.php"><h1 class="nume-grupa"><?php echo strtoupper($_SESSION['grupa_clasa_copil']); ?></h1></a>
    <a href="/pages/grupa_clasa_copil.php" ><div class="logo"></div></a>
  </div>
</header>
<main>
<div class="fixed-title">
  <h2 class="titlu-prezenta">Prezenta copiilor si contributia: <button onclick="printPage()" class="print-button">Print</button></h2>
</div>
<?php

   // Selectează toate datele unice de prezență
    $sql = "SELECT DISTINCT prezenta_data FROM prezenta_" . $_SESSION['grupa_clasa_copil_'] . " ORDER BY prezenta_data DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prezenta_data = $row['prezenta_data'];
       if ($status == "parinte") {
    $sql2 = "SELECT c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil WHERE pgm.prezenta_data = ? AND c.id_utilizator = ? ORDER BY pgm.prezenta_stare ASC, c.nume_copil";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("si", $prezenta_data, $id_utilizator);
} elseif ($status == "elev") {
    $sql2 = "SELECT c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil JOIN asociere_multipla a ON a.id_copil=c.id_copil WHERE pgm.prezenta_data = ? AND a.id_utilizator = ? AND a.grupa_clasa_copil = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("sis", $prezenta_data, $id_utilizator, $_SESSION['grupa_clasa_copil_']);
} else {
    $sql2 = "SELECT c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil WHERE pgm.prezenta_data = ? ORDER BY pgm.prezenta_stare ASC, c.nume_copil";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $prezenta_data);
}

$stmt2->execute();
$result2 = $stmt2->get_result();
$prezenta_info = $result2->fetch_assoc();


          $color_class = '';
            if ($status == "parinte") {
               if (isset($prezenta_info) && $prezenta_info['prezenta_stare'] == 'prezent') {
    if(isset($prezenta_info['confirmata_la']) && strtotime($prezenta_info['confirmata_la']) > strtotime('09:15:00')) {
        $color_class = 'late';
    } else {
        $color_class = 'present';
    }
} else {
    $color_class = 'absent';
}

            }


            echo "<h3 class='data-prezenta $color_class' data-data-prezenta='$prezenta_data'>" . date("d-m-Y", strtotime($prezenta_data)) . "</h3>";

      //pentru parinte este vizibila doar prezenta propriului copil
            if ($status == "parinte") {
    $sql2 = "SELECT c.id_copil, c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil WHERE pgm.prezenta_data = ? AND c.id_utilizator = ? ORDER BY pgm.prezenta_stare ASC, c.nume_copil";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("si", $prezenta_data, $id_utilizator);
        } elseif ($status == "elev") {
    $sql2 = "SELECT c.id_copil, c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil JOIN asociere_multipla a ON a.id_copil = c.id_copil WHERE pgm.prezenta_data = ? AND a.id_utilizator = ? ORDER BY pgm.prezenta_stare ASC, c.nume_copil";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("si", $prezenta_data, $id_utilizator);
        } else {
    $sql2 = "SELECT c.id_copil, c.nume_copil, pgm.prezenta_stare, pgm.confirmata_la, pgm.confirmata_de, pgm.prezenta_data FROM copii c JOIN prezenta_" . $_SESSION['grupa_clasa_copil_'] . " pgm ON c.id_copil = pgm.id_copil WHERE pgm.prezenta_data = ? ORDER BY pgm.prezenta_stare ASC, c.nume_copil";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $prezenta_data);
    }
    $stmt2->execute();
    $result2 = $stmt2->get_result();

          echo "<div class='report-container'>";
          echo "<table class='tabel-prezenta' style='display: none;'>";
          echo "<tr><th>Nume copil</th><th>Prezenta</th><th>Confirmata la</th><th>Confirmata de</th><th>Contributia</th></tr>";

    $prezenti = 0;
    $absenti = 0;

    while($row2 = $result2->fetch_assoc()) {

    // Extrage id_copil mai întâi.
    $id_copil = $row2['id_copil'];

    $prezenta_status_class = $row2['prezenta_stare'] == 'prezent' ? 'present' : 'absent';

    $sql3 = "SELECT status FROM utilizatori WHERE nume_prenume = ?";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param("s", $row2['confirmata_de']);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    $row3 = $result3->fetch_assoc();
    if (!isset($row3['status'])) {
        $row3['status'] = date('Y-m-d H:i:s');
    }

    $confirmata_la = isset($row2['confirmata_la']) && $row2['confirmata_la'] !== null ? $row2['confirmata_la'] : $row2['prezenta_data'];
    $confirmata_de = isset($row2['confirmata_de']) && $row2['confirmata_de'] !== null ? $row2['confirmata_de'] : 'profesor';

    $late_class = '';
    if($row2['prezenta_stare'] == 'prezent') {
        $late_class = strtotime($confirmata_la) > strtotime('09:15:00') && $row3['status'] == 'parinte' ? 'late' : '';
    }



// Calculul contribuției
$prezenta_luna = date("Y-m", strtotime($prezenta_data));

// Extragem anul și luna din variabila $prezenta_luna
$year = date("Y", strtotime($prezenta_luna));
$month = date("m", strtotime($prezenta_luna));

// Interogarea SQL pentru numărul de prezențe al unui copil într-o anumită lună
$sql4 = "SELECT COUNT(*) AS numar_prezente FROM prezenta_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND YEAR(prezenta_data) = ? AND MONTH(prezenta_data) = ? AND prezenta_stare = 'prezent'";
$stmt4 = $conn->prepare($sql4);
$stmt4->bind_param("iii", $id_copil, $year, $month);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = $result4->fetch_assoc();
$numar_prezente = $row4['numar_prezente'];

// contributia stabilita se extrage automat (require_once) din functii_si_constante.php, unde este definita pentru fiecare gradinita in parte

// Calculul valorii contributiei
$contributia = $numar_prezente * $contributia_stabilita;

// Denumiri lunilor în limba română
$luni = ["Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];

echo "<tr data-id-copil='$id_copil'><td>" . $row2['nume_copil'] . "</td><td class='$prezenta_status_class $late_class'>" . $row2['prezenta_stare'] . "</td><td>" . $confirmata_la . "</td><td>" . $confirmata_de . "</td><td>" . $contributia . " lei (" . $numar_prezente . " prezențe)</td></tr>";
//tabelul lunar corelat cu numarul de prezente si contributia
echo "<tr class='lunar-report-container' data-id-copil='$id_copil' style='display: none;'><td colspan='5'><table class='tabel-lunar-prezenta'></tr>";

echo "<tr><th>Luna</th><th>Prezente</th><th>Contributia</th><th>Contributia Platita</th><th>Numar Chitanta</th></tr>";

for ($i = 1; $i <= date("m"); $i++) {
    $sql_luna = "SELECT COUNT(*) AS numar_prezente FROM prezenta_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND YEAR(prezenta_data) = ? AND MONTH(prezenta_data) = ? AND prezenta_stare = 'prezent' AND prezenta_stare IS NOT NULL";
    $stmt_luna = $conn->prepare($sql_luna);
    $stmt_luna->bind_param("iii", $id_copil, $year, $i);
    $stmt_luna->execute();
    $result_luna = $stmt_luna->get_result();
    $row_luna = $result_luna->fetch_assoc();
    $numar_prezente_luna = $row_luna['numar_prezente'];
    $contributia_luna = $numar_prezente_luna * $contributia_stabilita;

    $luna_nume = $luni[$i - 1];  // Folosirea denumirii lunii din array-ul $luni

    // Încercare de inserare sau actualizare
    $sql_insert_update = "INSERT INTO contributia_" . $_SESSION['grupa_clasa_copil_'] . " (id_copil, luna, numar_prezente, contributia_stabilita, contributia)
                          VALUES (?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE numar_prezente = VALUES(numar_prezente), contributia_stabilita = VALUES(contributia_stabilita), contributia = VALUES(contributia)";

    $stmt_insert_update = $conn->prepare($sql_insert_update);
    $stmt_insert_update->bind_param("isidd", $id_copil, $luna_nume, $numar_prezente_luna, $contributia_stabilita, $contributia_luna);

    if ($stmt_insert_update->execute()) {
        // Valorile au fost inserate sau actualizate cu succes.
    } else {
        echo "Eroare la inserarea sau actualizarea valorilor: " . $stmt_insert_update->error;
    }

// interogare pentru platile asociate cu luna și copilul respectiv în `contributia_" . $_SESSION['grupa_clasa_copil_'] . "`
$sql_contrib = "SELECT contributia_platita, numar_chitanta, data_platii, diferenta_contributie FROM contributia_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND luna = ?";
$stmt_contrib = $conn->prepare($sql_contrib);
$stmt_contrib->bind_param("is", $id_copil, $luna_nume);
$stmt_contrib->execute();
$result_contrib = $stmt_contrib->get_result();
$row_contrib = $result_contrib->fetch_assoc();

$contributia_platita = $row_contrib['contributia_platita'] ?? "Valoare Platita + Data";
$numar_chitanta = $row_contrib['numar_chitanta'] ?? "Numar Chitanta";
$data_platii = $row_contrib['data_platii'] ?? "";
$diferenta_contributie = $row_contrib['diferenta_contributie'] ?? 0; // Inițializat la 0 dacă nu există în baza de date

//aici urmeaza o exceptie pentru determinarea diferenta_contributie care sa permita actualizarea in tabel in functie de schimbarea numarului de prezente pentru fiecare copil
$lunile = [1 => "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie", "Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie"];
$luna_curenta_index = intval(date("m"));
$luna_curenta_nume = $lunile[$luna_curenta_index];

if ($luna_nume === $luna_curenta_nume) {
    $luna_anterioara_index = $luna_curenta_index - 1;

    // Dacă luna curentă este Ianuarie, atunci luna anterioară este Decembrie.
    if ($luna_anterioara_index === 0) {
        $luna_anterioara_index = 12;
    }

    // Aici setăm numele pentru luna anterioară
    $luna_anterioara_nume = $lunile[$luna_anterioara_index];

    $sql_contrib_anterior = "SELECT diferenta_contributie FROM contributia_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND luna = ?";
    $stmt_contrib_anterior = $conn->prepare($sql_contrib_anterior);
    $stmt_contrib_anterior->bind_param("is", $id_copil, $luna_anterioara_nume);
    $stmt_contrib_anterior->execute();
    $result_contrib_anterior = $stmt_contrib_anterior->get_result();
    $row_contrib_anterior = $result_contrib_anterior->fetch_assoc();

    $diferenta_contributie_anterioara = $row_contrib_anterior['diferenta_contributie'] ?? 0;

    $diferenta_contributie_actuala = $row_contrib['contributia_platita'] - $contributia_luna + $diferenta_contributie_anterioara;
    $diferenta_contributie = $diferenta_contributie_actuala;

} else {
    $diferenta_contributie = $row_contrib['diferenta_contributie'] ?? 0;
} // aici se termina exceptia determinarii diferenta_contributie actualizata


if ($data_platii) {
    $data_platii_obj = new DateTime($data_platii);
    $data_platii_formatata = $data_platii_obj->format('d-m-Y H:i'); // zi-luna-an ora:minute
}

$zile_lucratoare = zileLucratoareDinLuna(date('n'), date('Y'));

// Calculul sumei totale de plată
$total_de_plata = ($zile_lucratoare * $contributia_stabilita) - $diferenta_contributie_anterioara;

if ($i == date('n')) {
    // Afisare contribuție plătită sau total de plată doar pentru luna și anul curent
    if ($contributia_platita !== "Valoare Platita + Data" && $data_platii) {
        $contributia_platita_display = $contributia_platita . " lei / " . $data_platii_formatata;
    } elseif ($contributia_platita === "Valoare Platita + Data") {
        $contributia_platita_display = "<span class='total-de-plata-lunar'>Total de plata = " . $total_de_plata . " lei</span>";
    } else {
        $contributia_platita_display = $contributia_platita;
    }
} else {
    // Comportamentul inițial pentru luni diferite de cea curentă
    $contributia_platita_display = ($contributia_platita !== "Valoare Platita + Data" && $data_platii)
                                   ? $contributia_platita . " lei / " . $data_platii_formatata
                                   : $contributia_platita;
}

// Calculul și formatarea diferenței pentru afișare
$afisare_diferenta = "";
if ($diferenta_contributie > 0) {
  $afisare_diferenta = " (+" . $diferenta_contributie . " lei)";
} elseif ($diferenta_contributie < 0) {
  $afisare_diferenta = " (-" . abs($diferenta_contributie) . " lei)";
} // Nicio acțiune dacă $diferenta_contributie = 0

// Aici folosim variabilele pe care le-am setat mai sus
echo "<tr data-luna = '$luna_nume'>";
echo "<td>" . $luni[$i - 1] . "</td>";
echo "<td>" . $numar_prezente_luna . "</td>";

// Afișează valoarea contribuției în bold
echo "<td><span class='contributia-bold'>" . $contributia_luna . " lei</span>";

// Dacă diferenta_contributie este negativă, folosește clasa 'diferenta-negativa'; dacă este pozitivă, folosește 'diferenta-pozitiva'
$diferenta_clasa = $diferenta_contributie < 0 ? "diferenta-negativa" : ($diferenta_contributie > 0 ? "diferenta-pozitiva" : "");

echo " <span class='$diferenta_clasa'>" . $afisare_diferenta . "</span></td>";

// Afișează contributia_platita în bold
echo "<td class='editabil contributie-platita contributia-platita-bold'>" . $contributia_platita_display . "</td>";

echo "<td class='editabil numar-chitanta'>" . $numar_chitanta . "</td>";
echo "</tr>";




// Aici începe tabelul pentru zilele-prezenta-lunar
    echo "<tr class='zile-prezenta-lunar' style='display: none;'><td colspan='3'><table>";

     // Interogare SQL pentru a extrage detaliile zilelor de prezență pentru luna și id_copil specific
    $sql_zile = "SELECT prezenta_data, confirmata_de, confirmata_la FROM prezenta_" . $_SESSION['grupa_clasa_copil_'] . " WHERE id_copil = ? AND YEAR(prezenta_data) = ? AND MONTH(prezenta_data) = ? AND prezenta_stare = 'prezent' ORDER BY prezenta_data DESC";
    $stmt_zile = $conn->prepare($sql_zile);
    $stmt_zile->bind_param("iii", $id_copil, $year, $i); // Corelare cu id_copil și lună
    $stmt_zile->execute();
    $result_zile = $stmt_zile->get_result();

    // Antetul tabelului pentru zilele de prezență lunare
    echo "<tr><th>Data prezentei</th><th>Confirmata de</th><th>Confirmata la</th></tr>";

    // Populează tabelul cu rândurile corespunzătoare
    while ($row_zile = $result_zile->fetch_assoc()) {
        echo "<tr><td>" . $row_zile['prezenta_data'] . "</td><td>" . $row_zile['confirmata_de'] . "</td><td>" . $row_zile['confirmata_la'] . "</td></tr>";
    }

    echo "</table></td></tr>"; // Închidere tabel pentru zilele-prezenta-lunar

}
echo "</table></td></tr>"; //inchidere tabel-lunar

    if ($row2['prezenta_stare'] == 'prezent') {
        $prezenti++;
    } else {
        $absenti++;
    }

}

echo "</table></td></tr>";//inchidere tabel prezenta-copii

if ($status !="parinte") {
    echo "<div class='summary'><div>Prezenti: $prezenti</div><div>Absenti: $absenti</div></div>";
}
echo "</div>";
        }
    } else {
        echo "Nu exista date de prezenta.";
    }
    ?>
</main>

<!--acest cod java script se ocupa de afisarea si ascunderea informatiilor la clic-->
<script>
$(document).ready(function() {
  $('.data-prezenta').click(function() {
    $(this).next('.report-container').find('.tabel-prezenta').toggle();
  });
$('.tabel-prezenta tr:not(:first-child):not(.lunar-report-container)').click(function() {
  var id_copil = $(this).data('id-copil');
  var lunarContainer = $(this).closest('.tabel-prezenta').find('.lunar-report-container[data-id-copil="' + id_copil + '"]');
  lunarContainer.toggle();
});
 $('.tabel-lunar-prezenta tr:not(:first-child):not(.zile-prezenta-lunar)').click(function() {
        var zileContainer = $(this).next('.zile-prezenta-lunar');
        zileContainer.toggle();
    });
var isValidState = true; // Variabilă de stare

// La click pe o celulă editabilă
$('.tabel-lunar-prezenta').on('click', 'td.editabil', function() {
    var originalContent = $(this).text().trim();//elimina spatiile albe
    $(this).data('previousContent', originalContent); // Stochează conținutul anterior
    var placeholderText;

    if ($(this).hasClass('contributie-platita')) {
      placeholderText = 'Valoare Platita + Data';
    } else if ($(this).hasClass('numar-chitanta')) {
      placeholderText = 'Numar Chitanta';
    } else {
      placeholderText = ''; // sau orice alt text de umplutură generic
    }

    // Eliminăm textul de umplutură dacă este prezent
    var valueForInput = (originalContent === placeholderText) ? '' : originalContent;

    $(this).addClass('edit-mode').html('<input type="text" value="' + valueForInput + '"/>');
    $(this).children().first().focus();
  });
// La focusout de pe o celulă editabilă
$('.tabel-lunar-prezenta').on('focusout', 'td.edit-mode', function() {
  var newContent = $(this).children().first().val();

  // Verificarea pentru cifre dacă este celula "contribuția platită"
  if ($(this).hasClass('contributie-platita') && (isNaN(newContent) || !newContent)) {
    alert('Te rog introdu doar cifre în câmpul contribuție!');
    $(this).html($(this).data('previousContent'));
    $(this).children().first().focus();
    return;
  }

   //validarea pentru 'numar-chitanta' dacă este necesar
  if ($(this).hasClass('numar-chitanta') && !newContent) {
    alert('Te rog completează câmpul număr chitanță!');
    $(this).html($(this).data('previousContent'));
    $(this).children().first().focus();
    return;
  }


  // Verificare dacă ambele câmpuri sunt completate
  var siblingCell = $(this).siblings('.editabil');
  var siblingContent = siblingCell.text().trim();  // adaugat trim pentru a elimina spatii

  $(this).removeClass('edit-mode').html(newContent);

    // Obținem id_copil și luna din rândurile și tabelele aferente
  var id_copil = $(this).closest('tr[data-id-copil]').data('id-copil');
  var luna = $(this).closest('tr').data('luna');
  var contributia_platita = $(this).closest('tr').find('.contributie-platita').text().trim();
  var numar_chitanta = $(this).closest('tr').find('.numar-chitanta').text().trim();

  console.log("ID copil: ", id_copil); // Doar pentru a verifica; poți să îl ștergi după
  console.log("Luna: ", luna); // Doar pentru a verifica; poți să îl ștergi după
  console.log("Contribuția plătită: ", contributia_platita);
  console.log("Număr chitanță: ", numar_chitanta);


  $.ajax({
    url: 'inregistreaza_contributia_platita_grupa_clasa_copil.php',
    method: 'POST',
    data: {
      id_copil: id_copil,
      luna: luna,
      contributia_platita: contributia_platita,
      numar_chitanta: numar_chitanta
    },
    success: function(response) {
      if(response === 'success') {
        alert("Contribuția a fost înregistrată cu succes!");
      } else {
        alert("A apărut o eroare: " + response);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      alert("A apărut o eroare: " + textStatus);
    }
  });



 });
});
</script>

<!--acest cod se ocupa de butonul de print din pagina "Tipareste Pagina"-->
<script>
    // Funcția care va fi apelată atunci când se apasă butonul de tipărire
    function printPage() {
        window.print();
    }
</script>

</body>
</html>
