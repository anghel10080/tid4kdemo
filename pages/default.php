<!DOCTYPE html>
<html>
<head>
	<title>TID4K - Pagină personalizată</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
	<header>
		<h1>TID4K</h1>
	</header>
	<main>
		<h2>Bun venit la TID4K!</h2>
		<p>Aici veți găsi toate informațiile legate de programul școlar, note, absențe, activități extrașcolare și multe altele.</p>
		<p>Pentru orice întrebări sau nelămuriri, nu ezitați să ne contactați.</p>
		<a href="../index.php">Deconectare</a>

		<?php
			// Obținem id-ul sesiunii utilizatorului din cookie
			$id_sesiune_utilizator = $_COOKIE['id_sesiune_utilizator'];

			// Obținem datele utilizatorului din baza de date MySQL
			$query = "SELECT * FROM utilizatori WHERE id_sesiune_utilizator = '$id_sesiune_utilizator'";
			$result = mysqli_query($conn, $query);
			$utilizator = mysqli_fetch_assoc($result);

			// Afisam datele utilizatorului
			echo 'Bine ai venit, ' . $utilizator['nume_prenume'] . '!<br>';
			echo 'Numele copilului: ' . $utilizator['nume_copil'] . '<br>';
			echo 'Vârsta copilului: ' . $utilizator['varsta_copil'] . '<br>';
			echo 'Grupa/clasa: ' . $utilizator['grupa_clasa'] . '<br>';
		?>
	</main>
	<footer>
		<p>TID4K &copy; 2023</p>
	</footer>
</body>
</html>
