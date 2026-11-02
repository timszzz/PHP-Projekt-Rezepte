<?php

// Hier stehen alle kleinen Hilfsfunktionen des gesamten Projekts

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}