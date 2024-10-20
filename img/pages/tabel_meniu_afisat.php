<style>
table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid black;
    padding: 5px;
    text-align: left;
  }
  th {
    background-color: #f2f2f2;
  }
  input[type="text"] {
    width: 100%;
    box-sizing: border-box;
    border: none;
    background-color: transparent;
  }
   .coloana-ore {
        width: 60px; /* Sau orice altă valoare care ți se pare potrivită */
    }

.inputText {
    font-size: 16px;
    width: 100%; /* Se ajustează la lățimea celulei */
    min-height: 20px; /* Înălțimea minimă inițială, ajustează după nevoie */
    border: none; /* Fără bordură */
    background-color: transparent; /* Fundal transparent */
    resize: none; /* Împiedică redimensionarea */
    overflow: hidden; /* Ascunde scrollbar-ul */
}


</style>

<table>
  <!-- Antetul tabelului -->
  <tr>
    <th class="rand-zile">Ora</th>
    <th>Luni </th>
    <th>Marți </th>
    <th>Miercuri </th>
    <th>Joi </th>
    <th>Vineri </th>
  </tr>

  <!-- Rândul pentru ora 08:15 -->
   <tr>
    <td class="coloana-ore"><textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">08:15</textarea></td>
    <td><textarea class="inputText">🥣 Cereale cu lapte (300ml)</textarea></td>
    <td><textarea class="inputText">🍞 Tartina cu unt și mușchi file</textarea></td>
    <td><textarea class="inputText">🥛 Ceai de fructe cu lămâie (300ml)</textarea></td>
    <td><textarea class="inputText">🧀 Omletă cu brânză</textarea></td>
    <td><textarea class="inputText">🥪 Tartina cu unt și cașcaval</textarea></td>
  </tr>

  <!-- Rândul pentru ora 10:00 -->
 <tr>
    <td class="coloana-ore"><textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">10:00</textarea></td>
    <td><textarea class="inputText">🍎 Fructe (1 buc)</textarea></td>
    <td><textarea class="inputText">🍏 Fructe (1 buc)</textarea></td>
    <td><textarea class="inputText">🍊 Fructe (1 buc)</textarea></td>
    <td><textarea class="inputText">🍓 Fructe (1 buc)</textarea></td>
    <td><textarea class="inputText">🍇 Fructe (1 buc)</textarea></td>
</tr>


  <!-- Rândul pentru ora 12:00 -->
 <tr>
    <td class="coloana-ore"><textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">12:00</textarea></td>
    <td><textarea class="inputText">🍲 Ciorbă a la Grec (300ml)</textarea></td>
    <td><textarea class="inputText">🍜 Supă de legume cu fidea (300ml)</textarea></td>
    <td><textarea class="inputText">🍵 Ciorbă rădăuțeană cu crutoane (300ml)</textarea></td>
    <td><textarea class="inputText">🍝 Supă cremă cu crutoane (300ml)</textarea></td>
    <td><textarea class="inputText">🍜 Ciorbă țărănească (300ml)</textarea></td>
</tr>

  <!-- Rândul pentru ora 15:15 -->
 <tr>
    <td class="coloana-ore"><textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">15:15</textarea></td>
    <td><textarea class="inputText">🍪 Biscuiți cu iaurt de băut</textarea></td>
    <td><textarea class="inputText">🍎 Fruct</textarea></td>
    <td><textarea class="inputText">🍪 Biscuiți cu iaurt de băut</textarea></td>
    <td><textarea class="inputText">🍰 Prăjitură Kinder</textarea></td>
    <td><textarea class="inputText">🍪 Biscuiți cu iaurt de băut</textarea></td>
</tr>



  <!-- Rândul pentru ora 19:00 -->
<tr>
    <td class="coloana-ore"><textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">19:00</textarea></td>
    <td><textarea class="inputText">🍞 Tartina cu Almette</textarea></td>
    <td><textarea class="inputText">🍝 Paste cu brânză</textarea></td>
    <td><textarea class="inputText">🍝 Paste cu brânză</textarea></td>
    <td><textarea class="inputText">🍞 Tartina cu Almette</textarea></td>
    <td><textarea class="inputText">🍝 Paste cu brânză</textarea></td>
</tr>


</table>
<div id="lista-alergeni"></div> <!--afiseaza sub tabel (se coreleaza cu functia actualizeazaAlergeni ) lista alergenilor asociati meniului-->

<script>
// Definește seturile de ore
var setOre1 = ["08:15", "10:00", "12:00", "15:15", "19:00"];
var setOre2 = ["09:00", "10:30", "12:30", "16:00"];

function actualizeazaOre(oraSelectata) {
    var setSelectat;

    // Determină setul de ore pe baza primei ore selectate
    if (["08:15", "10:00", "12:00", "15:15", "19:00"].includes(oraSelectata)) {
        setSelectat = setOre1;
    } else if (["09:00", "10:30", "12:30", "16:00"].includes(oraSelectata)) {
        setSelectat = setOre2;
    } else {
        // Dacă ora introdusă nu se potrivește cu niciun set, nu face nimic
        return;
    }

    // Obține toate câmpurile de input pentru ore
    var campuriOra = document.querySelectorAll('textarea.oraInput');
    var meniuInputuri = document.querySelectorAll('table tr td:nth-child(n+2) textarea.inputText'); // Actualizat pentru a selecta textarea

    // Actualizează câmpurile de input pentru ore cu valorile din setul selectat
    setSelectat.forEach(function(ora, index) {
        if (campuriOra[index] !== undefined) {
            campuriOra[index].value = ora; // Accesează proprietatea value pentru textarea
        }
    });

    // Ajustează numărul de rânduri din tabel dacă este necesar
    var tabel = document.querySelector('table');
    while (tabel.rows.length - 1 > setSelectat.length) {
        tabel.deleteRow(-1); // Șterge ultimul rând
    }

 var indexMeniu = 0;
while (tabel.rows.length - 1 < setSelectat.length) {
    var randNou = tabel.insertRow(-1);
    for (var i = 0; i < 6; i++) {
        var celulaNoua = randNou.insertCell(i);
        if (i === 0) {
            // Asigură-te că setezi corect ora pentru fiecare nou rând adăugat
            celulaNoua.innerHTML = `<textarea class="inputText oraInput" oninput="actualizeazaOre(this.value)">${setSelectat[tabel.rows.length - 2]}</textarea>`; // '-2' pentru că 'tabel.rows.length' include și antetul tabelului
        } else {
            // Repopulează valorile pentru meniuri dacă sunt disponibile
            var valoareMeniu = meniuInputuri.length > indexMeniu ? meniuInputuri[indexMeniu].value : '';
            celulaNoua.innerHTML = `<textarea class="inputText">${valoareMeniu}</textarea>`;
            indexMeniu++;
        }
    }
}
}

//identificarea zilelor saptamanii cu valorile din calendar si evidentierea zilei curent cu gri-inchis
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

        // Noua logica pentru evidențierea coloanei curente
        if (!esteWeekend && i === dataCurenta.getDay()) {
            var celule = document.querySelectorAll(`table tr td:nth-child(${i + 1}), table tr th:nth-child(${i + 1})`);
            celule.forEach(function(celula) {
                celula.style.backgroundColor = '#D3D3D3'; // Gri-deschis pentru evidențiere
            });
        }
    }
});


let alergeniGlobali = {}; // Variabilă globală pentru a stoca alergenii și cantitățile lor
function adaugaEmoji() { //functia care adauga emoji cuvintelor cheie (feluri mancare) si calculeaza alergenii asociati acestora
    const cuvinteCheie = {
        'almette': { emoji: '🧈 Almet\u200Bte (35gr)', alergeni: 'lactoza (2gr), proteine din lapte (4gr)' },
        'Almette': { emoji: '🧈 Almett\u200Be (35gr)', alergeni: 'lactoza (2gr), proteine din lapte (4gr)' },
        'ananas': { emoji: '🍍 Ananas\u200B (50gr)', alergeni: 'bromelaina (0.1gr)' },
        'ardei': { emoji: '🫑 ardei\u200B gras (35gr)', alergeni: 'capsaicina (0.1gr)' },
        'banana': { emoji: '🍌 Banan\u200Bă (1buc)', alergeni: 'proteine (1.5gr) ' },
        'Banana': { emoji: '🍌 Bana\u200Bnă (1buc)', alergeni: 'proteine (1.5gr)' },
        'biscuiti': { emoji: '🍪 biscuiț\u200Bi (60gr)', alergeni: 'gluten (2.5gr), ouă (1.5gr), lactoza (1gr)' },
        'Biscuiti': { emoji: '🍪 Biscui\u200Bți (60gr)', alergeni: 'gluten (2.53gr), ouă (1.5gr), lactoza (1gr)' },
        'branza': { emoji: '🧀 brânza\u200B (35gr)', alergeni: 'lactoza (1gr), proteine din lapte (7-10gr)' },
        'broccoli': { emoji: '🥬 broccoli\u200B (35gr)', alergeni: 'salicilati (0.1gr)' },
        'brios': { emoji: '🧁 Brio\u200Bse (60gr)', alergeni: 'gluten (3gr), ouă (1.5gr), lactoza (1-2gr)' },
        'Brios': { emoji: '🧁 Brios\u200Be (60gr)', alergeni: 'gluten (3gr), ouă (1.5gr), lactoza (1-2gr)' },
        'brownie': { emoji: '🍪 Brown\u200Bie (60gr)', alergeni: 'gluten (2-4gr), ouă (1.5gr), lactoza (1-2gr)' },
        'Brownie': { emoji: '🍪 Browni\u200Be\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1.5gr), lactoza (1-2gr)' },
        'burger': { emoji: '🍔 Burge\u200Br (100gr)', alergeni: 'gluten (2-5gr), lactoza (1.5gr)' },
        'Burger': { emoji: '🍔 Burg\u200Ber (100gr)', alergeni: 'gluten (2-5gr), lactoza (1.5gr)' },
        'capsuni': { emoji: '🍓 Căpșuni (50gr)', alergeni: 'fructoza (3-5gr)' },
        'cartofi': { emoji: '🥔 cartof\u200Bi (250gr)', alergeni: 'solanițe (0.1gr)' },
        'Cartofi': { emoji: '🥔 Carto\u200Bfi (250gr)', alergeni: 'solanițe (0.15gr)' },
        'cascaval': { emoji: '🧀 cașcaval\u200B (35gr)', alergeni: 'lactoza (1gr), proteine din lapte (8-12gr)' },
        'ceai': { emoji: '☕ Ce\u200Bai (300ml)', alergeni: 'taninuri (2.5gr)' },
        'Ceai': { emoji: '☕ Cea\u200Bi (300ml)', alergeni: 'taninuri (2.5gr)' },
        'ceapa': { emoji: '🧅 ceapă\u200B ', alergeni: 'compusi sulfurați (0.21)' },
        'cereale': { emoji: '🥣 cereal\u200Be (60gr)', alergeni: 'gluten (2.5gr), zaharuri (13gr)' },
        'Cereale': { emoji: '🥣 Cerea\u200Ble (60gr)', alergeni: 'gluten (2.5gr), zaharuri (13gr)' },
        'chec': { emoji: '🍰 Chec\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'ciocolata': { emoji: '🍫 Ciocolată\u200B ', alergeni: 'lactoza (1-3gr), alune/nuci (1-3gr)' },
        'ciorba': { emoji: '🍲 Ciorb\u200Bă (300ml)', alergeni: 'gluten (2-4gr) (1-2gr)' },
        'Ciorba': { emoji: '🍲 Cior\u200Bbă (300ml)', alergeni: 'gluten (2-4gr) (1-2gr)' },
        'chiftelute': { emoji: '🍔 chifteluțe marinat\u200Be (85gr)', alergeni: 'proteine din carne (40gr)' },
        'clatit': { emoji: '🥞 Clatite\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'croissant': { emoji: '🥐 Croissan\u200Bt (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'Croissant': { emoji: '🥐 Croissa\u200Bnt (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'corn': { emoji: '🥐 Cor\u200Bn (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
         'Corn': { emoji: '🥐 Co\u200Brn (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'cozonac': { emoji: '🍞 Cozonac\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'curcan': { emoji: '🦃 curcan\u200B (85gr)', alergeni: 'proteine din carne (40gr)' },
        'dulceata': { emoji: '🍯 dulceață (30gr)', alergeni: 'fructoza (10-15gr)' },
        'fasole': { emoji: '🥘 fasol\u200Be (250gr)', alergeni: 'lectine (0.13gr)' },
        'gogosi': { emoji: '🍩 Gogoși\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'iaurt': { emoji: '🥛 iaur\u200Bt (60ml)', alergeni: 'lactoza (1-2gr), proteine din lapte (3-5gr)' },
        'Iaurt': { emoji: '🥛 Iau\u200Brt (60ml)', alergeni: 'lactoza (1-2gr), proteine din lapte (3-5gr)' },
        'inghetata': { emoji: '🍨 Înghețată\u200B ', alergeni: 'lactoza (3-5gr), proteine din lapte (2-4gr)' },
        'kiwi': { emoji: '🥝 Kiwi\u200B (1buc) ', alergeni: 'actinidain (1-3gr)' },
        'lamaie': { emoji: '🍋 lămâie\u200B ', alergeni: 'limonen (0.13gr)' },
        'lapte': { emoji: '🐄 lapt\u200Be (300ml)', alergeni: 'lactoza (12-15gr), proteine din lapte (9-12gr)' },
        'Lapte': { emoji: '🐄 Lap\u200Bte (60ml)', alergeni: 'lactoza (13gr), proteine din lapte (9-12gr)' },
        'legume': { emoji: '🥗 legume\u200B (60gr)', alergeni: 'salicilati (0.37gr)' },
        'macaroane': { emoji: '🍝 macaroa\u200Bne (250gr)', alergeni: 'gluten (10-15gr)' },
        'mancare': {emoji: ' mâncare '},
        'mamaliga': { emoji: '🫓 mămălig\u200Bă (250gr)', alergeni: '' },
        'Mamaliga': { emoji: '🫓 Mămăli\u200Bgă (250gr)', alergeni: '' },
        'mere': { emoji: '🍎 M\u200Băr (1buc)', alergeni: 'fructoza (1-3gr)' },
        'Mar': { emoji: '🍎 Măr\u200B (1buc)', alergeni: 'fructoza (1-3gr)' },
        'mandarina': { emoji: '🍊 Mandarin\u200Bă (1buc)', alergeni: 'fructoza (1-3gr)' },
        'Mandarina': { emoji: '🍊 Mandari\u200Bnă (1buc)', alergeni: 'fructoza (1-3gr)' },
        'Mandarine': { emoji: '🍊 Mandar\u200Bină (1buc)', alergeni: 'fructoza (1-3gr)' },
        'mazare': { emoji: '🌱 mazăr\u200Be (250gr)', alergeni: 'lectine (0.17gr)' },
        'Mazare': { emoji: '🌱 Mază\u200Bre (250gr)', alergeni: 'lectine (0.17gr)' },
        'miere': { emoji: '🍯 miere\u200B (30gr)', alergeni: 'polen (0.27gr), proteine de la albine (1-3gr)' },
        'morcov': { emoji: '🥕 morcovi\u200B (50gr)', alergeni: 'beta-caroten (0.26gr)' },
        'omleta': { emoji: '🍳 Omlet\u200Bă (100gr)', alergeni: 'ouă (10-15gr)' },
        'Omleta': { emoji: '🍳 Omle\u200Btă (100gr)', alergeni: 'ouă (10-15gr)' },
        'ou': { emoji: '🥚 ou\u200B (30gr)', alergeni: 'ouă (30gr)' },
        'orez': { emoji: '🍚 ore\u200Bz ', alergeni: 'arsenic (0.01gr)' },
        'Orez': { emoji: '🍚 Or\u200Bez (250gr)', alergeni: 'arsenic (0.01gr)' },
        'paine': { emoji: '(🍞 felie de pâine\u200B 30gr)', alergeni: 'gluten (2-3gr)' },
        'pasta': { emoji: '🧈 past\u200Bă (35gr)', alergeni: 'gluten (dacă din grâu) (1-2gr)' },
        'paste': { emoji: '🍝 Past\u200Be (250gr)', alergeni: 'gluten (10-15gr)' },
        'Paste': { emoji: '🍝 Pas\u200Bte (250gr)', alergeni: 'gluten (10-15gr)' },
        'pate': { emoji: '🧈 paté\u200B (35gr)', alergeni: 'proteine din carne (5-10gr)' },
        'patrunjel': { emoji: '🌿 pătrunjel\u200B ', alergeni: 'apiol (0.013gr)' },
        'para': { emoji: '🍐 Par\u200Bă (1buc)', alergeni: 'fructoza (2-4gr)' },
        'Para': { emoji: '🍐 Pa\u200Bră (1buc)', alergeni: 'fructoza (2-4gr)' },
        'peste': { emoji: '🐟 pește\u200B (85gr)', alergeni: 'proteine din pește (15-20gr)' },
        'pizza': { emoji: '🍕 Pizza\u200B (60gr)', alergeni: 'gluten (2-4gr), lactoza (1-2gr), proteine din lapte (2-3gr)' },
        'pilaf': { emoji: '🍚 Pilaf\u200B (250gr)', alergeni: 'arsenic (0.013gr)' },
        'placinta': { emoji: '🥧 Plăcintă\u200B (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'portocala': { emoji: '🍊 Portocal\u200Bă (1buc)', alergeni: 'fructoza (1-3gr)' },
        'Portocala': { emoji: '🍊 Portoca\u200Blă (1buc)', alergeni: 'fructoza (1-3gr)' },
        'porc': { emoji: '🐷 porc\u200B (85gr)', alergeni: 'proteine din carne (15-20gr)' },
        'prajitura': { emoji: '🍰 Prăjitur\u200Bă (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'Prajitura': { emoji: '🍰 Prăjitu\u200Bră (60gr)', alergeni: 'gluten (2-4gr), ouă (1-2gr), lactoza (1-2gr)' },
        'pui': { emoji: '🍗 pui\u200B (85gr)', alergeni: 'proteine din carne (15-20gr)' },
        'ridichi': { emoji: '🍅 ridichi\u200B ', alergeni: 'isotiocianați (0.015gr)' },
        'rosie': { emoji: '🍅 roșie\u200B ', alergeni: 'histamina (0.019gr)' },
        'struguri': { emoji: '🍇 Strugur\u200Bi (50gr)', alergeni: 'fructoza (3-6gr)' },
        'Struguri': { emoji: '🍇 Strugu\u200Bri (50gr)', alergeni: 'fructoza (3-6gr)' },
        'suc': { emoji: '🧃 Suc\u200B (60ml)', alergeni: 'fructoza (5-10gr)' },
        'supa': { emoji: '🍜 Sup\u200Bă (300ml)', alergeni: 'gluten (1-2gr)' },
        'Supa': { emoji: '🍜 Su\u200Bpă (300ml)', alergeni: 'gluten (1-2gr)' },
        'sunca': { emoji: '🥩 șuncă\u200B (35gr)', alergeni: 'proteine din carne (5-10gr)' },
        'tartina': { emoji: '🥪 Tartin\u200Bă (30gr)', alergeni: 'gluten (1-2gr), lactoza (1gr)' },
        'Tartina': { emoji: '🥪 Tarti\u200Bnă (30gr)', alergeni: 'gluten (1-2gr), lactoza (1gr)' },
        'usturoi': { emoji: '🧄 usturoi\u200B ', alergeni: 'allicin (0.097gr)' },
        'unt': { emoji: '🧈 unt\u200B (15gr)', alergeni: 'lactoza (1gr), proteine din lapte (1-2gr)' },
        'varza': { emoji: '🥬 varză\u200B (250gr)', alergeni: 'isotiocianați (0.013gr)' },
        'salata': { emoji: '🥗 salat\u200Bă (60gr)', alergeni: 'polen (0.14gr)' },
        'Salata': { emoji: 'Sala\u200Btă', alergeni: 'polen (0.14gr)' },
        'salau': { emoji: '🐟 Șală\u200Bu (85gr)', alergeni: 'proteine din pește (15-20gr)' },
        'Salau': { emoji: '🐟 Șal\u200Bău (85gr)', alergeni: 'proteine din pește (15-20gr)' },
        'tocanita': { emoji: '🥘 Tocăniț\u200Bă (250gr)', alergeni: 'proteine (10-15gr), gluten (1-2gr)' }
        // Continuă să adaugi aici restul cuvintelor cheie și emoji-urile corespunzătoare
    };

    var textAreas = document.querySelectorAll('textarea.inputText');
if (textAreas.length > 0) {
    textAreas.forEach(function(textarea, index) {
        textarea.addEventListener('input', function() {
            let text = textarea.value;

            Object.keys(cuvinteCheie).forEach(function(cuvant) {
                const cuvantData = cuvinteCheie[cuvant];
                const regex = new RegExp(cuvant + '(?!\\s*\\u200B)', 'gu');
                if (text.match(regex)) {
                    text = text.replace(regex, cuvantData.emoji + '\u200B');
                    // Adăugăm sau actualizăm alergenii în variabila globală
                    cuvantData.alergeni.split(', ').forEach(alg => {
                        const [nume, cantitate] = alg.split(' (');
                        const cantitateNum = parseInt(cantitate, 10);
                        if (alergeniGlobali[nume]) {
                            alergeniGlobali[nume] += cantitateNum;
                        } else {
                            alergeniGlobali[nume] = cantitateNum;
                        }
                    });
                }
            });

            // Verificăm și adăugăm pâinea dacă este cazul
            const cuvinteCheieMancare = ['Ciorb\u200Bă', 'Cior\u200Bbă' ,'Sup\u200Bă','Su\u200Bpă', 'mazare', 'fasole', 'călită'];
            const contineCuvinteCheieMancare = cuvinteCheieMancare.some(cuvant => text.includes(cuvant));
            const dejaContinePaine = text.includes('(🍞 felie de pâine (30gr))', '(🍞 felie de pâin\u200Be (30gr))', '(🍞 felie de pâi\u200Bne (30gr))' );

            if (contineCuvinteCheieMancare && !dejaContinePaine) {
                text += ' (🍞 felie de pâine (30gr))';
            }

            textarea.value = text; // Actualizează textul cu emoji-urile și alergenii adăugați
            localStorage.setItem('input_' + index, text); // Salvarea imediată
            actualizeazaAlergeni();
        });
    });
}

function actualizeazaAlergeni() {
    let listaAlergeni = document.getElementById('lista-alergeni'); // Asigură-te că există acest element în HTML
    let alergeniText = '<strong>Alergeni:</strong> ' + Object.entries(alergeniGlobali).map(([nume, cantitate]) => `<span style="font-size: smaller; color: darkblue;">${nume} <span style="color: darkred;">(${cantitate}gr)</span></span>`).join(', ');
    listaAlergeni.innerHTML = alergeniText;
}


}
// Apelăm funcția la încărcarea paginii pentru a activa listener-ul
document.addEventListener('DOMContentLoaded', adaugaEmoji);

// salvarea locala a datelor modificate in campurile de input
document.addEventListener('DOMContentLoaded', function() {
    //culori diferite pentru zilele saptamanii
     const zile = ['Luni', 'Marți', 'Miercuri', 'Joi', 'Vineri'];
    const culori = ['#FF00FF', '#32CD32', '#FFA500', '#1E90FF', '#FF69B4']; // Magenta, Verde, Portocaliu, Albastru, Roz

    const thElements = document.querySelectorAll('table tr th');
    thElements.forEach(function(th, index) {
        if (index > 0 && index <= zile.length) { // Ignoră prima celulă ('Ora') și se aplică doar pentru zilele săptămânii
            th.style.color = culori[index - 1];
        }
    });

    // Încărcarea datelor salvate
    document.querySelectorAll('textarea.inputText').forEach(function(textarea, index) {
        var salvat = localStorage.getItem('input_' + index); // Păstrează aceeași cheie pentru compatibilitate
        if (salvat !== null) {
            textarea.value = salvat;
        }

        // Salvarea datelor la modificare
        textarea.addEventListener('input', function() {
            localStorage.setItem('input_' + index, textarea.value); // Folosește aceeași cheie
        });
    });

    // ajustarea inaltimii celulei de tabel in functie de continut
    var textAreas = document.querySelectorAll('textarea.inputText');

    function adjustHeight(textArea) {
        textArea.style.height = 'auto';
        textArea.style.height = textArea.scrollHeight + 'px';
    }

    textAreas.forEach(function(textArea) {
        adjustHeight(textArea); // Ajustează înălțimea inițială pe baza conținutului preexistent
        textArea.addEventListener('input', function() {
            adjustHeight(textArea);
        });
    });
});

</script>
