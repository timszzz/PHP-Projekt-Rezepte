    -- seed.sql: Testdaten, erst nach schema.sql ausführen
USE rezepte;

-- Reihenfolge wegen Fremdschlüsseln: erst Kategorien und Benutzer, dann Rezepte
INSERT INTO main_category (name) VALUES
    ('Hauptgericht'), ('Dessert'), ('Frühstück'), ('Getränke');

-- Testlogin: admin / test123
INSERT INTO main_user (username, password_hash) VALUES
    ('admin', '$2y$12$1fh.mdj.IBdw/LMIddWZmOT0Iln7uYJeg0XvkFnt6vFkcjuYP9c1S');

INSERT INTO main_recipe (title, ingredients, preparation, category_id, user_id) VALUES
    ('Spaghetti Aglio e Olio',
     '200 g Spaghetti\n3 Knoblauchzehen\n4 EL Olivenöl\n1 Chilischote\nPetersilie',
     'Nudeln kochen. Knoblauch und Chili in Öl anschwitzen, Nudeln untermischen, Petersilie darüber.',
     1, 1),
    ('Pfannkuchen',
     '250 g Mehl\n500 ml Milch\n3 Eier\n1 Prise Salz',
     'Alles zu einem glatten Teig verrühren, 10 Minuten ruhen lassen, portionsweise in der Pfanne ausbacken.',
     3, 1),
    ('Schokoladenmousse',
     '200 g Zartbitterschokolade\n3 Eier\n200 ml Sahne\n1 EL Zucker',
     'Schokolade schmelzen, Eigelb unterrühren, Sahne und Eischnee unterheben, 4 Stunden kühlen.',
     2, 1);