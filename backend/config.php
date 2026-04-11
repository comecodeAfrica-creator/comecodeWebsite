<?php

define('BACKEND_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__));
define('COMMUNITY_DATA_FILE', BACKEND_DIR . '/data/community_data.json');
define('COMMUNITY_UPLOAD_DIR', ROOT_DIR . '/uploads/community/');
define('COMMUNITY_UPLOAD_URL', '../uploads/community/');

date_default_timezone_set('Africa/Lagos');

function ensureCommunityStorage(): void
{
    if (!is_dir(COMMUNITY_UPLOAD_DIR)) {
        mkdir(COMMUNITY_UPLOAD_DIR, 0755, true);
    }

    if (!file_exists(COMMUNITY_DATA_FILE)) {
        $dir = dirname(COMMUNITY_DATA_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(COMMUNITY_DATA_FILE, json_encode(getDefaultCommunityData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function getDefaultCommunityData(): array
{
    return [
        'gallery' => [
            [
                'image' => 'img/community-image-1.jpg',
                'caption' => 'Students collaborating on a new web project.'
            ],
            [
                'image' => 'img/community-image-2.jpg',
                'caption' => 'Our community meetup with local creatives.'
            ],
            [
                'image' => 'img/community-image-3.jpg',
                'caption' => 'Hands-on coding and design workshop.'
            ]
        ],
        'events' => [
            [
                'title' => 'Design Sprint Session',
                'date' => '2026-04-25',
                'description' => 'A full day of rapid ideation and UI/UX collaboration.',
                'image' => 'img/community-image-4.jpg'
            ],
            [
                'title' => 'Tech Talk: Web Performance',
                'date' => '2026-05-08',
                'description' => 'Learn how to create lightning-fast digital experiences.',
                'image' => 'img/community-image-5.jpg'
            ]
        ]
    ];
}

function getCommunityData(): array
{
    ensureCommunityStorage();
    $content = file_get_contents(COMMUNITY_DATA_FILE);
    $data = json_decode($content, true);

    if (!is_array($data)) {
        $data = getDefaultCommunityData();
        saveCommunityData($data);
    }

    if (!isset($data['gallery']) || !is_array($data['gallery'])) {
        $data['gallery'] = [];
    }

    if (!isset($data['events']) || !is_array($data['events'])) {
        $data['events'] = [];
    }

    return $data;
}

function saveCommunityData(array $data): bool
{
    ensureCommunityStorage();
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(COMMUNITY_DATA_FILE, $json) !== false;
}

function sanitizeText(string $text): string
{
    return trim(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
}

function makeFilename(string $name): string
{
    return preg_replace('/[^a-z0-9._-]+/i', '-', strtolower($name));
}

function buildUploadPath(string $filename): string
{
    return COMMUNITY_UPLOAD_DIR . basename($filename);
}

function buildUploadUrl(string $filename): string
{
    return COMMUNITY_UPLOAD_URL . basename($filename);
}
