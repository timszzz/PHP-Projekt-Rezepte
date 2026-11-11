# PHP-Projekt-Rezepte

## Feature-Liste

### Pflicht-Features

- [x] Rezepte anzeigen (für alle Besucher, ohne Login)
- [x] Rezepte suchen
- [x] Rezepte anlegen
- [x] Rezepte ändern
- [x] Rezepte löschen
- [x] Attribute je Rezept: Titel, Zutatenliste, Zubereitung, Kategorie
- [x] Login-Mechanismus, der Anlegen, Ändern und Löschen schützt

### Optionale Erweiterungen

Welche davon wir umsetzen, ist noch offen.

- [x] Kategorien-Filter
- [x] Like-Funktion
- [x] Benutzerverwaltung als Admin

## Voraussetzungen

- [Docker Desktop](https://www.docker.com/get-started) (unter Windows mit WSL2, Installation über `wsl --install`)
- Die Docker-Umgebung aus der Vorlesung: [docker-for-students](https://github.com/ogmueller/docker-for-students)
- Ein aktueller Browser

Die Docker-Umgebung bringt Webserver (nginx), PHP 8.4, MariaDB und phpMyAdmin mit.

## Installation

1. Docker Desktop installieren und starten.

2. Die Docker-Umgebung herunterladen und starten:

   ```bash
   git clone https://github.com/ogmueller/docker-for-students.git
   cd docker-for-students
   cp .env.dist .env        # Windows PowerShell: copy .env.dist .env
   docker compose up -d
   ```

3. Dieses Repository in das Webverzeichnis der Docker-Umgebung klonen:

   ```bash
   cd docker-for-students/php
   git clone https://github.com/<USER>/PHP-Projekt-Rezepte.git
   ```

4. Datenbank einrichten: phpMyAdmin unter http://localhost:8081 öffnen, eine Datenbank `rezepte` anlegen und den Dump importieren.

   ```
   TODO: Pfad zum Dump eintragen, z. B. database/dump.sql (fehlt nämlich noch)
   ```

5. Die Anwendung im Browser aufrufen: http://localhost:8080/php/PHP-Projekt-Rezepte/

### Datenbankverbindung

Innerhalb der Docker-Umgebung gelten diese Zugangsdaten:

| Einstellung | Wert |
|-------------|------|
| Host        | `db` |
| Port        | `3306` |
| Benutzer    | `root` |
| Passwort    | (leer) |
| Datenbank   | `rezepte` |

### Test-Login

```
admin / test123
```

## Technik

- PHP 8.4, objektorientiert aufgebaut
- MariaDB, Zugriff über PDO mit Prepared Statements
- HTML und CSS für die Oberfläche
- Sessions für den Login

### Datenbank

Alle Tabellen tragen einheitlich das Präfix `main_`, zum Beispiel `main_recipe` und `main_category`. Das Präfix wird in Schema, Abfragen und Datenbankdump durchgängig verwendet.

Geplante Tabellen (TODO: nach dem Datenbankentwurf anpassen):

| Tabelle         | Inhalt |
|-----------------|--------|
| `main_recipe`   | Rezepte mit Titel, Zutatenliste, Zubereitung und Verweis auf die Kategorie |
| `main_category` | Kategorien |
| `main_user`     | Benutzer mit Login und Passwort-Hash |

## Rezept-Erstellung (Xenias Part (Teilbereich 1))

### Ziel
Mit der Rezept-Erstellung können neue Rezepte über ein Webformular angelegt und in der MariaDB-Datenbank gespeichert werden.
Nach der Integration der Authentifizierung soll diese Funktion ausschließlich angemeldeten Benutzern zur Verfügung stehen.

### Implementierung
Die Rezept-Erstellung befindet sich in `recipes/create.php` und verarbeitet GET- und POST-Anfragen.

- **GET:** Das Formular zur Erstellung eines neuen Rezepts wird angezeigt.
- **POST:** Die eingegebenen Formulardaten werden serverseitig validiert und bei gültigen Eingaben gespeichert.

Die Datenbankoperation erfolgt über die Methode `RecipeRepository::create()`. Dabei wird ein Prepared Statement verwendet, um die Daten sicher in die Tabelle `main_recipe` einzufügen.

Die verfügbaren Kategorien werden mit `CategoryRepository::findAll()` dynamisch aus der Datenbank geladen.

### Formular und Validierung (ERS-FE, ERS-VAL)

Das Formular enthält die Felder Titel, Zutaten, Zubereitung und Kategorie.

Vor dem Speichern werden folgende Prüfungen durchgeführt:

- Alle Pflichtfelder müssen ausgefüllt sein.
- Der Titel darf maximal 150 Zeichen enthalten.
- Die Kategorie muss in der Datenbank existieren.
- Die übermittelten Formulardaten müssen die erwarteten Datentypen besitzen.

Ungültige Eingaben werden mit verständlichen Fehlermeldungen abgelehnt. Bereits eingegebene Werte bleiben erhalten.

Zur sicheren HTML-Ausgabe wird die Hilfsfunktion `e()` verwendet.

### Erfolgsmeldung und Post/Redirect/Get

Nach erfolgreichem Speichern wird eine einmalige Erfolgsmeldung mithilfe einer PHP-Session angezeigt.

Das Post/Redirect/Get-Prinzip mit HTTP-Statuscode 303 verhindert eine erneute Speicherung des Rezepts beim Aktualisieren der Seite.

### Zugriffskontrolle (ERS-SEC)

Die Zugriffskontrolle wurde in die Rezept-Erstellung integriert.

Die Funktion `requireLogin()` aus `src/auth.php` stellt sicher, dass ausschließlich angemeldete Benutzer neue Rezepte erstellen können. Nicht angemeldete Benutzer werden zur Login-Seite weitergeleitet.

Zum Schutz vor Cross-Site Request Forgery (CSRF) enthält das Rezeptformular einen CSRF-Token. Dieser wird bei jeder POST-Anfrage serverseitig mit `checkCsrfToken()` überprüft. Anfragen ohne gültigen Token werden mit HTTP-Status 400 abgelehnt, ohne ein Rezept zu speichern.

Beim Erstellen eines Rezepts wird die Benutzer-ID aus der PHP-Session (`$_SESSION['user_id']`) übernommen und zusammen mit den Rezeptdaten in der Tabelle `main_recipe` gespeichert.

Die serverseitige Validierung der Pflichtfelder bleibt bestehen. Das Formular verwendet `novalidate`, damit die PHP-Validierung und die entsprechenden Fehlermeldungen getestet werden können.

### Tests (ERS-TST)

Die Rezept-Erstellung wird mit gültigen und ungültigen Eingaben getestet.

Die Testfälle umfassen:

- Erfolgreiches Erstellen und Speichern eines Rezepts
- Fehlende Pflichtfelder
- Überschreitung der maximalen Titellänge
- Ungültige Kategorie-ID
- Prüfung auf doppelte Speicherung beim Aktualisieren
- Zugriff ohne Login (nach Integration der Authentifizierung)

Die Testfälle und ihre Ergebnisse sind in `tests/TESTPLAN_ERS.md` dokumentiert.

### Zugehörige Dateien

| Datei | Aufgabe |
|---|---|
| `recipes/create.php` | Formular, Validierung und Verarbeitung |
| `assets/style.css` | Gestaltung des Formulars (gemeinsames Stylesheet) |
| `src/RecipeRepository.php` | Methode `create()` zum Speichern |
| `src/CategoryRepository.php` | Laden der Kategorien |
| `src/helpers.php` | Sichere HTML-Ausgabe über `e()` |
| `templates/header.php` | Gemeinsamer Header |
| `templates/footer.php` | Gemeinsamer Footer |
| `tests/TESTPLAN_ERS.md` | Dokumentation der Testfälle |


## Suche und Rezeptanzeige (Tims Part (Teilbereich 2))

Rezepte können ohne Login angesehen und gesucht werden.

- `index.php` zeigt alle Rezepte alphabetisch mit Kategorie. Über das Suchformular lässt sich ein Begriff in Titel und Zutaten suchen, Groß- und Kleinschreibung spielen dabei keine Rolle.
- `recipes/show.php` zeigt Titel, Kategorie, Zutaten und Zubereitung eines Rezepts. Bei einer ungültigen oder unbekannten ID erscheint „Rezept nicht gefunden“ mit dem Statuscode 404.

| Datei | Aufgabe |
|---|---|
| `src/RecipeRepository.php` | `findAll()`, `findById()`, `search()` |
| `src/CategoryRepository.php` | `findAll()` für die Kategorien |
| `src/helpers.php` | `e()` für die sichere Ausgabe |

Die Rezept-ID muss eine Ganzzahl sein. Der Suchbegriff wird bereinigt und nur per Prepared Statement an die Datenbank übergeben.

Zum Testen im Browser aufrufen: `tests/test_helpers.php`, `tests/test_recipes.php` und `tests/test_search.php`.

## Benutzer und Authentifizierung (Teilbereich 4)

Rezepte ansehen und suchen geht ohne Login. Anlegen, Ändern und Löschen sind nur nach der Anmeldung erlaubt.

- `auth/login.php` zeigt das Formular und prüft die Anmeldedaten. Nach dem Erfolg geht es zurück zur Startseite. Wer von einer geschützten Seite umgeleitet wurde, sieht den Hinweis „Bitte melde dich an“.
- `auth/logout.php` beendet die Session und nimmt nur POST-Anfragen an.
- `users/manage.php` ist die Benutzerverwaltung: Nur der Admin (Benutzername `admin`) sieht alle Benutzer und kann neue anlegen. Andere angemeldete Benutzer bekommen den Statuscode 403.
- Die Navigation in `templates/header.php` zeigt je nach Status „Anmelden“ oder den Benutzernamen und „Abmelden“. Den Menüpunkt „Benutzer“ sieht nur der Admin.

| Datei | Aufgabe |
|---|---|
| `src/UserRepository.php` | `findByUsername()`, `findAll()`, `create()` |
| `src/auth.php` | Session, Validierung, Passwortprüfung, `requireLogin()`, `isAdmin()`, `requireAdmin()`, CSRF-Token |

Für neue Benutzer gilt: Benutzername 3 bis 50 Zeichen aus Buchstaben, Ziffern, `.`, `_` und `-`, Passwort 8 bis 72 Zeichen und zweimal gleich eingegeben. Doppelte Benutzernamen werden abgelehnt.

### Seite schützen

Diese Zeilen gehören ganz oben in jede geschützte Seite, zum Beispiel in `recipes/create.php`. Sie stehen vor der Verarbeitung des Formulars, damit auch POST-Anfragen geprüft werden:

```php
require_once __DIR__ . '/../src/auth.php';
requireLogin();
```

### Sicherheit

- Passwörter liegen nur als Hash von `password_hash()` in der Datenbank und werden mit `password_verify()` geprüft.
- Bei falschem Namen und falschem Passwort erscheint dieselbe Meldung, damit der Login keine Benutzernamen verrät.
- Nach dem Login wird die Session-ID mit `session_regenerate_id(true)` erneuert (Schutz vor Session Fixation).
- Das Session-Cookie ist `HttpOnly` und `SameSite=Lax`, unter HTTPS zusätzlich `Secure`.
- Die Session speichert nur Benutzer-ID und Namen, nie den Passwort-Hash.
- Das Formular in `users/manage.php` ist mit einem CSRF-Token abgesichert (`csrfToken()` und `checkCsrfToken()` in `src/auth.php`).

Zum Testen im Browser aufrufen: `tests/test_auth.php` und `tests/test_protected.php`.

## Erklärung zur Verwendung generativer KI

```
TODO: Hier ehrlich festhalten, welche KI-Tools wofür genutzt wurden.
Die erste Fassung dieses README wurde mit Claude (Anthropic) erstellt.
Der Teilbereich Benutzer und Authentifizierung wurde mit Unterstützung von Claude (Anthropic) erstellt.
```
