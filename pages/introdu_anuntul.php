<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Introdu Anunțul</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>
    <header id="header_infodisplay">
        <a href="grupa_clasa_copil.php"><div id="schoolName_infodisplay">Unitatea Școlară DEMO</div></a>
        <div id="logo_infodisplay">
            <img src="tid4k.png" alt="TID4K Logo" onclick="saveAnnouncement()">
            <span id="logoCheckmark" class="logoCheckmark">✔</span>
        </div>
    </header>
    <div>
        <!-- Secțiunea pentru introducerea anunțului -->
        <label for="announcementText">Începeți să scrieți Anunțul:</label>
        <textarea id="announcementText" rows="10" cols="50" style="font-size: 18px; color: black;"></textarea>
        <br>
        <!-- Input pentru încărcarea imaginilor -->
        <label for="announcementImages">Adăugați imagini la Anunț:</label>
        <input type="file" id="announcementImages" accept="image/png, image/jpeg" multiple onchange="previewAnnouncementImages(event)">
        <!-- Previzualizarea imaginilor încărcate -->
        <div id="announcementImagesPreview" style="display: flex; flex-wrap: wrap; margin-top: 10px;"></div>
    </div>

    <script>
        // Funcția pentru previzualizarea imaginilor selectate
        function previewAnnouncementImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('announcementImagesPreview');
            previewContainer.innerHTML = '';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.createElement('img');
                    imgElement.src = e.target.result;
                    imgElement.style.maxWidth = '200px';
                    imgElement.style.maxHeight = '150px';
                    imgElement.style.margin = '5px';
                    previewContainer.appendChild(imgElement);
                };
                reader.readAsDataURL(file);
            }
        }

        function saveAnnouncement() {
            const logoCheckmark = document.getElementById('logoCheckmark');
            logoCheckmark.style.display = 'inline';

            const announcementText = document.getElementById('announcementText').value;
            const announcementImagesInput = document.getElementById('announcementImages');
            const announcementImages = announcementImagesInput.files;

            let imagesContent = '';

            if (announcementImages.length > 0) {
                const promises = [];

                for (let i = 0; i < announcementImages.length; i++) {
                    const file = announcementImages[i];

                    const promise = new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgData = e.target.result;
                            imagesContent += `<img src="${imgData}" style="max-width: 200px; max-height: 150px; margin: 5px;">`;
                            resolve();
                        };
                        reader.onerror = function(error) {
                            reject(error);
                        };
                        reader.readAsDataURL(file);
                    });

                    promises.push(promise);
                }

                Promise.all(promises).then(() => {
                    sendAnnouncement(announcementText, imagesContent);
                }).catch((error) => {
                    console.error('Eroare la citirea imaginilor:', error);
                    sendAnnouncement(announcementText, imagesContent);
                });

            } else {
                sendAnnouncement(announcementText, imagesContent);
            }
        }

        function sendAnnouncement(announcementText, imagesContent) {
            const styledAnnouncement = `
                <div class="announcement-container">
                    <p>${announcementText.replace(/\n/g, '<br>')}</p>
                    <div class="images-container">
                        ${imagesContent}
                    </div>
                </div>
            `;

            fetch('salveaza_anuntul.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'continut=' + encodeURIComponent(styledAnnouncement)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Anunțul a fost salvat cu succes.');
                    window.parent.postMessage('announcementSaved', '*');
                } else {
                    console.error('Eroare la salvarea anunțului:', data.error);
                }
            })
            .catch(error => {
                console.error('Eroare la trimiterea anunțului:', error);
            });
        }
    </script>
</body>
</html>
