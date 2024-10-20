<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Editor de Meniu Săptămânal</title>
    <link rel="stylesheet" href="style.css">
    <!-- Încărcare pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
    <style>
        /* Stilizări CSS */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Permite celulelor să se ajusteze */
        }
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word; /* Permite textului să treacă pe rândul următor */
        }
        th {
            background-color: #f2f2f2;
        }
        .coloana-ore {
            width: 60px;
        }
        .inputText {
            font-size: 16px;
            line-height: 1.2;
            width: 100%;
            min-height: 20px;
            border: none;
            background-color: transparent;
            resize: none;
            overflow: hidden;
            box-sizing: border-box;
            padding: 2px;
        }
        .suggestions {
            position: absolute;
            background-color: #fff;
            border: 1px solid #ccc;
            z-index: 1000;
            max-height: 150px;
            overflow-y: auto;
            width: 95%;
        }
        .suggestion-item {
            padding: 5px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body id="body_infodisplay">
<header id="header_infodisplay">
    <a href="grupa_clasa_copil.php">
        <div id="schoolName_infodisplay">Unitatea Școlară DEMO</div>
    </a>
    <div id="logo_infodisplay">
        <img src="tid4k.png" alt="TID4K Logo">
        <span id="logoCheckmark" class="logoCheckmark">✔</span>
    </div>
</header>
<div class="separator_infodisplay"></div>

<!-- Added id="meniuTabel" to the table -->
<table id="meniuTabel">
    <!-- Antetul tabelului -->
    <tr>
        <th class="rand-zile">Ora</th>
        <th>Luni</th>
        <th>Marți</th>
        <th>Miercuri</th>
        <th>Joi</th>
        <th>Vineri</th>
    </tr>
    <!-- Rândurile tabelului -->
    <tr>
        <td class="coloana-ore"><textarea class="inputText oraInput">08:15</textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
    </tr>
    <tr>
        <td class="coloana-ore"><textarea class="inputText oraInput">10:00</textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
    </tr>
    <tr>
        <td class="coloana-ore"><textarea class="inputText oraInput">12:00</textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
    </tr>
    <tr>
        <td class="coloana-ore"><textarea class="inputText oraInput">15:15</textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
        <td><textarea class="inputText"></textarea></td>
    </tr>
    <!-- Poți adăuga mai multe rânduri dacă este necesar -->
</table>
<div id="lista-alergeni"></div> <!-- Afișează lista alergenilor -->

<!-- Added the Export to Excel button -->
<button id="exportButton">Exportă în Excel</button>

<!-- Included the SheetJS library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Variabile globale
    let alergeniGlobali = {};
    let feluriDeMancare = {
        // Lista completă de feluri de mâncare
        'Tartină cu unt și mușchi file, ceai de fructe de pădure cu lămâie': {
            emoji: '🍞 Tartină cu unt și mușchi file, ☕ Ceai de fructe de pădure cu lămâie (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Tartină cu unt și cașcaval, lapte cu cacao': {
            emoji: '🍞 Tartină cu unt și cașcaval, 🥛 Lapte cu cacao (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Tartină cu calmette, lapte caramel': {
            emoji: '🍞 Tartină cu brânză Calmette, 🥛 Lapte caramel (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Omletă cu brânză telemea, ceai de fructe de pădure cu lămâie': {
            emoji: '🍳 Omletă cu brânză telemea, ☕ Ceai de fructe de pădure cu lămâie (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Tartină cu unt și ou, ceai de fructe de pădure cu lămâie': {
            emoji: '🍞 Tartină cu unt și ou, ☕ Ceai de fructe de pădure cu lămâie (300ml)',
            alergeni: 'gluten, ouă'
        },
        'Tartină cu unt și șuncă de Praga, ceai de fructe cu lămâie': {
            emoji: '🍞 Tartină cu unt și șuncă de Praga, ☕ Ceai de fructe cu lămâie (300ml)',
            alergeni: 'gluten'
        },
        'Griș cu lapte și dulceață de căpșuni': {
            emoji: '🥣 Griș cu lapte și dulceață de căpșuni (250g)',
            alergeni: 'lactoză, gluten'
        },
        'Lapte cu cereale Nestlé': {
            emoji: '🥛 Lapte cu cereale Nestlé (300ml)',
            alergeni: 'lactoză, gluten'
        },
        'Orez cu lapte și scorțișoară': {
            emoji: '🍚 Orez cu lapte și scorțișoară (250g)',
            alergeni: 'lactoză'
        },
        'Fidea cu lapte, tartine cu unt și gem': {
            emoji: '🍜 Fidea cu lapte, 🍞 Tartine cu unt și gem',
            alergeni: 'gluten, lactoză'
        },
        'Iaurt și crispante': {
            emoji: '🥛 Iaurt și 🥨 Biscuiți crispante',
            alergeni: 'lactoză, gluten'
        },
        'Lapte cu Nesquik, tartine cu unt și gem': {
            emoji: '🥛 Lapte cu Nesquik, 🍞 Tartine cu unt și gem',
            alergeni: 'lactoză, gluten'
        },
        'Ceai cu lămâie, tartine cu unt și cașcaval': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu unt și cașcaval',
            alergeni: 'gluten, lactoză'
        },
        'Melcișori cu lapte': {
            emoji: '🥣 Melcișori cu lapte (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Tăieței cu lapte': {
            emoji: '🥣 Tăieței cu lapte (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Budincă de orez': {
            emoji: '🍮 Budincă de orez (250g)',
            alergeni: 'lactoză'
        },
        'Omletă cu brânză la cuptor, pâine și castravete': {
            emoji: '🍳 Omletă cu brânză la cuptor, 🍞 Pâine și 🥒 Castravete',
            alergeni: 'ouă, lactoză, gluten'
        },
        'Tartină cu unt și gem, ceai de fructe cu lămâie': {
            emoji: '🍞 Tartină cu unt și gem, ☕ Ceai de fructe cu lămâie (300ml)',
            alergeni: 'gluten'
        },
        'Tartină cu unt și ou, lapte': {
            emoji: '🍞 Tartină cu unt și ou, 🥛 Lapte (300ml)',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Tartină cu brânză Philadelphia, ceai de fructe cu lămâie': {
            emoji: '🍞 Tartină cu brânză Philadelphia, ☕ Ceai de fructe cu lămâie (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Cereale cu lapte': {
            emoji: '🥣 Cereale cu lapte (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Griș cu lapte': {
            emoji: '🥣 Griș cu lapte (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Omletă cu brânză și mușchi file, ceai de fructe cu lămâie': {
            emoji: '🍳 Omletă cu brânză și mușchi file, ☕ Ceai de fructe cu lămâie (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Tartină cu unt și cașcaval, lapte': {
            emoji: '🍞 Tartină cu unt și cașcaval, 🥛 Lapte (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Lapte cu cacao, tartină cu brânză pufoasă': {
            emoji: '🥛 Lapte cu cacao, 🍞 Tartină cu brânză pufoasă',
            alergeni: 'gluten, lactoză'
        },
        'Tartină cu unt și miere, lapte': {
            emoji: '🍞 Tartină cu unt și miere, 🥛 Lapte (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, tartină cu unt și brânză telemea': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartină cu unt și brânză telemea',
            alergeni: 'gluten, lactoză'
        },
        'Lapte cu Nesquik, tartină cu unt și șuncă de curcan': {
            emoji: '🥛 Lapte cu Nesquik, 🍞 Tartină cu unt și șuncă de curcan',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, tartină cu unt și cașcaval, măsline și ardei capia': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartină cu unt și cașcaval, 🫒 Măsline și 🌶️ Ardei capia',
            alergeni: 'gluten, lactoză'
        },
        'Orez cu lapte și dulceață': {
            emoji: '🍚 Orez cu lapte și dulceață (250g)',
            alergeni: 'lactoză'
        },
        'Lapte cu Nesquik, tartină cu unt și gem': {
            emoji: '🥛 Lapte cu Nesquik, 🍞 Tartină cu unt și gem',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, omletă cu brânză': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍳 Omletă cu brânză',
            alergeni: 'ouă, lactoză'
        },
        'Melcișori cu lapte, tartine cu pastă de ficăței': {
            emoji: '🥣 Melcișori cu lapte, 🍞 Tartine cu pastă de ficăței',
            alergeni: 'gluten, lactoză'
        },
        'Fidea cu lapte, tartine cu unt și gem': {
            emoji: '🍜 Fidea cu lapte, 🍞 Tartine cu unt și gem',
            alergeni: 'gluten, lactoză'
        },
        'Omletă cu brânză la cuptor, pâine și ardei gras': {
            emoji: '🍳 Omletă cu brânză la cuptor, 🍞 Pâine și 🌶️ Ardei gras',
            alergeni: 'ouă, lactoză, gluten'
        },
        'Ceai de fructe cu lămâie, tartine cu unt și mușchi file': {
            emoji: '☕ Ceai de fructe cu lămâie (300ml), 🍞 Tartine cu unt și mușchi file',
            alergeni: 'gluten'
        },
        'Lapte cu cacao, tartine cu unt și cașcaval': {
            emoji: '🥛 Lapte cu cacao, 🍞 Tartine cu unt și cașcaval',
            alergeni: 'gluten, lactoză'
        },
        'Budincă de orez cu scorțișoară': {
            emoji: '🍮 Budincă de orez cu scorțișoară (250g)',
            alergeni: 'lactoză'
        },
        'Ceai cu lămâie, tartine cu pastă de brânză de vaci și ardei gras': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu pastă de brânză de vaci și 🌶️ Ardei gras',
            alergeni: 'gluten, lactoză'
        },
        'Lapte cu Nesquik, tartină cu unt și șuncă de curcan': {
            emoji: '🥛 Lapte cu Nesquik, 🍞 Tartină cu unt și șuncă de curcan',
            alergeni: 'gluten, lactoză'
        },
        'Griș cu lapte și scorțișoară': {
            emoji: '🥣 Griș cu lapte și scorțișoară (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Iaurt cu fulgi de porumb': {
            emoji: '🥛 Iaurt cu fulgi de porumb',
            alergeni: 'lactoză, gluten'
        },
        'Ceai cu lămâie, tartine cu unt și ou fiert, ardei gras': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu unt și ou fiert, 🌶️ Ardei gras',
            alergeni: 'gluten, ouă'
        },
        'Omletă cu brânză, la cuptor, pâine și castravete': {
            emoji: '🍳 Omletă cu brânză la cuptor, 🍞 Pâine și 🥒 Castravete',
            alergeni: 'ouă, lactoză, gluten'
        },
        'Ceai cu lămâie, tartine cu pastă de ficat': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu pastă de ficat',
            alergeni: 'gluten'
        },
        'Lapte cu cereale Nestlé duo': {
            emoji: '🥛 Lapte cu cereale Nestlé duo (300ml)',
            alergeni: 'lactoză, gluten'
        },
        'Ceai din plante cu miere, omletă cu cașcaval la cuptor': {
            emoji: '☕ Ceai din plante cu miere (300ml), 🍳 Omletă cu cașcaval la cuptor',
            alergeni: 'ouă, lactoză'
        },
        'Iaurt și fursecuri cu fructe confiate': {
            emoji: '🥛 Iaurt, 🍪 Fursecuri cu fructe confiate',
            alergeni: 'lactoză, gluten'
        },
        'Ceai de fructe de pădure cu lămâie, tartină cu brânză de vaci și unt': {
            emoji: '☕ Ceai de fructe de pădure cu lămâie (300ml), 🍞 Tartină cu brânză de vaci și unt',
            alergeni: 'gluten, lactoză'
        },
        'Cacao cu lapte, biscuiți multicereale': {
            emoji: '🥛 Cacao cu lapte, 🍪 Biscuiți multicereale',
            alergeni: 'lactoză, gluten'
        },
        'Lapte cu cereale Nestlé, tartine cu unt și gem': {
            emoji: '🥛 Lapte cu cereale Nestlé, 🍞 Tartine cu unt și gem',
            alergeni: 'lactoză, gluten'
        },
        'Ceai cu lămâie, tartină cu brânză pufoasă': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartină cu brânză pufoasă',
            alergeni: 'gluten, lactoză'
        },
        'Ceai de fructe cu lămâie, tartine cu pastă de ou și castravete': {
            emoji: '☕ Ceai de fructe cu lămâie (300ml), 🍞 Tartine cu pastă de ou și 🥒 Castravete',
            alergeni: 'gluten, ouă'
        },
        'Ceai de plante cu miere, tartine cu unt și cașcaval': {
            emoji: '☕ Ceai de plante cu miere (300ml), 🍞 Tartine cu unt și cașcaval',
            alergeni: 'gluten, lactoză'
        },
        'Lapte cu Nesquik, tartine cu unt și gem': {
            emoji: '🥛 Lapte cu Nesquik, 🍞 Tartine cu unt și gem',
            alergeni: 'gluten, lactoză'
        },
        'Ceai de plante cu lămâie, tartine cu pastă de brânză de vaci': {
            emoji: '☕ Ceai de plante cu lămâie (300ml), 🍞 Tartine cu pastă de brânză de vaci',
            alergeni: 'gluten, lactoză'
        },
        'Omletă cu brânză telemea, ceai cu lămâie': {
            emoji: '🍳 Omletă cu brânză telemea, ☕ Ceai cu lămâie (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Lapte, tartine cu unt și miere': {
            emoji: '🥛 Lapte (300ml), 🍞 Tartine cu unt și miere',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, tartine cu unt, brânză telemea și castravete': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu unt, brânză telemea și 🥒 Castravete',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, tartine cu pastă de ficăței de pui': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu pastă de ficăței de pui',
            alergeni: 'gluten'
        },
        'Lapte cu cacao, tartine cu pastă de ou': {
            emoji: '🥛 Lapte cu cacao, 🍞 Tartine cu pastă de ou',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Ceai cu lămâie, omletă cu brânză la cuptor și ardei gras': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍳 Omletă cu brânză la cuptor și 🌶️ Ardei gras',
            alergeni: 'ouă, lactoză'
        },
        'Lapte cu cereale Nestlé, tartine cu unt și gem': {
            emoji: '🥛 Lapte cu cereale Nestlé, 🍞 Tartine cu unt și gem',
            alergeni: 'gluten, lactoză'
        },
        'Ceai, tartine cu pastă de măsline': {
            emoji: '☕ Ceai (300ml), 🍞 Tartine cu pastă de măsline',
            alergeni: 'gluten'
        },
        'Ceai, tartine cu pastă de brânză de vaci și mărar': {
            emoji: '☕ Ceai (300ml), 🍞 Tartine cu pastă de brânză de vaci și 🌿 Mărar',
            alergeni: 'gluten, lactoză'
        },
        'Fidea cu lapte, tartine cu pastă de ficăței': {
            emoji: '🍜 Fidea cu lapte, 🍞 Tartine cu pastă de ficăței',
            alergeni: 'gluten, lactoză'
        },
        'Lapte cu cacao, tartine cu pastă de ficat și ardei': {
            emoji: '🥛 Lapte cu cacao, 🍞 Tartine cu pastă de ficat și 🌶️ Ardei',
            alergeni: 'gluten, lactoză'
        },
        'Ceai cu lămâie, tartine cu unt și brânză telemea': {
            emoji: '☕ Ceai cu lămâie (300ml), 🍞 Tartine cu unt și brânză telemea',
            alergeni: 'gluten, lactoză'
        },
        'Budincă de orez': {
            emoji: '🍮 Budincă de orez (250g)',
            alergeni: 'lactoză'
        },'Pâine prăjită cu unt, ceai de fructe': {
            emoji: '🍞 Pâine prăjită cu unt, ☕ Ceai de fructe (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Orez cu lapte și vanilie': {
            emoji: '🍚 Orez cu lapte și vanilie (250g)',
            alergeni: 'lactoză'
        },
        'Omletă cu șuncă și brânză, ceai de mentă': {
            emoji: '🍳 Omletă cu șuncă și brânză, ☕ Ceai de mentă (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Iaurt cu fructe și musli': {
            emoji: '🥛 Iaurt cu fructe și 🥣 Musli',
            alergeni: 'lactoză, gluten'
        },
        'Tartină cu brânză și roșii': {
            emoji: '🍞 Tartină cu brânză și 🍅 Roșii',
            alergeni: 'gluten, lactoză'
        },
        'Salată de legume cu brânză feta, pâine prăjită': {
            emoji: '🥗 Salată de legume cu brânză feta, 🍞 Pâine prăjită',
            alergeni: 'gluten, lactoză'
        },
        'Pâine cu unt de arahide și gem, lapte': {
            emoji: '🍞 Pâine cu unt de arahide și gem, 🥛 Lapte (300ml)',
            alergeni: 'gluten, arahide, lactoză'
        },
        'Omletă cu ciuperci și cașcaval, ceai verde': {
            emoji: '🍳 Omletă cu ciuperci și cașcaval, ☕ Ceai verde (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Fulgi de ovăz cu fructe și iaurt': {
            emoji: '🥣 Fulgi de ovăz cu fructe și 🥛 Iaurt',
            alergeni: 'gluten, lactoză'
        },
        'Tartină cu cremă de brânză și castraveți': {
            emoji: '🍞 Tartină cu cremă de brânză și 🥒 Castraveți',
            alergeni: 'gluten, lactoză'
        },
        'Budincă de vanilie cu biscuiți': {
            emoji: '🍮 Budincă de vanilie cu 🍪 Biscuiți',
            alergeni: 'lactoză, gluten'
        },
        'Clătite cu dulceață de afine': {
            emoji: '🥞 Clătite cu dulceață de afine',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Gustare de morcovi și hummus': {
            emoji: '🥕 Morcovi cu hummus',
            alergeni: 'n/a'
        },
        'Salată de fructe cu iaurt': {
            emoji: '🍇 Salată de fructe cu 🥛 Iaurt',
            alergeni: 'lactoză'
        },
        'Batoane de ovăz cu ciocolată și lapte': {
            emoji: '🍫 Batoane de ovăz cu ciocolată, 🥛 Lapte (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Omletă cu spanac și brânză, ceai de fructe': {
            emoji: '🍳 Omletă cu spanac și brânză, ☕ Ceai de fructe (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Macaroane cu brânză': {
            emoji: '🍝 Macaroane cu brânză (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Sandwich cu șuncă și cașcaval': {
            emoji: '🥪 Sandwich cu șuncă și cașcaval',
            alergeni: 'gluten, lactoză'
        },
        'Iaurt cu cereale integrale și miere': {
            emoji: '🥛 Iaurt cu cereale integrale și 🍯 Miere',
            alergeni: 'gluten, lactoză'
        },
        'Pui cu orez și legume la cuptor': {
            emoji: '🍗 Pui cu orez și 🥕 Legume la cuptor',
            alergeni: 'n/a'
        },
        'Fursecuri cu fulgi de ciocolată, ceai de plante': {
            emoji: '🍪 Fursecuri cu fulgi de ciocolată, ☕ Ceai de plante (300ml)',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Sandwich cu brânză și șuncă de pui': {
            emoji: '🥪 Sandwich cu brânză și șuncă de pui',
            alergeni: 'gluten, lactoză'
        },
        'Salată de quinoa cu avocado și roșii': {
            emoji: '🥗 Salată de quinoa cu 🥑 Avocado și 🍅 Roșii',
            alergeni: 'n/a'
        },
        'Budincă de ciocolată și banane': {
            emoji: '🍮 Budincă de ciocolată și 🍌 Banane',
            alergeni: 'lactoză'
        },
        'Iaurt cu granola și fructe de pădure': {
            emoji: '🥛 Iaurt cu granola și 🍓 Fructe de pădure',
            alergeni: 'lactoză, gluten'
        },
        'Pâine prăjită cu avocado și ou fiert': {
            emoji: '🍞 Pâine prăjită cu 🥑 Avocado și 🥚 Ou fiert',
            alergeni: 'gluten, ouă'
        },
        'Smoothie de fructe cu iaurt': {
            emoji: '🍹 Smoothie de fructe cu 🥛 Iaurt',
            alergeni: 'lactoză'
        },
        'Griș cu lapte și scorțișoară': {
            emoji: '🥣 Griș cu lapte și scorțișoară (250g)',
            alergeni: 'gluten, lactoză'
        },
        'Pâine prăjită cu brânză de vaci și mărar': {
            emoji: '🍞 Pâine prăjită cu brânză de vaci și 🌿 Mărar',
            alergeni: 'gluten, lactoză'
        },
        'Fulgi de porumb cu lapte și banane': {
            emoji: '🥣 Fulgi de porumb cu 🥛 Lapte și 🍌 Banane',
            alergeni: 'gluten, lactoză'
        },
        'Biscuiți cu fulgi de ovăz și lapte': {
            emoji: '🍪 Biscuiți cu fulgi de ovăz, 🥛 Lapte (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Tartine cu hummus și morcovi': {
            emoji: '🍞 Tartine cu hummus și 🥕 Morcovi',
            alergeni: 'gluten'
        },
        'Omletă cu roșii și brânză feta, ceai de mentă': {
            emoji: '🍳 Omletă cu roșii și brânză feta, ☕ Ceai de mentă (300ml)',
            alergeni: 'ouă, lactoză'
        },
        'Pâine cu unt și miere, ceai verde': {
            emoji: '🍞 Pâine cu unt și 🍯 Miere, ☕ Ceai verde (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Salată de ton cu porumb și pâine prăjită': {
            emoji: '🥗 Salată de ton cu 🌽 Porumb și 🍞 Pâine prăjită',
            alergeni: 'gluten, pește'
        },
        'Clătite cu cremă de ciocolată': {
            emoji: '🥞 Clătite cu cremă de ciocolată',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Budincă de orez cu fructe de pădure': {
            emoji: '🍮 Budincă de orez cu 🍓 Fructe de pădure',
            alergeni: 'lactoză'
        },
        'Iaurt cu miere și fulgi de migdale': {
            emoji: '🥛 Iaurt cu 🍯 Miere și 🌰 Fulgi de migdale',
            alergeni: 'lactoză, migdale'
        },
        'Pâine cu brânză topită și șuncă, ceai de mușețel': {
            emoji: '🍞 Pâine cu brânză topită și șuncă, ☕ Ceai de mușețel (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Smoothie de fructe cu fulgi de ovăz și iaurt': {
            emoji: '🍹 Smoothie de fructe cu fulgi de ovăz și 🥛 Iaurt',
            alergeni: 'gluten, lactoză'
        },
        'Pâine cu unt de arahide și gem, ceai de plante': {
            emoji: '🍞 Pâine cu unt de arahide și gem, ☕ Ceai de plante (300ml)',
            alergeni: 'gluten, arahide'
        },
        'Macaroane cu sos de roșii și parmezan': {
            emoji: '🍝 Macaroane cu sos de roșii și 🧀 Parmezan',
            alergeni: 'gluten, lactoză'
        },
        'Clătite cu ricotta și fructe': {
            emoji: '🥞 Clătite cu ricotta și 🍓 Fructe',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Budincă de ciocolată și fructe de pădure': {
            emoji: '🍮 Budincă de ciocolată și 🍓 Fructe de pădure',
            alergeni: 'lactoză'
        },
        'Porridge de ovăz cu lapte și nuci': {
            emoji: '🥣 Porridge de ovăz cu 🥛 Lapte și 🌰 Nuci',
            alergeni: 'gluten, lactoză, nuci'
        },
        'Iaurt cu granola și sirop de arțar': {
            emoji: '🥛 Iaurt cu granola și 🍁 Sirop de arțar',
            alergeni: 'lactoză, gluten'
        },
        'Orez cu pui și legume': {
            emoji: '🍚 Orez cu pui și 🥕 Legume',
            alergeni: 'n/a'
        },
        'Sandwich cu somon afumat și brânză': {
            emoji: '🥪 Sandwich cu somon afumat și 🧀 Brânză',
            alergeni: 'gluten, pește, lactoză'
        },
        'Fulgi de ovăz cu iaurt și miere': {
            emoji: '🥣 Fulgi de ovăz cu 🥛 Iaurt și 🍯 Miere',
            alergeni: 'gluten, lactoză'
        },
        'Clătite americane cu sirop de arțar': {
            emoji: '🥞 Clătite americane cu 🍁 Sirop de arțar',
            alergeni: 'gluten, ouă'
        },
        'Budincă de vanilie cu fructe': {
            emoji: '🍮 Budincă de vanilie cu 🍓 Fructe',
            alergeni: 'lactoză'
        },
        'Smoothie de banane și lapte': {
            emoji: '🍌 Smoothie de banane și 🥛 Lapte',
            alergeni: 'lactoză'
        },
        'Salată de quinoa cu avocado și pui': {
            emoji: '🥗 Salată de quinoa cu 🥑 Avocado și 🍗 Pui',
            alergeni: 'n/a'
        },
        'Pâine cu hummus și castraveți': {
            emoji: '🍞 Pâine cu hummus și 🥒 Castraveți',
            alergeni: 'gluten'
        },
        'Iaurt cu fulgi de ovăz și căpșuni': {
            emoji: '🥛 Iaurt cu fulgi de ovăz și 🍓 Căpșuni',
            alergeni: 'gluten, lactoză'
        },
        'Orez cu legume și sos de soia': {
            emoji: '🍚 Orez cu legume și 🌿 Sos de soia',
            alergeni: 'n/a'
        },
        'Clătite cu cremă de vanilie și fructe de pădure': {
            emoji: '🥞 Clătite cu cremă de vanilie și 🍓 Fructe de pădure',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Smoothie de mango și lapte de cocos': {
            emoji: '🥭 Smoothie de mango și 🥥 Lapte de cocos',
            alergeni: 'n/a'
        },
        'Pâine prăjită cu unt și dulceață, ceai de plante': {
            emoji: '🍞 Pâine prăjită cu unt și dulceață, ☕ Ceai de plante (300ml)',
            alergeni: 'gluten, lactoză'
        },
        'Budincă de ciocolată și nuci': {
            emoji: '🍮 Budincă de ciocolată și 🌰 Nuci',
            alergeni: 'lactoză, nuci'
        },
        'Orez cu lapte și scorțișoară, ceai de mentă': {
            emoji: '🍚 Orez cu lapte și scorțișoară, ☕ Ceai de mentă (300ml)',
            alergeni: 'lactoză'
        },
        'Iaurt cu miere și nuci': {
            emoji: '🥛 Iaurt cu 🍯 Miere și 🌰 Nuci',
            alergeni: 'lactoză, nuci'
        },
        'Salată de avocado și ou fiert': {
            emoji: '🥗 Salată de 🥑 Avocado și 🥚 Ou fiert',
            alergeni: 'ouă'
        },
        'Clătite cu mere caramelizate': {
            emoji: '🥞 Clătite cu 🍎 Mere caramelizate',
            alergeni: 'gluten, ouă'
        },
        'Budincă de vanilie cu biscuiți și nuci': {
            emoji: '🍮 Budincă de vanilie cu 🍪 Biscuiți și 🌰 Nuci',
            alergeni: 'lactoză, gluten, nuci'
        },
        'Pâine cu hummus și roșii': {
            emoji: '🍞 Pâine cu hummus și 🍅 Roșii',
            alergeni: 'gluten'
        },
        'Iaurt cu miere și fulgi de ciocolată': {
            emoji: '🥛 Iaurt cu 🍯 Miere și 🍫 Fulgi de ciocolată',
            alergeni: 'lactoză'
        },
        'Salată de ton cu ou fiert și pâine prăjită': {
            emoji: '🥗 Salată de ton cu 🥚 Ou fiert și 🍞 Pâine prăjită',
            alergeni: 'pește, ouă, gluten'
        },
        'Clătite cu cremă de brânză și căpșuni': {
            emoji: '🥞 Clătite cu cremă de brânză și 🍓 Căpșuni',
            alergeni: 'gluten, ouă, lactoză'
        },
        'Smoothie de banane și fructe de pădure': {
            emoji: '🍌 Smoothie de banane și 🍓 Fructe de pădure',
            alergeni: 'n/a'
        },
        'Pâine cu unt și dulceață de fructe de pădure': {
            emoji: '🍞 Pâine cu unt și dulceață de 🍓 Fructe de pădure',
            alergeni: 'gluten, lactoză'
        },
        'Orez cu lapte și ciocolată, ceai de mentă': {
            emoji: '🍚 Orez cu lapte și ciocolată, ☕ Ceai de mentă (300ml)',
            alergeni: 'lactoză'
        },
        'Biscuiți cu ovăz și stafide, lapte de migdale': {
            emoji: '🍪 Biscuiți cu ovăz și stafide, 🥥 Lapte de migdale',
            alergeni: 'gluten, migdale'
        },
        'Porridge cu lapte de migdale și miere': {
            emoji: '🥣 Porridge cu 🥥 Lapte de migdale și 🍯 Miere',
            alergeni: 'n/a'
        },


        // Adaugă alte feluri de mâncare aici
    };

    // Funcții JavaScript
    function initializeAutocomplete() {
        const textAreas = document.querySelectorAll('textarea.inputText');
        textAreas.forEach((textarea, index) => {
            textarea.addEventListener('input', function () {
                showSuggestions(this, index);
                adjustHeight(this);
            });

            // Salvarea și încărcarea datelor
            const savedValue = localStorage.getItem('input_' + index);
            if (savedValue !== null) {
                textarea.value = savedValue;
                adjustHeight(textarea);
            }
            textarea.addEventListener('input', function () {
                localStorage.setItem('input_' + index, this.value);
            });
        });
    }

    function adjustHeight(textArea) {
        textArea.style.height = 'auto'; // Reset height to calculate new height
        textArea.style.height = textArea.scrollHeight + 'px'; // Set new height based on content
    }

    // Apply the adjustment to all textareas on page load and on input
    document.querySelectorAll('textarea.inputText').forEach(function (textarea) {
        adjustHeight(textarea); // Adjust initial height
        textarea.addEventListener('input', function () {
            adjustHeight(textarea); // Adjust height on content change
        });
    });

    function normalizeString(str) {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    function showSuggestions(textarea, index) {
        const parent = textarea.parentElement;
        let suggestionBox = parent.querySelector('.suggestions');
        if (!suggestionBox) {
            suggestionBox = document.createElement('div');
            suggestionBox.classList.add('suggestions');
            parent.appendChild(suggestionBox);
        }
        const inputValue = textarea.value;
        const inputValueNormalized = normalizeString(inputValue);
        suggestionBox.innerHTML = '';
        if (inputValue.length > 1) {
            const suggestions = Object.keys(feluriDeMancare).filter(item => {
                return normalizeString(item).includes(inputValueNormalized);
            });
            suggestions.forEach(suggestion => {
                const itemDiv = document.createElement('div');
                itemDiv.classList.add('suggestion-item');
                itemDiv.textContent = suggestion;
                itemDiv.addEventListener('click', function () {
                    textarea.value = feluriDeMancare[suggestion].emoji;
                    suggestionBox.innerHTML = '';

                    // Salvează valoarea completă în localStorage după selectare
                    localStorage.setItem('input_' + index, textarea.value);

                    // actualizeazaAlergeni(); // Uncomment if you have this function defined
                    adjustHeight(textarea);
                });
                suggestionBox.appendChild(itemDiv);
            });
        } else {
            suggestionBox.innerHTML = '';
        }
    }

    function afiseazaAlergeni() {
        let listaAlergeni = document.getElementById('lista-alergeni');
        let alergeniText = '<strong>Alergeni:</strong> ' + Object.keys(alergeniGlobali).join(', ');
        listaAlergeni.innerHTML = alergeniText;
    }

    // Funcția pentru actualizarea orelor și evidențierea zilei curente
    document.addEventListener('DOMContentLoaded', function () {
        function adaugaZile(data, zile) {
            var rezultat = new Date(data);
            rezultat.setDate(rezultat.getDate() + zile);
            return rezultat;
        }

        function obtinePrimaZiASaptamanii(data, esteWeekend) {
            var zi = data.getDay();
            var diferenta = zi === 0 ? -6 : 1;
            var primaZi = new Date(data);
            primaZi.setDate(data.getDate() - zi + diferenta);

            if (esteWeekend) {
                primaZi.setDate(primaZi.getDate() + 7);
            }

            return primaZi;
        }

        var dataCurenta = new Date();
        var esteWeekend = dataCurenta.getDay() === 0 || dataCurenta.getDay() === 6;

        var primaZi = obtinePrimaZiASaptamanii(dataCurenta, esteWeekend);

        for (var i = 1; i <= 5; i++) {
            var dataZilei = adaugaZile(primaZi, i - 1);
            var dataFormatata = dataZilei.toLocaleDateString('ro-RO', { day: '2-digit', month: 'long' });
            var ziuaSaptamanii = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'][i - 1];

            var th = document.querySelector(`table tr th:nth-child(${i + 1})`);
            th.textContent = `${ziuaSaptamanii} (${dataFormatata})`;

            // Evidențierea zilei curente
            if (!esteWeekend && i === dataCurenta.getDay()) {
                var celule = document.querySelectorAll(`table tr td:nth-child(${i + 1}), table tr th:nth-child(${i + 1})`);
                celule.forEach(function (celula) {
                    celula.style.backgroundColor = '#DBDBDB';
                    celula.style.borderRadius = '0';
                });
            }
        }

        initializeAutocomplete();
        // actualizeazaAlergeni(); // Uncomment if you have this function defined

        // Ajustează înălțimea pentru toate textarea-urile la încărcare
        document.querySelectorAll('textarea.inputText').forEach(function (textarea) {
            adjustHeight(textarea);
        });
    });

    // Salvarea meniului la clic pe logo
    document.getElementById('logo_infodisplay').addEventListener('click', function () {
        // Afișează bifa verde
        const logoCheckmark = document.getElementById('logoCheckmark');
        logoCheckmark.style.display = 'inline';

        // Extrage tabelul și lista de alergeni
        const tabel = document.querySelector("table");
        const listaAlergeni = document.getElementById("lista-alergeni");

        // Actualizează conținutul fiecărui textarea cu valoarea curentă introdusă de utilizator
        tabel.querySelectorAll("textarea").forEach(textarea => {
            textarea.textContent = textarea.value;
        });

        // Construiește HTML-ul complet pentru a fi salvat
        const tabelHTML = tabel.outerHTML;
        const listaAlergeniHTML = listaAlergeni.outerHTML;
        const fullHTML = tabelHTML + listaAlergeniHTML;

        // Trimite HTML-ul pentru salvare
        fetch('salveaza_meniuHTML.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'html=' + encodeURIComponent(fullHTML)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('HTML-ul meniului a fost salvat cu succes.');
                    // Redirecționează către pagina avizierului
                    window.location.href = '../avizier/tid4k.html';
                } else {
                    console.error('Eroare la salvarea HTML-ului meniului:', data.error);
                }
            })
            .catch(error => {
                console.error('Eroare la trimiterea HTML-ului meniului:', error);
            });
    });

    // Function to handle export to Excel
    document.getElementById('exportButton').addEventListener('click', function() {
        // Get the table element
        var table = document.getElementById('meniuTabel');

        // Create a clone of the table to modify for exporting
        var tableClone = table.cloneNode(true);

        // Replace textarea elements with their text content
        var textareas = tableClone.getElementsByTagName('textarea');
        for (var i = textareas.length - 1; i >= 0; i--) {
            var textarea = textareas[i];
            var td = textarea.parentNode;
            var value = textarea.value;
            td.removeChild(textarea);
            td.textContent = value;
        }

        // Convert the modified table to a workbook
        var wb = XLSX.utils.table_to_book(tableClone, {sheet: "Meniu"});

        // Export the workbook to an Excel file
        XLSX.writeFile(wb, 'meniu_export.xlsx');
    });
</script>
</body>
</html>
