<?php
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/RecipeRepository.php';
require_once __DIR__ . '/../src/CategoryRepository.php';

$db = new Db();

// Repository für Datenbankoperationen mit Rezepten
$recipeRepo = new RecipeRepository($db);

// Repository für Kategorien
$categoryRepo = new CategoryRepository($db);

// Session für einmalige Erfolgsmeldungen starten
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Kategorien über das CategoryRepository laden
$categories = $categoryRepo->findAll();

// Fehlermeldungen sammeln
$errors = [];

// Einmalige Erfolgsmeldung aus der Session lesen
$success = $_SESSION['recipe_success'] ?? '';

// Meldung nach dem Lesen löschen
unset($_SESSION['recipe_success']);

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

   // Datentypen prüfen
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

      // Titel prüfen
      if ($title === '') {
         $errors[] = 'Bitte geben Sie einen Titel ein.';
      } elseif (mb_strlen($title, 'UTF-8') > 150) {
         $errors[] = 'Der Titel darf maximal 150 Zeichen lang sein.';
      }

      // Zutaten prüfen
      if ($ingredients === '') {
         $errors[] = 'Bitte geben Sie die Zutaten ein.';
      }

      // Zubereitung prüfen
      if ($preparation === '') {
         $errors[] = 'Bitte beschreiben Sie die Zubereitung.';
      }

      // Prüfen, ob die gewählte Kategorie in der Datenbank existiert
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
   // Rezept nur speichern, wenn keine Validierungsfehler vorliegen
   if (empty($errors)) {
         // Datenbankoperation über das RecipeRepository ausführen -> create()
         $recipeRepo->create(
            $title,
            $ingredients,
            $preparation,
            (int)$categoryId,
            (int)$_SESSION['user_id']
         );

         // Erfolgsmeldung für die nächste Anfrage speichern
         $_SESSION['recipe_success'] = 'Das Rezept wurde erfolgreich gespeichert.';

         // Nach erfolgreichem INSERT zur GET-Seite weiterleiten (PRG)
         header('Location: create.php', true, 303);
         exit;
   }
}

// Seitentitel für das gemeinsame Layout festlegen
$pageTitle = 'Rezept erstellen';

// Eigenes Stylesheet für das Rezeptformular
$extraCss = '/php/PHP-Projekt-Rezepte/recipes/css/create.css';

// Gemeinsamen Header einbinden
require_once __DIR__ . '/../templates/header.php';
?>

<h1>Neues Rezept erstellen</h1>

   <!-- Fehlermeldungen anzeigen, falls die Validierung fehlschlägt -->
   <?php if (!empty($errors)): ?>
      <div class="error-box">
         <?php foreach ($errors as $error): ?>
            <p><?= e($error) ?></p>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>

   <?php if ($success !== ''): ?>
      <div class="success-box">
         <p><?= e($success) ?></p>
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
         value="<?= e($title)?>"
         required
      >

      <label for="ingredients">Zutaten:</label>
      <textarea
         id="ingredients"
         name="ingredients"
         rows="5"
         required><?= e($ingredients) ?></textarea>

      <label for="preparation">Zubereitung:</label>
      <textarea
         id="preparation"
         name="preparation"
         rows="7"
         required><?= e($preparation) ?></textarea>

      <label for="category_id">Kategorie:</label>

      <select
         id="category_id"
         name="category_id"
         required>
         <option value="">Kategorie auswählen</option>
         <!-- Die Kategorien werden dynamisch aus der Datenbank geladen.
            Die Hilfsfunktion e() sorgt für eine sichere HTML-Ausgabe
            der Kategorienamen. -->
         <?php foreach ($categories as $category): ?>
            <option
               value="<?= (int)$category['id'] ?>"
               <?= (string)$category['id'] === (string)$categoryId ? 'selected' : '' ?>>
               <?= e($category['name']) ?>
            </option>
         <?php endforeach; ?>

      </select>

      <button type="submit">
         Rezept speichern
      </button>

   </form>

<?php
// Gemeinsamen Footer einbinden
require_once __DIR__ . '/../templates/footer.php';
?>