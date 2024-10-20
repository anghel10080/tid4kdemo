
let files_ceilalti = []; // variabila care se ocupa de fisierele asociate profesorilor
let files_utilizator = []; // variabila care se ocupa de fisierele asociate utilizatorului curent
let firstFileDisplayed = false; // fisierul primul din lista care se afiseza la incarcare
let displayedFileSrc = ''; // fisierul afisat in fereastra iFrame care si poate fi downloadat
let fileList = []; //lista fisierelor disponibile pentru navigare in fereastra de preview


  document.addEventListener('DOMContentLoaded', function() {
function fetchImages() {
    fetch('fetch_images.php')
        .then(response => response.json())
        .then(output => {
            files_utilizator = output.files_utilizator;
            files_ceilalti = output.files_ceilalti;
            status= output.status;

            // fileList este actualizat pentru fereastra de preview a.i. sa poata naviga prin toate imaginile
            fileList = files_utilizator.concat(files_ceilalti);

         let fileListHtmlUtilizator = '<h3>...de la dumneavoastra:</h3><ul>';
            let fileListHtmlCeilalti;

            if (status === 'parinte') {
                fileListHtmlCeilalti = '<h3>...de la profesori:</h3><ul>';
            } else {
                fileListHtmlCeilalti = '<h3>...de la părinți:</h3><ul>';
            }

            if (files_utilizator.length > 0 || files_ceilalti.length > 0) {
                files_utilizator.forEach(file => {
                    let thumbnailPath;
                    thumbnailPath = temp_paths[file.id_utilizator] + '/Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
                    let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">' +
                       // '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' + // Adăugați miniatura înaintea numelui fișierului
                    '<span class="file-name">' + file.nume_fisier + '</span>' +
                    '</a> - ' +
                    '<span class="file-user-name">' + file.nume_prenume + '</span>' +
                    ' - ' +
                    '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                    '</li>';

                    fileListHtmlUtilizator += fileListHtml;
                });

                files_ceilalti.forEach(file => {
                    let thumbnailPath;
                    thumbnailPath = temp_paths[file.id_utilizator] + '/Thumbnailuri/' + file.nume_fisier.replace('.pdf', '.png');
                    let fileListHtml = '<li class="file-item" data-id="' + file.id_info + '"><a href="javascript:void(0);" id="file_' + file.id_info + '">' +
                    // '<img src="' + thumbnailPath + '" class="file-thumbnail" alt="thumbnail">' + // Adăugați miniatura înaintea numelui fișierului
                    '<span class="file-name">' + file.nume_fisier + '</span>' +
                    '</a> - ' +
                    '<span class="file-user-name">' + file.nume_prenume + '</span>' +
                    ' - ' +
                    '<span class="file-date">' + new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' }) + '</span>' +
                    '</li>';

                    fileListHtmlCeilalti += fileListHtml;
                });

                fileListHtmlUtilizator += '</ul>';
                fileListHtmlCeilalti += '</ul>';
                getElementById('file_list_utilizator').innerHTML = fileListHtmlUtilizator;
                getElementById('file_list_profesor').innerHTML = fileListHtmlCeilalti;

                // Aici adăugați apelul funcției displayImageGrid
                fetchTempPaths().then(() => {
                    displayImageGrid();
        });

                       //codul de ascultare eveniment de click pe fisierul care se afiseaza in img
                fileList.forEach((file, index) => {
                    console.log(file); // Acest rând va afișa obiectul file în consolă
    getElementById('file_' + file.id_info).addEventListener('click', (event) => {
        event.preventDefault();
        console.log('Clicked file:', file.nume_fisier);
        displayFile(event, file.id_info, file.extensie, file.nume_fisier, file.id_utilizator, file.nume_prenume, file.data_upload);

     // Revino la poziția inițială a paginii cu derulare fluidă
        scrollToTop();
                });
                // Adăugați acest fragment de cod pentru a afișa primul fișier la încărcarea paginii
    if (!firstFileDisplayed && index === 0) {
        firstFileDisplayed = true;


        fetchTempPaths().then(() => {
           displayImageGrid();
        });
    }
});

            } else {
                getElementById('image_grid_container').innerHTML = 'Niciun fișier în baza de date.';
                getElementById('file_list_utilizator').innerHTML = 'Niciun fișier în baza de date.';
                getElementById('file_list_profesor').innerHTML = '';
            }
        })
        .catch(error => {
            console.error('A apărut o eroare la preluarea fișierelor:', error);
        });
}

fetchImages();

//functia care se ocupa de afisarea celor 9 imagini in grila si care este apelata din functia fetchFiles
function displayImageGrid() {
    const imageGridContainer = document.getElementById('image_grid_container');
    imageGridContainer.innerHTML = ''; // Clear the container first
    let combinedFiles = files_utilizator.concat(files_ceilalti);

    combinedFiles.sort((a, b) => new Date(b.data_upload) - new Date(a.data_upload));

    let latestImages = combinedFiles.filter(file => file.extensie === 'png' || file.extensie === 'jpg' || file.extensie === 'jpeg' || file.extensie === 'bmp').slice(0, 9);

    latestImages.forEach((file, index) => {
        const imageGridItem = document.createElement('div');
        imageGridItem.classList.add('image-grid-item');

        const img = document.createElement('img');
        img.src = temp_paths[file.id_utilizator] + file.nume_fisier;
        img.alt = file.nume_fisier;
        img.classList.add('galerie-activitati');
        img.id = `image_${file.id_info}`;

        img.addEventListener('dblclick', (event) => {
            const previewContainer = document.getElementById('preview-container');
            const previewImage = document.getElementById('preview-image');
            previewContainer.classList.remove('preview-container-hidden');
            previewContainer.classList.add('preview-container');
            previewImage.src = img.src;

            displayFile(event, file.id_info, file.extensie, file.nume_fisier, file.id_utilizator, file.nume_prenume, file.data_upload);
        });

        const imageCaption = document.createElement('div');
        imageCaption.classList.add('image-caption');
        imageCaption.id = `caption_${file.id_info}`;
        imageCaption.textContent = file.nume_fisier;

        const imageLabel = document.createElement('div');
        imageLabel.classList.add('image-label');
        imageLabel.classList.add('image-label-hidden');
        imageLabel.id = `label_${file.id_info}`;
        imageLabel.innerHTML = `
            <span class="file-user-name">${file.nume_prenume}</span> -
            <span class="file-date">${new Date(file.data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' })}</span>
        `;

        imageGridItem.appendChild(img);
        imageGridItem.appendChild(imageCaption);
        imageGridItem.appendChild(imageLabel);
        imageGridContainer.appendChild(imageGridItem);
    });
}


//acesta este codul care stabileste caile catre relative catre fisierele care trebuiesc listate
let temp_paths = {};


function fetchTempPaths() {
    return new Promise((resolve, reject) => {
        fetch("cale_dir_temp.php")
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
                resolve();
            })
            .catch((error) => {
                reject(error);
            });
    });
}


fetchTempPaths();


//functia displayFile care marcheaza fisierul selectat la click, incadreaza caseta imaginii selectate si deschide fereastra mare de preview daca nu se afla in ultimele 9 imagini , in ordine descrescatoare
let temp_file_url = '';
let imageSrc = '';
let fileInfoContainer;

function displayFile(event, id_info, extensie, nume_fisier, id_utilizator, nume_prenume, data_upload) {
    // evita propagarea nedorita a evenimentului
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
    let imageSrc = temp_file_url + nume_fisier;

    const imageGridImages = document.querySelectorAll('#image_grid_container .galerie-activitati');
    imageGridImages.forEach(image => {
        if (image.id === `image_${id_info}`) {
            image.classList.add('image-selected');
        } else {
            image.classList.remove('image-selected');
        }
    });

    // caseta violet care incadreaza imaginea selectata
    const imageGridItems = document.querySelectorAll('#image_grid_container .image-grid-item');
    imageGridImages.forEach((image, index) => {
        if (image.id === `image_${id_info}`) {
            image.classList.add('image-selected');
            imageGridItems[index].classList.add('image-box-selected');
        } else {
            image.classList.remove('image-selected');
            imageGridItems[index].classList.remove('image-box-selected');
        }

        imageGridItems.forEach((item, index) => {
            const imageLabel = item.querySelector('.image-label');
            if (imageGridImages[index].id === `image_${id_info}`) {
                // Afișează eticheta pentru imaginea selectată
                imageLabel.classList.remove('image-label-hidden');
            } else {
                // Ascunde eticheta pentru celelalte imagini
                imageLabel.classList.add('image-label-hidden');
            }
        });
    });

    // acest cod afiseaza numele fisierului selectat peste caseta de imagine selectata
    displayedFileSrc = imageSrc;

    // Verificați dacă fisierul selectat se află în cele 9 imagini afișate
    let imageFound = false;
    imageGridImages.forEach(image => {
        if (image.id === `image_${id_info}`) {
            imageFound = true;
        }
    });

if (!imageFound) {
    // Afișează previzualizarea și încarcă imaginea corespunzătoare
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    previewContainer.classList.remove('preview-container-hidden');
    previewContainer.classList.add('preview-container');
    previewImage.src = imageSrc;


}

const previewImage = document.getElementById('preview-image');
let fileInfoContainer = document.querySelector('.file-info-container');

if (!fileInfoContainer) {
    fileInfoContainer = document.createElement('div');
    fileInfoContainer.classList.add('file-info-container');
    fileInfoContainer.style.display = 'none';
    previewImage.parentElement.appendChild(fileInfoContainer);
}

console.log('displayFile: nume_fisier, nume_prenume, data_upload', nume_fisier, nume_prenume, data_upload);
updateFileInfoContainer(fileInfoContainer, nume_fisier, nume_prenume, data_upload);


    previewImage.addEventListener('mouseover', (event) => {
        if (event.target === previewImage) {
            fileInfoContainer.style.display = 'block';
        }
    });

    previewImage.addEventListener('mouseout', (event) => {
        if (event.relatedTarget !== previewImage) {
            fileInfoContainer.style.display = 'none';
        }
    });

}

// functia updateFileInfoContainer se ocupa cu acutalizarea continutului afisat pe banda de informatii afisata peste fereastra de preview a imaginii
function updateFileInfoContainer(fileInfoContainer, nume_fisier, nume_prenume, data_upload) {
    console.log('updateFileInfoContainer', { nume_fisier, nume_prenume, data_upload });

    const fileInfoHTML = `
        <span class="file-name">${nume_fisier}</span> -
        <span class="file-user-name">${nume_prenume}</span> -
        <span class="file-date">${new Date(data_upload).toLocaleString('ro-RO', { year: 'numeric', month: '2-digit', day: '2-digit', hour12: false, hour: '2-digit', minute: '2-digit' })}</span>
    `;

    fileInfoContainer.innerHTML = fileInfoHTML;
}




//se ocupa de descarcarea imaginii
getElementById('downloadButton').addEventListener('click', function () {
    if (displayedFileSrc) {
        let downloadLink = document.createElement('a');
        downloadLink.href = displayedFileSrc;
        downloadLink.download = ''; // Aceasta va solicita browserul să folosească numele fișierului din URL pentru descărcare
        downloadLink.click();
    } else {
        alert('Niciun fișier selectat pentru descărcare.');
    }
});



function fetchProfesori() {
    fetch('profesori_grupa_clasa.php')
        .then(response => response.json())
        .then(profesori => {
            if (profesori.length > 0) {
                let profesoriHtml = '';

                profesori.forEach(profesor => {
                    if (profesor.nume_prenume) {
                      profesoriHtml += '<div class="profesor-info">';
profesoriHtml += '<span class="profesor-label">profesor:</span>';
profesoriHtml += '<span class="profesor-nume">' + profesor.nume_prenume + '</span>';
profesoriHtml += '<span class="profesor-label">, email:</span>';
profesoriHtml += '<span class="profesor-telefon">' + profesor.email + '</span>';
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

//codul legat de fereastra de previzualizare pentru evenimentele de clic pe "x" sau in afara ferestrei de previzualizare si elementele de navigatie stanga-dreapta prntre imaginile previzualizate
const closePreview = document.getElementById('close-preview');
const previewContainer = document.getElementById('preview-container');
const previewNavigationTop = document.getElementById('preview-navigation-top');
const previewNavigationBottom = document.getElementById('preview-navigation-bottom');

function closePreviewFunction() {
    previewContainer.classList.remove('preview-container');
    previewContainer.classList.add('preview-container-hidden');
}

closePreview.addEventListener('click', closePreviewFunction);

previewContainer.addEventListener('click', (event) => {
    if (event.target === previewContainer) {
        closePreviewFunction();
    }
});

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closePreviewFunction();
    }
});

let currentIndex = -1;


//codul care navigheaza, in fereastra de preview, printre imagini
function changeImage(direction) {
    // Concatenați files_utilizator și files_ceilalti în fileList

    const newIndex = currentIndex + direction;

    if (newIndex >= 0 && newIndex < fileList.length) {
        currentIndex = newIndex;
        const selectedFile = fileList[currentIndex];
        const { id_info, extensie, nume_fisier, id_utilizator, nume_prenume, data_upload } = selectedFile;
        const temp_file_url = temp_paths[id_utilizator];
        const imageSrc = temp_file_url + nume_fisier;

        const previewImage = document.getElementById('preview-image');
        previewImage.src = imageSrc;

        // Update fileInfoContainer in changeImage function
        const fileInfoContainer = document.querySelector('.file-info-container');
        updateFileInfoContainer(fileInfoContainer, nume_fisier, nume_prenume, data_upload);
    }
}


// Adăugați ascultători de evenimente pentru săgețile de navigație
previewNavigationTop.addEventListener('click', () => changeImage(-1));
previewNavigationBottom.addEventListener('click', () => changeImage(1));


//ascultatori, tip detector Hammer, pentru detectarea gesturilor pentru touch screen, in sus si respectiv in jos cu actualizarea functiei changeImage ();
const previewImage = document.getElementById('preview-image');

const hammer = new Hammer(previewImage);

hammer.get('swipe').set({ direction: Hammer.DIRECTION_VERTICAL });

hammer.on('swipeup', () => {
    changeImage(1); // Schimbă imaginea în jos
});

hammer.on('swipedown', () => {
    changeImage(-1); // Schimbă imaginea în sus
});


}); //sfarsitul functiei DOM care incarca toate elementele

 //functie creata pentru a identifica elementul care nu se incarca
  function getElementById(id) {
    const element = document.getElementById(id);
    if (!element) {
        console.error('Element not found with ID:', id);
    }
    return element;
}

