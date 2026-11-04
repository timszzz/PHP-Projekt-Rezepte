
# Testplan – Rezept-Erstellung (ERS-TST)

## Ziel

Überprüfung der Rezept-Erstellung mit gültigen und ungültigen Eingaben.
Es soll sichergestellt werden, dass nur gültige Rezepte gespeichert werden.

## Testfälle

| Test-ID | Testfall | Erwartetes Ergebnis | Status |
|---|---|---|---|
| ERS-T01 | Gültiges Rezept erstellen | Rezept wird gespeichert und in der Rezeptliste angezeigt | Bestanden |
| ERS-T02 | Titel leer | Fehlermeldung: Bitte geben Sie einen Titel ein. | Bestanden |
| ERS-T03 | Titel länger als 150 Zeichen | Fehlermeldung: Der Titel darf maximal 150 Zeichen lang sein. | Bestanden |
| ERS-T04 | Zutaten leer | Fehlermeldung: Bitte geben Sie die Zutaten ein. | Bestanden |
| ERS-T05 | Zubereitung leer | Fehlermeldung: Bitte beschreiben Sie die Zubereitung. | Bestanden |
| ERS-T06 | Ungültige Kategorie-ID | Fehlermeldung: Die ausgewählte Kategorie ist ungültig. | Bestanden |
| ERS-T07 | Seite nach erfolgreichem Speichern aktualisieren | Kein doppelter Datenbankeintrag | Bestanden |
| ERS-T08 | Rezept-Erstellung ohne Login aufrufen | Zugriff wird verweigert oder zur Anmeldung weitergeleitet | Ausstehend (AUTH) |

## Teststatus

- Offen: Test noch nicht durchgeführt
- Bestanden: Erwartetes Ergebnis bestätigt
- Fehlgeschlagen: Tatsächliches Ergebnis weicht vom erwarteten Ergebnis ab
- Ausstehend (AUTH): Test erst nach Integration der Authentifizierung möglich
