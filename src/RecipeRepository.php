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

    // Liefert ein Rezept mit Kategorienamen oder null, wenn es nicht existiert
    public function findById(int $id): ?array
    {
        $sql = 'SELECT r.id, r.title, r.ingredients, r.preparation, c.name AS category
                FROM main_recipe r
                JOIN main_category c ON r.category_id = c.id
                WHERE r.id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Sucht in Titel und Zutaten, optional auf eine Kategorie eingeschränkt
    public function search(string $term, ?int $categoryId = null): array
    {
        $sql = 'SELECT r.id, r.title, c.name AS category
                FROM main_recipe r
                JOIN main_category c ON r.category_id = c.id
                WHERE (r.title LIKE :title OR r.ingredients LIKE :ingredients)';

        $pattern = '%' . $term . '%';
        $params = ['title' => $pattern, 'ingredients' => $pattern];

        // Kategorie-Filter nur ergänzen, wenn eine gewählt wurde
        if ($categoryId !== null) {
            $sql .= ' AND r.category_id = :category';
            $params['category'] = $categoryId;
        }

        $sql .= ' ORDER BY r.title';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

}