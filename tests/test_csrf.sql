
-- ERS-T09: Datenbankprüfung des CSRF-Schutzes
--
-- Ziel:
-- Prüfen, ob ein POST-Request ohne CSRF-Token
-- ein Rezept in der Datenbank angelegt hat.
--
-- Voraussetzung:
-- Der JavaScript-Test test_csrf.js wurde ausgeführt.
--
-- Erwartetes Ergebnis:
-- Die Abfrage liefert 0 Datensätze.

SELECT
    id,
    title,
    user_id
FROM main_recipe
WHERE title = 'CSRF-Sicherheitstest';
