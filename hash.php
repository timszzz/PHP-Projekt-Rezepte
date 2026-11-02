<?php
// Nur zum einmaligen Erzeugen des Test-Hashes, danach löschen
echo password_hash('test123', PASSWORD_DEFAULT);