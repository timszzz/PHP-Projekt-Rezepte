<?php
// Rezept bearbeiten: nur der Ersteller darf die Daten ändern.
require_once __DIR__ . '/../src/Db.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/RecipeRepository.php';
require_once __DIR__ . '/../src/CategoryRepository.php';
require_once __DIR__ . '/../src/auth.php';

// Login-Prüfung vor jeder weiteren Verarbeitung (auch bei POST).
requireLogin();

$db = new Db();
$recipeRepo = new RecipeRepository($db);
$categoryRepo = new CategoryRepository($db);
$userId = (int) $_SESSION['user_id'];

// Rezept-ID aus der URL prüfen, bei ungültiger ID gibt es kein Rezept.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$recipe = ($id && $id > 0) ? $recipeRepo->findById($id) : null;

// Nicht vorhandene Rezepte und Rezepte anderer Benutzer gleich behandeln.
// Die Meldung erscheint im normalen Seitenlayout statt als reiner Text.
if ($recipe === null || (int) $recipe['user_id'] !== $userId) {
    http_response_code(404);
    $pageTitle = 'Rezept nicht gefunden';
    require __DIR__ . '/../templates/header.php';
    ?>
    <h1>Rezept bearbeiten</h1>
    <p role="alert">Rezept nicht gefunden oder keine Berechtigung.</p>
    <p><a href="<?= BASE_URL ?>/index.php">Zurück zu allen Rezepten</a></p>
    <?php
    require __DIR__ . '/../templates/footer.php';
    exit;
}

$categories = $categoryRepo->findAll();
$validCategoryIds = array_map('strval', array_column($categories, 'id'));
$errors = [];

$title = $recipe['title'];
$ingredients = $recipe['ingredients'];
$preparation = $recipe['preparation'];
$categoryId = (string) $recipe['category_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF-Prüfung muss vor der Änderung der Datenbank erfolgen.
    if (!checkCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(400);
        $errors[] = 'Das Formular ist abgelaufen. Bitte laden Sie die Seite neu.';
    } else {
        $titleInput = $_POST['title'] ?? null;
        $ingredientsInput = $_POST['ingredients'] ?? null;
        $preparationInput = $_POST['preparation'] ?? null;
        $categoryInput = $_POST['category_id'] ?? null;

        if (!is_string($titleInput) || !is_string($ingredientsInput)
            || !is_string($preparationInput) || !is_string($categoryInput)) {
            $errors[] = 'Die Formulardaten sind ungültig.';
        } else {
            $title = trim($titleInput);
            $ingredients = trim($ingredientsInput);
            $preparation = trim($preparationInput);
            $categoryId = $categoryInput;

            if ($title === '') {
                $errors[] = 'Bitte geben Sie einen Titel ein.';
            } elseif (mb_strlen($title, 'UTF-8') > 150) {
                $errors[] = 'Der Titel darf maximal 150 Zeichen lang sein.';
            }

            if ($ingredients === '') {
                $errors[] = 'Bitte geben Sie die Zutaten ein.';
            }
            if ($preparation === '') {
                $errors[] = 'Bitte beschreiben Sie die Zubereitung.';
            }
            if (!in_array($categoryId, $validCategoryIds, true)) {
                $errors[] = 'Die ausgewählte Kategorie ist ungültig.';
            }
        }

        if (!$errors) {
            // Die user_id wird zusätzlich in der SQL-WHERE-Bedingung geprüft.
            $recipeRepo->updateOwnedRecipes(
                $id,
                $userId,
                $title,
                $ingredients,
                $preparation,
                (int) $categoryId
            );

            header('Location: ' . BASE_URL . '/recipes/show.php?id=' . $id, true, 303);
            exit;
        }
    }
}

$pageTitle = 'Rezept bearbeiten';
require __DIR__ . '/../templates/header.php';
?>
<h1>Rezept bearbeiten</h1>

<?php foreach ($errors as $error): ?>
    <p role="alert"><?= e($error) ?></p>
<?php endforeach; ?>

<form method="post" action="<?= BASE_URL ?>/recipes/edit.php?id=<?= (int) $id ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

    <p>
        <label for="title">Titel:</label><br>
        <input type="text" id="title" name="title" maxlength="150"
               value="<?= e($title) ?>" required>
    </p>

    <p>
        <label for="ingredients">Zutaten:</label><br>
        <textarea id="ingredients" name="ingredients" rows="5" required><?= e($ingredients) ?></textarea>
    </p>

    <p>
        <label for="preparation">Zubereitung:</label><br>
        <textarea id="preparation" name="preparation" rows="7" required><?= e($preparation) ?></textarea>
    </p>

    <p>
        <label for="category_id">Kategorie:</label><br>
        <select id="category_id" name="category_id" required>
            <option value="">Kategorie auswählen</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"
                    <?= (string) $category['id'] === $categoryId ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <button type="submit">Änderungen speichern</button>
    <a href="<?= BASE_URL ?>/recipes/show.php?id=<?= (int) $id ?>">Abbrechen</a>
</form>
<?php require __DIR__ . '/../templates/footer.php'; ?>
