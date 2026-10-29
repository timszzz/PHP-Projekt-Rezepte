
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rezept erstellen</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 500px;
        }

        label {
            font-weight: bold;
        }

        input, textarea, select {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
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
            <option value="1">Hauptgericht</option>
            <option value="2">Dessert</option>
            <option value="3">Frühstück</option>
            <option value="4">Getränke</option>
         </select>

        <button type="submit">
            Rezept speichern
        </button>

    </form>

</body>
</html>
