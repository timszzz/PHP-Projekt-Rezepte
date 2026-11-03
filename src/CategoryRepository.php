<?php
// Datenbankzugriff auf die Kategorien
class CategoryRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Liefert alle Kategorien, alphabetisch nach Name
    public function findAll(): array
    {
        return $this->db->query('SELECT id, name FROM main_category ORDER BY name')->fetchAll();
    }
}