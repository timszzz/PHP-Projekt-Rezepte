# PHP-Projekt-Rezepte

## Feature-Liste

### Pflicht-Features

- [ ] Rezepte anzeigen (für alle Besucher, ohne Login)
- [ ] Rezepte suchen
- [ ] Rezepte anlegen
- [ ] Rezepte ändern
- [ ] Rezepte löschen
- [ ] Attribute je Rezept: Titel, Zutatenliste, Zubereitung, Kategorie
- [ ] Login-Mechanismus, der Anlegen, Ändern und Löschen schützt

### Optionale Erweiterungen

Welche davon wir umsetzen, ist noch offen.

- [ ] Bewertungen und Kommentare durch Besucher
- [ ] Fotos hochladen und anzeigen
- [ ] Foto-Slider zum Durchblättern
- [ ] Zusätzliche Attribute (Zeitaufwand, Schwierigkeitsgrad, Untertitel, Tags, Erstelldatum)
- [ ] Fuzzy Search, also eine unscharfe Suche, die auch bei Tippfehlern Treffer liefert
- [ ] Bemerkungen bzw. Notizen zu Rezepten
- [ ] Benutzerverwaltung

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
   # TODO: Zielordner prüfen und hier eintragen
   git clone https://github.com/<USER>/PHP-Projekt-Rezepte.git
   ```

4. Datenbank einrichten: phpMyAdmin unter http://localhost:8081 öffnen, eine Datenbank `rezepte` anlegen und den Dump importieren.

   ```
   TODO: Pfad zum Dump eintragen, z. B. database/dump.sql
   ```

5. Die Anwendung im Browser aufrufen: http://localhost:8080 (TODO: genauen Pfad ergänzen)

### Datenbankverbindung

Innerhalb der Docker-Umgebung gelten diese Zugangsdaten:

| Einstellung | Wert |
|-------------|------|
| Host        | `db` |
| Port        | `3306` |
| Benutzer    | `root` |
| Passwort    | (leer) |
| Datenbank   | `rezepte` (TODO: endgültigen Namen festlegen) |

### Test-Login

```
TODO: Zugangsdaten eines Testbenutzers aus dem Dump eintragen
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

### Projektstruktur

```
TODO: Ordner und Dateien ergänzen, sobald die Struktur steht.
Kurz dazuschreiben, was in welcher Datei liegt.
```

### Sicherheit

Diese Maßnahmen aus der Vorlesung setzen wir um:

- Passwörter werden mit `password_hash()` gespeichert und mit `password_verify()` geprüft, nie im Klartext.
- Alle Datenbankabfragen mit Benutzereingaben laufen über Prepared Statements (Schutz vor SQL Injection).
- Ausgaben von Benutzereingaben werden mit `htmlspecialchars()` kodiert (Schutz vor XSS).
- Jede schreibende Aktion prüft serverseitig, ob der Benutzer angemeldet ist.
- Nach dem Login wird die Session-ID mit `session_regenerate_id(true)` erneuert.
- Formulare für Anlegen, Ändern und Löschen sind mit einem CSRF-Token abgesichert.

## Erklärung zur Verwendung generativer KI

```
TODO: Hier ehrlich festhalten, welche KI-Tools wofür genutzt wurden.
Die erste Fassung dieses README wurde mit Claude (Anthropic) erstellt.
```
