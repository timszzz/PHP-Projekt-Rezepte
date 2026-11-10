/*
 * ERS-T09: CSRF-Schutz der Rezept-Erstellung
 *
 * Ziel:
 * Prüfen, ob ein POST-Request ohne gültiges
 * CSRF-Token vom Server abgelehnt wird.
 *
 * Voraussetzung:
 * Der Benutzer ist angemeldet.
 *
 * Erwartetes Ergebnis:
 * HTTP-Status 400 (Bad Request).
 * Es wird kein Rezept in der Datenbank gespeichert.
 *
 * Ausführung:
 * Den Code in der Browser-Konsole auf der
 * Rezept-Erstellungsseite ausführen.
 */

fetch('/php/PHP-Projekt-Rezepte/recipes/create.php', {
   method: 'POST',
   headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
   },
   body: new URLSearchParams({
      title: 'CSRF-Sicherheitstest',
      ingredients: 'Testzutat',
      preparation: 'Testzubereitung',
      category_id: '1'
      // Absichtlich kein CSRF-Token
   })
})
.then(response => {
   console.log('HTTP-Status:', response.status);
});