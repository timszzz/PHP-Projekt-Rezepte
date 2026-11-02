<?php
require_once __DIR__ . '/../src/Db.php';

$db = new Db();

$stmt = $db->query(
    'SELECT id, name FROM main_category ORDER BY name'
);

$categories = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rezept erstellen</title>

    <style>

   body {
      font-family: Arial, sans-serif;
      background-color: #f5f6f8;
      color: #253043;
      margin: 0;
      padding: 40px 20px;
   }

   h1 {
      font-size: 28px;
      margin-top: 0;
      margin-bottom: 25px;
      text-align: center;
   }

   form {
      display: flex;
      flex-direction: column;
      gap: 12px;

      max-width: 500px;
      margin: 0 auto;

      background-color: white;
      padding: 30px;
      border: 1px solid #e2e5ea;
      border-radius: 12px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
   }

   label {
      font-size: 14px;
      font-weight: bold;
      margin-top: 8px;
   }

   input, textarea, select {
      width: 100%;
      box-sizing: border-box;
      padding: 11px 12px;
      border: 1px solid #cbd2dc;
      border-radius: 6px;
      font-family: inherit;
      font-size: 15px;
      background-color: white;
   }

   input:focus, textarea:focus, select:focus {
      outline: 2px solid #8aa8d6;
      border-color: #507bb8;
   }

   textarea {
      resize: vertical;
   }

   button {
      margin-top: 15px;
      padding: 13px;
      background-color: #345c83;
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      font-weight: bold;
      cursor: pointer;
   }

   button:hover {
      background-color: #284967;
   }

    </style>
</head>
<body>

    <h1>Neues Rezept erstellen</h1>

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
            required
        >

        <label for="ingredients">Zutaten:</label>
        <textarea
            id="ingredients"
            name="ingredients"
            rows="5"
            required
        ></textarea>

        <label for="preparation">Zubereitung:</label>
        <textarea
            id="preparation"
            name="preparation"
            rows="7"
            required
        ></textarea>

      <label for="category_id">Kategorie:</label>

   <!-- Vorläufige Lösung: Die Kategorien sind aktuell fest im HTML-Code definiert. 
   Später werden sie mit einer SQL-Abfrage dynamisch aus der Datenbank geladen. -->
   <!-- Das value-Attribut bestimmt, welcher Wert an den Server gesendet wird.
   Bei "Dessert" wird beispielsweise der Wert "2" übertragen. -->
         <select
            id="category_id"
            name="category_id"
            required
         >
         <option value="">Kategorie auswählen</option>
            <!--<option value="">Kategorie auswählen</option>
            <option value="1">Hauptgericht</option>
            <option value="2">Dessert</option>
            <option value="3">Frühstück</option>
            <option value="4">Getränke</option>-->

      <!-- Die Kategorien werden aus der Datenbank geladen.
      Das value-Attribut enthält die Kategorie-ID.
      htmlspecialchars() sorgt für eine sichere HTML-Ausgabe
      des Kategorienamens. -->
         <?php foreach ($categories as $category): ?>
            <option value="<?= (int)$category['id'] ?>">
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
