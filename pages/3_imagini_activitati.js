
let files_profesor = []; // variabila care se ocupa de fisierele asociate profesorilor
let files_utilizator = []; // variabila care se ocupa de fisierele asociate utilizatorului curent
let fileList = []; //lista fisierelor disponibile pentru navigare in fereastra de preview


  // document.addEventListener('DOMContentLoaded', function() {
function fetchImages() {
    fetch('fetch_images.php')
        .then(response => response.json())
        .then(output => {
            files_utilizator = output.files_utilizator;
            files_profesor = output.files_profesor;



                // Aici adăugați apelul funcției displayImageGrid dupa ce a fost stabilita calea catre directorul temporar al utilizatorului prin fetchTempPaths
                fetchTempPaths().then(() => {
                    displayImageGrid();
        });


                });

};

fetchImages();

//functia care se ocupa de afisarea celor 9 imagini in grila si care este apelata din functia fetchFiles
function displayImageGrid() {
    const imageGridContainer = document.getElementById('image_grid_container');
    imageGridContainer.innerHTML = ''; // Clear the container first
    let combinedFiles = files_utilizator.concat(files_profesor);

    combinedFiles.sort((a, b) => new Date(b.data_upload) - new Date(a.data_upload));

    let latestImages = combinedFiles.filter(file => file.extensie === 'png' || file.extensie === 'jpg' || file.extensie === 'jpeg' || file.extensie === 'bmp').slice(0, 3);

    latestImages.forEach((file, index) => {
        const imageGridItem = document.createElement('div');
        imageGridItem.classList.add('image-grid-item');

        const img = document.createElement('img');
        img.src = temp_paths[file.id_utilizator] + file.nume_fisier;
        img.alt = file.nume_fisier;
        img.classList.add('galerie-activitati');
        img.id = `image_${file.id_info}`;

        imageGridItem.appendChild(img);
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



