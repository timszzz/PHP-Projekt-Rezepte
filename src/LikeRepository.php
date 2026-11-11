
<?php

// Datenbankzugriff auf Rezept-Likes
class LikeRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Anzahl der Likes für ein Rezept
    public function countByRecipe(int $recipeId): int
    {
        $sql = 'SELECT COUNT(*)
                FROM main_recipe_like
                WHERE recipe_id = :recipe_id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['recipe_id' => $recipeId]);

        return (int) $stmt->fetchColumn();
    }

    // Prüft, ob der Benutzer das Rezept gelikt hat
    public function isLiked(int $recipeId, int $userId): bool
    {
        $sql = 'SELECT COUNT(*)
                FROM main_recipe_like
                WHERE recipe_id = :recipe_id
                  AND user_id = :user_id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'recipe_id' => $recipeId,
            'user_id' => $userId
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    // Like hinzufügen oder entfernen
    public function toggle(int $recipeId, int $userId): void
    {
        if ($this->isLiked($recipeId, $userId)) {
            $sql = 'DELETE FROM main_recipe_like
                    WHERE recipe_id = :recipe_id
                      AND user_id = :user_id';
        } else {
            $sql = 'INSERT IGNORE INTO main_recipe_like
                    (recipe_id, user_id)
                    VALUES (:recipe_id, :user_id)';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'recipe_id' => $recipeId,
            'user_id' => $userId
        ]);
    }
}
