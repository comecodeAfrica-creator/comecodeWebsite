<?php

/**
 * Migration Script: JSON to MySQL
 * 
 * This script helps migrate data from JSON files to MySQL database.
 * Run this once after setting up the database to import existing data.
 * 
 * Usage: php backend/database/migrate.php
 */

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/../config.php';

// Check if database is connected
if (!testDBConnection()) {
    die("ERROR: Cannot connect to database. Check your db_config.php credentials.\n");
}

echo "=== ComeCode JSON to MySQL Migration ===\n\n";

// Counter for tracking imports
$imported = [
    'gallery' => 0,
    'events' => 0,
    'contacts' => 0,
    'about' => 0,
    'settings' => 0
];

// ============ MIGRATE GALLERY ============
echo "Migrating Gallery Images...\n";
$communityData = getCommunityData();
if (!empty($communityData['gallery'])) {
    foreach ($communityData['gallery'] as $item) {
        $id = insertRecord('gallery', [
            'image' => $item['image'] ?? '',
            'caption' => $item['caption'] ?? ''
        ]);
        if ($id) {
            $imported['gallery']++;
        }
    }
    echo "✓ Imported {$imported['gallery']} gallery images\n";
} else {
    echo "- No gallery data to migrate\n";
}

// ============ MIGRATE EVENTS ============
echo "Migrating Events...\n";
if (!empty($communityData['events'])) {
    foreach ($communityData['events'] as $event) {
        $id = insertRecord('events', [
            'title' => $event['title'] ?? '',
            'date' => $event['date'] ?? date('Y-m-d'),
            'description' => $event['description'] ?? '',
            'image' => $event['image'] ?? ''
        ]);
        if ($id) {
            $imported['events']++;
        }
    }
    echo "✓ Imported {$imported['events']} events\n";
} else {
    echo "- No events data to migrate\n";
}

// ============ MIGRATE CONTACTS ============
echo "Migrating Contact Submissions...\n";
$contactData = getContactData();
if (!empty($contactData['submissions'])) {
    foreach ($contactData['submissions'] as $submission) {
        $id = insertRecord('contact_submissions', [
            'name' => $submission['name'] ?? '',
            'email' => $submission['email'] ?? '',
            'company' => $submission['company'] ?? '',
            'service' => $submission['service'] ?? '',
            'message' => $submission['message'] ?? '',
            'status' => $submission['status'] ?? 'new'
        ]);
        if ($id) {
            $imported['contacts']++;
        }
    }
    echo "✓ Imported {$imported['contacts']} contact submissions\n";
} else {
    echo "- No contact data to migrate\n";
}

// ============ MIGRATE ABOUT ============
echo "Migrating About Content...\n";
$aboutData = getAboutData();
if (!empty($aboutData)) {
    // Delete existing about content first
    deleteRecord('about_content', '1=1');

    $id = insertRecord('about_content', [
        'hero_title' => $aboutData['hero_title'] ?? '',
        'hero_subtitle' => $aboutData['hero_subtitle'] ?? '',
        'hero_description' => $aboutData['hero_description'] ?? '',
        'mission' => $aboutData['mission'] ?? '',
        'vision' => $aboutData['vision'] ?? ''
    ]);
    if ($id) {
        $imported['about']++;
    }
    echo "✓ Imported about content\n";
} else {
    echo "- No about data to migrate\n";
}

// ============ MIGRATE SETTINGS ============
echo "Migrating Settings...\n";
$settings = getSettings();
if (!empty($settings)) {
    // Delete existing settings first
    deleteRecord('site_settings', '1=1');

    // Insert settings
    foreach ($settings as $key => $value) {
        if ($key === 'social_links') {
            // Store as JSON
            $id = insertRecord('site_settings', [
                'setting_key' => $key,
                'setting_value' => json_encode($value),
                'setting_type' => 'json'
            ]);
        } else {
            // Store as string
            $id = insertRecord('site_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => 'string'
            ]);
        }
        if ($id) {
            $imported['settings']++;
        }
    }
    echo "✓ Imported {$imported['settings']} settings\n";
} else {
    echo "- No settings data to migrate\n";
}

// ============ SUMMARY ============
echo "\n=== Migration Summary ===\n";
echo "Gallery Images: {$imported['gallery']}\n";
echo "Events: {$imported['events']}\n";
echo "Contact Submissions: {$imported['contacts']}\n";
echo "About Content: {$imported['about']}\n";
echo "Settings: {$imported['settings']}\n";

$total = array_sum($imported);
echo "\nTotal Records Imported: $total\n";

if ($total > 0) {
    echo "\n✓ Migration completed successfully!\n";
    echo "You can now delete the JSON files in backend/data/ if you prefer.\n";
} else {
    echo "\n⚠ No data was migrated. Check if JSON files exist and contain data.\n";
}

echo "\nNote: Keep your JSON files as backup until you verify all data is working correctly.\n";
?>