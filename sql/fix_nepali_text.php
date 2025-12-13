#!/usr/bin/env php
<?php
/**
 * Fix Corrupted Nepali Text in items.sql
 * 
 * This script reads the SQL file and replaces corrupted HTML entities
 * with proper Nepali Unicode characters
 */

$inputFile = __DIR__ . '/items.sql';
$outputFile = __DIR__ . '/items_fixed.sql';

if (!file_exists($inputFile)) {
    die("Error: items.sql not found!\n");
}

echo "Reading SQL file...\n";
$content = file_get_contents($inputFile);

echo "Decoding HTML entities...\n";
// Decode HTML entities to proper Unicode
$content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

echo "Writing fixed SQL file...\n";
file_put_contents($outputFile, $content);

echo "✓ Complete! Fixed SQL saved to: items_fixed.sql\n";
echo "File size: " . number_format(filesize($outputFile)) . " bytes\n";
