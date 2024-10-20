<?php
// Script pentru listarea fișierelor HTML din directorul ~/avizier/
$directory = '../avizier/';
$files = glob($directory . '*.html'); // Obținem toate fișierele HTML din acest director
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>InfoDisplay Selection</title>
    <link rel="stylesheet" href="style.css">
   <!-- Încărcare pdf.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script>
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
</script>

</head>
<body id="body_infodisplay">
    <header id="header_infodisplay">
        <a href="grupa_clasa_copil.php"><div id="schoolName_infodisplay">Grădinița DEMO</div></a>
        <div id="logo_infodisplay">
            <img src="tid4k.png" alt="TID4K Logo">
        </div>
    </header>

    <!-- Interfața cu file -->
<div id="layoutTabs">
    <ul id="tabList">
        <!-- Filele vor fi generate dinamic -->
        <!-- Exemplar:
        <li class="tab active" data-layout-index="0">Layout 1</li>
        <li class="tab" data-layout-index="1">Layout 2</li>
        <li id="addTab">+</li>
        -->
    </ul>
</div>

    <div class="separator_infodisplay"></div>
    <section id="selectionArea_infodisplay">
        <!-- Rândul 1 -->
       <div class="infoBox" id="id_infobox_r11">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r12">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
       <div class="infoBox" id="id_infobox_r13">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r14">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r15">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_meniul">Meniul Săptămânii</li>
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
        <!-- Rândul 2 -->
        <div class="infoBox" id="id_infobox_r21">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mica C</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r22">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
       <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r23">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r24">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r25">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <!-- Rândul 3 -->
        <div class="infoBox" id="id_infobox_r31">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r32">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r33">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r34">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_grupa_mica_A">Grupa Mica A</li>
        <li class="option" data-group="informatii_grupa_mica_B">Grupa Mica B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_A">Grupa Mijlocie A</li>
        <li class="option" data-group="informatii_grupa_mijlocie_B">Grupa Mijlocie B</li>
        <li class="option" data-group="informatii_grupa_mijlocie_C">Grupa Mijlocie C</li>
        <li class="option" data-group="informatii_grupa_mare_A">Grupa Mare A</li>
        <li class="option" data-group="informatii_grupa_mare_B">Grupa Mare B</li>
        <li class="option" data-group="informatii_grupa_mare_C">Grupa Mare C</li>
        <li class="option" data-group="informatii_grupa_mare_D">Grupa Mare D</li>
        </ul></div>
        <div class="infoBox" id="id_infobox_r35">
        <ul class="optionList" style="display:none;">
        <li class="option" data-group="informatii_anunturi">Anunțuri</li>
        <li class="option" data-group="informatii_ministerul_educatiei">Ministerul Educației</li>
        <li class="option" data-group="informatii_inspectorat">Inspectorat</li>
        <li class="option" data-group="informatii_extracurricurlare">Extracurriculare</li>
        <li class="option" data-group="informatii_optionale">Opționale</li>
        </ul></div>
    </section>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const logo = document.getElementById('logo_infodisplay');
    const boxes = document.querySelectorAll('.infoBox');
    const tabList = document.getElementById('tabList');
    const addTab = document.createElement('li');
    addTab.id = 'addTab';
    addTab.textContent = '+';
    tabList.appendChild(addTab);

    // Inițializăm bifa verde, dar o ținem ascunsă inițial
    const logoCheckmark = document.createElement('span');
    logoCheckmark.className = 'logoCheckmark';
    logoCheckmark.textContent = '✔';
    logoCheckmark.style.display = 'none';  // Inițial nu este vizibilă
    logo.appendChild(logoCheckmark);

    // Încarcă layout-urile și currentLayoutIndex din localStorage sau inițializează cu un layout implicit
    let layouts = JSON.parse(localStorage.getItem('layouts')) || [
        { name: 'Layout 1', selections: {} }
    ];
    let currentLayoutIndex = parseInt(localStorage.getItem('currentLayoutIndex')) || 0;

    // Funcție pentru a salva layout-urile în LocalStorage
    function saveLayoutsToLocalStorage() {
        localStorage.setItem('layouts', JSON.stringify(layouts));
        localStorage.setItem('currentLayoutIndex', currentLayoutIndex);
    }

    // Funcție pentru a afișa filele de layout
    function renderTabs() {
        // Șterge toate filele, cu excepția butonului de adăugare
        while (tabList.firstChild && tabList.firstChild !== addTab) {
            tabList.removeChild(tabList.firstChild);
        }

        layouts.forEach((layout, index) => {
            const tab = document.createElement('li');
            tab.className = 'tab' + (index === currentLayoutIndex ? ' active' : '');
            tab.setAttribute('data-layout-index', index);

            const tabName = document.createElement('span');
            tabName.className = 'tabName';
            tabName.textContent = layout.name;
            tab.appendChild(tabName);

            // Input pentru editarea numelui
            const editNameInput = document.createElement('input');
            editNameInput.className = 'editName';
            editNameInput.type = 'text';
            editNameInput.value = layout.name;
            tab.appendChild(editNameInput);

            // Buton pentru închiderea tab-ului
            const closeTab = document.createElement('span');
            closeTab.className = 'closeTab';
            closeTab.textContent = '×';
            tab.appendChild(closeTab);

            tabList.insertBefore(tab, addTab);

            // Eveniment pentru comutarea la acest tab
            tab.addEventListener('click', function(e) {
                if (e.target !== closeTab && e.target !== tabName && e.target !== editNameInput) {
                    currentLayoutIndex = index;
                    saveLayoutsToLocalStorage();
                    renderTabs();
                    updateUIWithSelections();
                }
            });

            // Eveniment pentru editarea numelui tab-ului
            tabName.addEventListener('click', function(e) {
                e.stopPropagation();
                tab.classList.add('editing');
                editNameInput.focus();
            });

            // Eveniment pentru închiderea tab-ului
            closeTab.addEventListener('click', function(e) {
                e.stopPropagation();
                if (layouts.length > 1) {
                    layouts.splice(index, 1);
                    if (currentLayoutIndex >= index) {
                        currentLayoutIndex = Math.max(0, currentLayoutIndex - 1);
                    }
                    saveLayoutsToLocalStorage();
                    renderTabs();
                    updateUIWithSelections();
                } else {
                    alert('Nu puteți șterge ultimul layout.');
                }
            });

            // Evenimente pentru editarea numelui
            editNameInput.addEventListener('blur', function() {
                const newName = editNameInput.value.trim();
                if (newName) {
                    layouts[index].name = newName;
                    saveLayoutsToLocalStorage();
                    renderTabs();
                }
                tab.classList.remove('editing');
            });

            editNameInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    editNameInput.blur();
                }
            });
        });
    }

    // Eveniment pentru adăugarea unei noi file
    addTab.addEventListener('click', function() {
        const newLayoutName = 'Layout ' + (layouts.length + 1);
        layouts.push({ name: newLayoutName, selections: {} });
        currentLayoutIndex = layouts.length - 1;
        saveLayoutsToLocalStorage();
        renderTabs();
        updateUIWithSelections();
    });

    // Funcție pentru actualizarea UI-ului cu selecțiile curente
    function updateUIWithSelections() {
        const selections = layouts[currentLayoutIndex].selections;

        // Resetăm toate box-urile și opțiunile
        boxes.forEach(box => {
            box.classList.remove('selected');
            box.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            const optionList = box.querySelector('.optionList');
            optionList.style.display = 'none';
        });

        // Aplicăm selecțiile din layout-ul curent
        Object.keys(selections).forEach(boxId => {
            const box = document.getElementById(boxId);
            if (box) {
                box.classList.add('selected');
                const optionList = box.querySelector('.optionList');
                optionList.style.display = 'block';
                const selectedOption = selections[boxId];
                box.querySelectorAll('.option').forEach(option => {
                    if (option.getAttribute('data-group') === selectedOption.group && option.textContent === selectedOption.title) {
                        option.classList.add('selected');
                    }
                });
            }
        });

        // Ascundem bifa verde când schimbăm layout-ul
        logoCheckmark.style.display = 'none';
    }

    // Evenimente pentru box-uri
    boxes.forEach(box => {
        box.addEventListener('click', function() {
            const selections = layouts[currentLayoutIndex].selections;
            this.classList.toggle('selected');
            const optionList = this.querySelector('.optionList');
            optionList.style.display = this.classList.contains('selected') ? 'block' : 'none';
            if (!this.classList.contains('selected')) {
                delete selections[this.id];
            } else {
                // Actualizează selecțiile
                const selectedOption = box.querySelector('.option.selected');
                if (selectedOption) {
                    selections[box.id] = {
                        group: selectedOption.getAttribute('data-group'),
                        title: selectedOption.textContent
                    };
                }
            }
            saveLayoutsToLocalStorage();
        });

        box.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function(event) {
                event.stopPropagation();
                const selections = layouts[currentLayoutIndex].selections;
                box.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selections[box.id] = {
                    group: this.getAttribute('data-group'),
                    title: this.textContent
                };
                saveLayoutsToLocalStorage();
            });
        });
    });

    // Eveniment pentru generarea fișierului HTML
    logo.addEventListener('click', function() {
        const selections = layouts[currentLayoutIndex].selections;
        const layoutName = layouts[currentLayoutIndex].name;

        if (Object.keys(selections).length > 0) {
            // Afișează bifa verde
            logoCheckmark.style.display = 'block';

            // Generăm stringul infodisplay_data (selecțiile curente sub formă de JSON)
            const infodisplayData = JSON.stringify(selections);

            setTimeout(function() {
                fetch('genereaza_tid4kHTML.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        selections: selections,
                        html: generateHTMLContent(layoutName),
                        layoutName: layoutName, // eliminat `tid4k_`
                        infodisplayData: infodisplayData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirecționează către fișierul generat
                        const filename = `../avizier/${data.layoutNameSanitized}.html`;
                        window.location.href = filename; // Suprascriem fișierul cu același nume
                    }
                })
                .catch(error => {
                    console.error('Eroare la trimiterea paginii', error);
                });
            }, 1000);
        } else {
            alert('Selectați opțiuni înainte de a confirma.');
        }
    });

    // Funcție pentru generarea HTML-ului
    function generateHTMLContent(layoutName) {
        return `
        <!DOCTYPE html>
        <html lang="ro">
        <head>
            <meta charset="UTF-8">
            <title>TID4K Avizier - ${layoutName}</title>
            <meta name="infodisplay_data" content='${JSON.stringify(layouts[currentLayoutIndex].selections)}'>
        </head>
        <body id="body_infodisplay">
        <header id="header_infodisplay">
            <a href="../pages/grupa_clasa_copil.php"><div id="schoolName_infodisplay">Grădinița 248</div></a>
            <div class="logo-qrcode-mare"><img src="../logo_qr_code.png" alt="Logo TID4K"><span class="logoCheckmarkPopUp">✔</span></div>
        </header>
        <section id="displayArea"></section>
        </body>
        </html>`;
    }

    // Listăm fișierele HTML din directorul avizier
    const files = <?php echo json_encode($files); ?>;

    files.forEach(file => {
        const filePath = file.replace('../', ''); // Extragem calea relativă

        fetch(filePath)
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const infodisplayMeta = doc.querySelector('meta[name="infodisplay_data"]');

            // Verificăm dacă există tag-ul meta
            if (infodisplayMeta) {
                const infodisplayData = infodisplayMeta.getAttribute('content');
                const selections = JSON.parse(infodisplayData);

                // Verificăm dacă acest layout nu este deja în listă
                if (!layouts.some(layout => layout.name === filePath.replace('../avizier/', '').replace('.html', ''))) {
                    // Adăugăm layout-ul în tab-uri
                    layouts.push({
                        name: filePath.replace('../avizier/', '').replace('.html', ''),
                        selections: selections
                    });

                    renderTabs();
                    updateUIWithSelections();
                }
            }
        });
    });

    renderTabs();
    updateUIWithSelections();
});
</script>
