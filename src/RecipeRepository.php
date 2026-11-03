<?php
// Datenbankzugriff auf die Rezepte
class RecipeRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Liefert alle Rezepte mit Kategorienamen, alphabetisch nach Titel
    public function findAll(): array
    {
        $sql = 'SELECT r.id, r.title, c.name AS category
                FROM main_recipe r
                JOIN main_category c ON r.category_id = c.id
                ORDER BY r.title';

        return $this->db->query($sql)->fetchAll();
    }
}