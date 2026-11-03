<?php
// create.php hat zwei Aufgaben:
// Bei einer GET-Anfrage wird das Formular angezeigt.
// Bei einer POST-Anfrage werden die eingegebenen Formulardaten verarbeitet

require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';

$db = new Db();

$stmt = $db->query(
   'SELECT id, name FROM main_category ORDER BY name'
);

$categories = $stmt->fetchAll();

// Fehlermeldungen sammeln
$errors = [];

// Erfolgsmeldung nach erfolgreichem Speichern anzeigen
$success = ($_SERVER['REQUEST_METHOD'] === 'GET')
   && (($_GET['success'] ?? '') === '1');

// Formularwerte initialisieren
$title = '';
$ingredients = '';
$preparation = '';
$categoryId = '';

// Prüfen, ob das Formular abgeschickt wurde

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   // Formulardaten aus dem POST-Request auslesen
   $titleInput = $_POST['title'] ?? '';
   $ingredientsInput = $_POST['ingredients'] ?? '';
   $preparationInput = $_POST['preparation'] ?? '';
   $categoryInput = $_POST['category_id'] ?? '';

   // ERS-VAL: Datentypen prüfen
   if (
      !is_string($titleInput) ||
      !is_string($ingredientsInput) ||
      !is_string($preparationInput) ||
      !is_string($categoryInput)
   ) {
      $errors[] = 'Die Formulardaten sind ungültig.';
   } else {

      // Eingaben bereinigen
      $title = trim($titleInput);
      $ingredients = trim($ingredientsInput);
      $preparation = trim($preparationInput);
      $categoryId = $categoryInput;

      // ERS-VAL: Titel prüfen
      if ($title === '') {
         $errors[] = 'Bitte geben Sie einen Titel ein.';
      } elseif (mb_strlen($title, 'UTF-8') > 150) {
         $errors[] = 'Der Titel darf maximal 150 Zeichen lang sein.';
      }

      // ERS-VAL: Zutaten prüfen
      if ($ingredients === '') {
         $errors[] = 'Bitte geben Sie die Zutaten ein.';
      }

      // ERS-VAL: Zubereitung prüfen
      if ($preparation === '') {
         $errors[] = 'Bitte beschreiben Sie die Zubereitung.';
      }

      // ERS-VAL: Prüfen, ob die gewählte Kategorie in der Datenbank existiert
      $validCategoryIds = array_map(
         'strval',
         array_column($categories, 'id')
      );

      if ($categoryId === '') {
         $errors[] = 'Bitte wählen Sie eine Kategorie aus.';
      } elseif (!in_array($categoryId, $validCategoryIds, true)) {
         $errors[] = 'Die ausgewählte Kategorie ist ungültig.';
      }
   }
   // ERS-DB: Rezept nur speichern, wenn keine Validierungsfehler vorliegen
   if (empty($errors)) {
      // SQL INSERT folgt hier
      $sql = 'INSERT INTO main_recipe
               (title, ingredients, preparation, category_id)
               VALUES
               (:title, :ingredients, :preparation, :category_id)';

      $stmt = $db->prepare($sql);

      // SQL-Abfrage mit den Formulardaten ausführen
      $stmt->execute([
         'title' => $title,
         'ingredients' => $ingredients,
         'preparation' => $preparation,
         'category_id' => $categoryId
      ]);

      // ERS-BE: Nach dem Speichern zur GET-Seite weiterleiten
      // Verhindert ein erneutes INSERT beim Aktualisieren der Seite
      header('Location: create.php?success=1', true, 303);
      exit;
   }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
   <meta charset="UTF-8">
   <title>Rezept erstellen</title>
   <link rel="stylesheet" href="css/create.css">
</head>

<body>

   <h1>Neues Rezept erstellen</h1>

   <!-- Fehlermeldungen anzeigen, falls die Validierung fehlschlägt -->
   <?php if (!empty($errors)): ?>
      <div class="error-box">
         <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>

   <?php if ($success): ?>
      <div class="success-box">
         <p>Das Rezept wurde erfolgreich gespeichert.</p>
      </div>
   <?php endif; ?>

   <!-- Der Browser sendet den POST-Request an die aktuelle Seite (create.php). -->
   <form method="POST">

      <label for="title">Titel:</label>

      <!-- Das name-Attribut legt fest, unter welchem Namen der Wert im HTTP-Request übertragen wird.
     Beispiel: name="title" → Zugriff in PHP über $_POST['title']. -->
      <input
         type="text"
         id="title"
         name="title"
         maxlength="150"
         value="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>"
         required>

      <label for="ingredients">Zutaten:</label>
      <textarea
         id="ingredients"
         name="ingredients"
         rows="5"
         required><?= htmlspecialchars($ingredients, ENT_QUOTES, 'UTF-8') ?></textarea>

      <label for="preparation">Zubereitung:</label>
      <textarea
         id="preparation"
         name="preparation"
         rows="7"
         required><?= htmlspecialchars($preparation, ENT_QUOTES, 'UTF-8') ?></textarea>

      <label for="category_id">Kategorie:</label>

      <select
         id="category_id"
         name="category_id"
         required>
         <option value="">Kategorie auswählen</option>
         <!-- Die Kategorien werden aus der Datenbank geladen.
      Das value-Attribut enthält die Kategorie-ID.
      htmlspecialchars() sorgt für eine sichere HTML-Ausgabe
      des Kategorienamens. -->
         <?php foreach ($categories as $category): ?>
            <option
               value="<?= (int)$category['id'] ?>"
               <?= (string)$category['id'] === (string)$categoryId ? 'selected' : '' ?>>
               <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
            </option>
         <?php endforeach; ?>

      </select>

      <button type="submit">
         Rezept speichern
      </button>

   </form>

</body>

</html>