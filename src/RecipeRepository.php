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
        $sql = 'SELECT r.id, r.title, r.ingredients, r.preparation, r.category_id, r.user_id, c.name AS category
                FROM main_recipe r
                JOIN main_category c ON r.category_id = c.id
                WHERE r.id = :id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    // Sucht den Begriff in Titel und Zutaten
    public function search(string $term): array
    {
        $sql = 'SELECT r.id, r.title, c.name AS category
                FROM main_recipe r
                JOIN main_category c ON r.category_id = c.id
                WHERE r.title LIKE :title OR r.ingredients LIKE :ingredients
                ORDER BY r.title';

        $pattern = '%' . $term . '%';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['title' => $pattern, 'ingredients' => $pattern]);

        return $stmt->fetchAll();
    }

    // Speichert ein neues Rezept in der DB
   public function create(
      string $title,
      string $ingredients,
      string $preparation,
      int $categoryId,
      int $userId
   ): bool
   {
      $sql = 'INSERT INTO main_recipe
      (title, ingredients, preparation, category_id, user_id)
      VALUES
      (:title, :ingredients, :preparation, :category_id, :user_id)';

   $stmt = $this->db->prepare($sql);

   return $stmt->execute([
      'title' => $title,
      'ingredients' => $ingredients,
      'preparation' => $preparation,
      'category_id' => $categoryId,
      'user_id' => $userId
   ]);
   }
}
