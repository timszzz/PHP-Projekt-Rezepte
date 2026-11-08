<?php
// Datenbankzugriff auf die Benutzer
class UserRepository
{
    private Db $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    // Liefert einen Benutzer samt Passwort-Hash oder null, wenn es ihn nicht gibt
    public function findByUsername(string $username): ?array
    {
        $sql = 'SELECT id, username, password_hash
                FROM main_user
                WHERE username = :username';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);

        return $stmt->fetch() ?: null;
    }

    // Liefert alle Benutzer ohne Passwort-Hash, alphabetisch nach Namen
    public function findAll(): array
    {
        $sql = 'SELECT id, username, created_at
                FROM main_user
                ORDER BY username';

        return $this->db->query($sql)->fetchAll();
    }

    // Legt einen Benutzer an und liefert seine neue ID
    // Erwartet das Ergebnis von password_hash(), nie das Passwort im Klartext
    public function create(string $username, string $passwordHash): int
    {
        $sql = 'INSERT INTO main_user (username, password_hash)
                VALUES (:username, :password_hash)';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username, 'password_hash' => $passwordHash]);

        return (int) $this->db->lastInsertId();
    }
}
