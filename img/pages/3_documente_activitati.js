
let fisiere_profesor = []; // variabila care se ocupa de fisierele asociate profesorilor
let fisiere_utilizator = []; // variabila care se ocupa de fisierele asociate utilizatorului curent

  // document.addEventListener('DOMContentLoaded', function() {
function fetchIframes() {
    console.log('Fetching iframes...');
    fetch('fetch_iframes.php')
        .then(response => response.json())
        .then(output => {
            console.log('Fetched iframes:', output);
            console.log('Fisiere utilizator:', output.fisiere_utilizator);
            console.log('Fisiere profesor:', output.fisiere_profesor);
            fisiere_utilizator = output.fisiere_utilizator;
            fisiere_profesor = output.fisiere_profesor;

            // Aici adăugați apelul funcției displayIframeGrid dupa ce a fost stabilita calea catre directorul temporar al utilizatorului prin fetchTempCai
            fetchTempCai().then(() => {
                displayIframeGrid();
            });
        });
};

fetchIframes();

//functia care se ocupa de afisarea celor 3 documente in grila si care este apelata din functia fetchIframes
function displayIframeGrid() {
    console.log('Displaying iframe grid...');
    const iframeGridContainer = document.getElementById('iframe_grid_container');
    iframeGridContainer.innerHTML = ''; // Clear the container first
    let combinateFisiere = fisiere_utilizator.concat(fisiere_profesor);

    combinateFisiere.sort((a, b) => new Date(b.data_upload) - new Date(a.data_upload));

    let latestIframes = combinateFisiere.filter(fisiere => fisiere.extensie === 'pdf' ).slice(0, 3);
    console.log('Latest iframes:', latestIframes);

    latestIframes.forEach((fisiere, index) => {
        console.log(`Processing file ${index + 1}:`, fisiere); // Log the file being processed
        const iframeGridItem = document.createElement('div');
        iframeGridItem.classList.add('iframe-grid-item');

        const img = document.createElement('img'); // Creăm un element img în loc de iframe
        img.src = temp_cai[fisiere.id_utilizator] + fisiere.nume_fisier.replace('.pdf', '.png');
        // Setăm sursa la calea către fișierul thumbnail
        // img.src = temp_paths[file.id_utilizator] + file.nume_fisier;
        console.log(`Image src for file ${index + 1}:`, img.src); // Log the src of the image
        img.alt = fisiere.nume_fisier;
        img.classList.add('galerie-activitati');
        img.classList.add('iframe-grid-item');
        img.id = `img_${fisiere.id_info}`;

        iframeGridItem.appendChild(img);
        iframeGridContainer.appendChild(iframeGridItem);
    });
}


//acesta este codul care stabileste caile catre relative catre fisierele care trebuiesc listate
let temp_cai = {};

function fetchTempCai() {
    console.log('Fetching temp paths...');
    return new Promise((resolve, reject) => {
        fetch("cale_dir_temp.php")
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                console.log('Fetched temp paths:', data);
                data.forEach((user) => {
                    temp_cai[user.id_utilizator] = user.temp_path + "Thumbnailuri/";

                });
                resolve();
            })
            .catch((error) => {
                console.error('Error fetching temp cai:', error);
                reject(error);
            });
    });
}
