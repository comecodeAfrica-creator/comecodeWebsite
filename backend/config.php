<?php

define('BACKEND_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__));
define('COMMUNITY_DATA_FILE', BACKEND_DIR . '/data/community_data.json');
define('CONTACT_DATA_FILE', BACKEND_DIR . '/data/contact_data.json');
define('ABOUT_DATA_FILE', BACKEND_DIR . '/data/about_data.json');
define('SETTINGS_DATA_FILE', BACKEND_DIR . '/data/settings_data.json');
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

// ============ CONTACT DATA FUNCTIONS ============
function ensureContactStorage(): void
{
    if (!file_exists(CONTACT_DATA_FILE)) {
        $dir = dirname(CONTACT_DATA_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(CONTACT_DATA_FILE, json_encode(['submissions' => []], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function getContactData(): array
{
    ensureContactStorage();
    $content = file_get_contents(CONTACT_DATA_FILE);
    $data = json_decode($content, true);

    if (!is_array($data) || !isset($data['submissions'])) {
        $data = ['submissions' => []];
        saveContactData($data);
    }

    return $data;
}

function saveContactData(array $data): bool
{
    ensureContactStorage();
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(CONTACT_DATA_FILE, $json) !== false;
}

function addContactSubmission(array $submission): bool
{
    $data = getContactData();
    $submission['id'] = time() . '-' . rand(1000, 9999);
    $submission['date'] = date('Y-m-d H:i:s');
    $submission['status'] = 'new';
    $data['submissions'][] = $submission;
    return saveContactData($data);
}

function deleteContactSubmission(string $id): bool
{
    $data = getContactData();
    $data['submissions'] = array_filter($data['submissions'], function ($item) use ($id) {
        return $item['id'] !== $id;
    });
    $data['submissions'] = array_values($data['submissions']);
    return saveContactData($data);
}

function updateContactSubmissionStatus(string $id, string $status): bool
{
    $data = getContactData();
    foreach ($data['submissions'] as &$submission) {
        if ($submission['id'] === $id) {
            $submission['status'] = $status;
            break;
        }
    }
    return saveContactData($data);
}

// ============ ABOUT DATA FUNCTIONS ============
function getDefaultAboutData(): array
{
    return [
        'hero_title' => 'We are comecode',
        'hero_subtitle' => 'We are your trusted partner',
        'hero_description' => 'Building digital solutions for the fastest-growing companies.',
        'mission' => 'Our mission is to deliver exceptional digital experiences that drive growth and innovation.',
        'vision' => 'To be the leading digital solutions provider trusted by businesses worldwide.',
        'values' => [
            ['title' => 'Innovation', 'description' => 'Constantly pushing boundaries with cutting-edge technology.'],
            ['title' => 'Quality', 'description' => 'Delivering excellence in every project we undertake.'],
            ['title' => 'Partnership', 'description' => 'Building long-term relationships with our clients.']
        ],
        'team' => []
    ];
}

function ensureAboutStorage(): void
{
    if (!file_exists(ABOUT_DATA_FILE)) {
        $dir = dirname(ABOUT_DATA_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(ABOUT_DATA_FILE, json_encode(getDefaultAboutData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function getAboutData(): array
{
    ensureAboutStorage();
    $content = file_get_contents(ABOUT_DATA_FILE);
    $data = json_decode($content, true);

    if (!is_array($data)) {
        $data = getDefaultAboutData();
        saveAboutData($data);
    }

    return $data;
}

function saveAboutData(array $data): bool
{
    ensureAboutStorage();
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(ABOUT_DATA_FILE, $json) !== false;
}

// ============ SETTINGS DATA FUNCTIONS ============
function getDefaultSettings(): array
{
    return [
        'site_name' => 'ComeCode',
        'site_email' => 'info@comecode.com',
        'site_phone' => '+234 XXX XXX XXXX',
        'site_address' => 'Lagos, Nigeria',
        'site_description' => 'Digital solutions for forward-thinking companies',
        'social_links' => [
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'linkedin' => 'https://linkedin.com',
            'instagram' => 'https://instagram.com'
        ]
    ];
}

function ensureSettingsStorage(): void
{
    if (!file_exists(SETTINGS_DATA_FILE)) {
        $dir = dirname(SETTINGS_DATA_FILE);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(SETTINGS_DATA_FILE, json_encode(getDefaultSettings(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function getSettings(): array
{
    ensureSettingsStorage();
    $content = file_get_contents(SETTINGS_DATA_FILE);
    $data = json_decode($content, true);

    if (!is_array($data)) {
        $data = getDefaultSettings();
        saveSettings($data);
    }

    return $data;
}

function saveSettings(array $data): bool
{
    ensureSettingsStorage();
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents(SETTINGS_DATA_FILE, $json) !== false;
}
