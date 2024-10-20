let files_ceilalti = []; // variabila care se ocupa de fisierele asociate profesorilor
let files_administrator = []; // variabila care se ocupa de fisierele asociate utilizatorului curent
let firstFileDisplayed = false; // fisierul primul din lista care se afiseza la incarcare
let displayedFileSrc = ''; // fisierul afisat in fereastra iFrame care si poate fi downloadat
let fileList = []; //lista fisierelor disponibile pentru navigare in fereastra de preview



async function fetchFiles() {
    await fetchTempPaths();
    fetch('fetch_meniuri.php')
        .then(response => response.json())
        .then(output => {
            files_administrator = output.files_administrator;
            numar_meniuri_nou = output.numar_meniuri_nou;

            let fileListHtmlAdministrator = '<h3>...de la administrator:</h3><ul>';

               if (files_administrator.length > 0) {
            files_administrator.forEach(file => {
                    let thumbnailPath;
                    thumbnailPath = temp_paths[file.id_utilizator] + '/Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');

                let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">' +
                    // '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' + // Adăugați miniatura înaintea numelui fișierului
                    '<span class="file-name">' + file.nume_fisier + '</span>' +
                    '</a> - ' +
                    '<span class="file-user-name">' + file.nume_prenume + '</span>' +
                    ' - ' + // document.getElementById('last_uploaded_image').style.display = 'none';
                    '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                    '</li>';

    fileListHtmlAdministrator += fileListHtml;
});
                fileListHtmlAdministrator += '</ul>';
                document.getElementById('file_list_administrator').innerHTML = fileListHtmlAdministrator;

//codul de ascultare eveniment de click pe fisierul care se afiseaza in iframe
files_administrator.forEach((file, index) => {
    console.log(file); // Acest rând va afișa obiectul file în consolă
    document.getElementById('file_' + file.id_info).addEventListener('click', (event) => {
        event.preventDefault();
        console.log('Clicked file:', file.nume_fisier);
        displayFile(event, file.id_info, file.extensie, file.nume_fisier, file.id_utilizator);

     // Revino la poziția inițială a paginii cu derulare fluidă
        scrollToTop();

    });

// Adăugați acest fragment de cod pentru a afișa primul fișier la încărcarea paginii
    if (!firstFileDisplayed && index === 0) {
        firstFileDisplayed = true;
                    displayFile(new MouseEvent('click'), file.id_info, file.extensie, file.nume_fisier, file.id_utilizator);
          }
});

          } else {
                document.getElementById('last_uploaded_file').src = '';
                document.getElementById('file_list_administrator').innerHTML = 'Niciun fișier în baza de date.';
                document.getElementById('file_list_ceilalti').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('A apărut o eroare la preluarea fișierelor:', error);
        });
}


fetchFiles();

//acesta este codul care stabilest caile catre relative catre fisierele care trebuiesc listate
let temp_paths = {};


function fetchTempPaths() {
    return fetch("cale_dir_temp.php")
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            data.forEach((user) => {
                temp_paths[user.id_utilizator] = user.temp_path;
            });
            // Adăugați această linie pentru a verifica valorile temp_paths după ce sunt setate
            console.log('temp_paths after setting:', temp_paths);
        })
        .catch((error) => {
            console.error("There was a problem with the fetch operation:", error);
        });
}

fetchTempPaths();

//functia displayFile care se ocupa de afisarea continutului last_uploaded_file  in fereastra de afisare iframe
function displayFile(event, id_info, extensie, nume_fisier, id_utilizator) {
    event.preventDefault();
    event.stopPropagation();

    const fileNameElements = document.querySelectorAll('.file-name');
    fileNameElements.forEach(element => {
        if (element.parentElement.id === `file_${id_info}`) {
            element.classList.add('file-name-selected');
        } else {
            element.classList.remove('file-name-selected');
        }
    });

    let temp_file_url = temp_paths[id_utilizator];
    let iframeSrc = temp_file_url + nume_fisier;

    if (extensie === 'pdf') {
        document.getElementById('last_uploaded_file').src = iframeSrc;
        document.getElementById('last_uploaded_file').style.display = 'block';
        displayedFileSrc = iframeSrc;
    }
    console.log('temp_file_url:', temp_file_url);

    // Actualizează și afișează titlul fișierului selectat
    const fileTitleContainer = document.getElementById('file-title-container');
    const fileTitle = document.getElementById('file-title');
    fileTitle.textContent = nume_fisier;
    fileTitleContainer.style.display = 'block';
}

// functia download buton
document.getElementById('downloadButton').addEventListener('click', function () {
    if (displayedFileSrc) {
        let downloadLink = document.createElement('a');
        downloadLink.href = displayedFileSrc;
        downloadLink.download = ''; // Aceasta va solicita browserul să folosească numele fișierului din URL pentru descărcare
        downloadLink.click();
    } else {
        alert('Niciun fișier selectat pentru descărcare.');
    }
});

//functia de afisare a profesorilor ,  dar aici va fi afsarea administrator
function fetchProfesori() {
    fetch('fetch_administrativ.php')
        .then(response => response.json())
        .then(profesori => {
            if (profesori.length > 0) {
                let profesoriHtml = '';

                profesori.forEach(profesor => {
                  if (profesor.nume_prenume) {
    profesoriHtml += '<div class="profesor-info">';

    // Verificăm statusul și ajustăm eticheta corespunzător
    let label = "administrativ"; // valoarea implicită
    if (profesor.status === 'administrator') {
        label = "administrator";
    } else if (profesor.status === 'director') {
        label = "director";
    } else if (profesor.status === 'secretara') {
        label = "secretara";
    }

    profesoriHtml += '<span class="profesor-label">' + label + ':</span>';
    profesoriHtml += '<span class="profesor-nume">' + profesor.nume_prenume + '</span>';
    profesoriHtml += '<span class="profesor-label">, email:</span>';
    profesoriHtml += '<span class="profesor-email">' + profesor.email + '</span>';
    profesoriHtml += '</div>';
}

                });

                const profesoriContainer = document.createElement('div');
                profesoriContainer.innerHTML = profesoriHtml;
               document.querySelector('.profesori-container').appendChild(profesoriContainer);

            }
        })
        .catch(error => {
            console.error('A apărut o eroare la preluarea profesorilor:', error);
        });
}



fetchProfesori();

//functia de cautare in pagina
function rezultateCautare() {
    const searchInput = document.querySelector('.search-input');
    const searchString = searchInput.value.toLowerCase();

    files_administrator.forEach(file => {
        const numeFisier = file.nume_fisier.toLowerCase();
        const numeProfesor = file.nume_prenume.toLowerCase();
        const data = new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' });

        const fileElement = document.querySelector(`#file_list_administrator .file-item[data-id="${file.id_info}"]`);

        if (numeFisier.includes(searchString) || numeProfesor.includes(searchString) || data.includes(searchString)) {
            fileElement.classList.remove('hidden');
        } else {
            fileElement.classList.add('hidden');
        }
    });

}


document.querySelector('.search-input').addEventListener('input', rezultateCautare);


//functia care face ca revenirea la fereastra de afisare. iframe sau img, sa fie mult mai fluida
function scrollToTop(elapsedTime) {
    const duration = 1000; // Durata în milisecunde pentru derulare
    const easeInOutCubic = (t) => {
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    };

    const startTime = elapsedTime || performance.now();
    const startPosition = window.scrollY;

    if (startPosition === 0) {
        return;
    }

    const animateScroll = (currentTime) => {
        const timeElapsed = currentTime - startTime;
        const progress = Math.min(timeElapsed / duration, 1);
        const easing = easeInOutCubic(progress);

        window.scrollTo(0, startPosition - startPosition * easing);

        if (progress < 1) {
            window.requestAnimationFrame(animateScroll);
        }
    };

    window.requestAnimationFrame(animateScroll);
}
