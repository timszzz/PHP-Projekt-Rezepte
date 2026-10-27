
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

        input, textarea {
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

    <form method="POST">

        <label for="title">Titel:</label>
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

        <button type="submit">
            Rezept speichern
        </button>

    </form>

</body>
</html>
