<?php
require_once __DIR__ . '/config.php';

// Get all data
$communityData = getCommunityData();
$contactData = getContactData();
$aboutData = getAboutData();
$settings = getSettings();

$errors = [];
$success = null;
$activeTab = $_GET['tab'] ?? 'dashboard';

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ============ COMMUNITY MANAGEMENT ============
    if (isset($_POST['upload_gallery'])) {
        $caption = sanitizeText($_POST['gallery_caption'] ?? '');
        $imageUrl = handleUploadImage($_FILES['gallery_image'] ?? [], $errors);

        if ($imageUrl && empty($errors)) {
            $communityData['gallery'][] = [
                'image' => $imageUrl,
                'caption' => $caption
            ];
            if (saveCommunityData($communityData)) {
                $success = 'Gallery image uploaded successfully.';
                $activeTab = 'community';
            } else {
                $errors[] = 'Unable to save gallery data.';
            }
        }
    }

    if (isset($_POST['create_event'])) {
        $title = sanitizeText($_POST['event_title'] ?? '');
        $date = sanitizeText($_POST['event_date'] ?? '');
        $description = sanitizeText($_POST['event_description'] ?? '');
        $imageUrl = handleUploadImage($_FILES['event_image'] ?? [], $errors);

        if (empty($title) || empty($date) || empty($description)) {
            $errors[] = 'Please fill in the event title, date, and description.';
        }

        if ($imageUrl && empty($errors)) {
            $communityData['events'][] = [
                'title' => $title,
                'date' => $date,
                'description' => $description,
                'image' => $imageUrl
            ];
            if (saveCommunityData($communityData)) {
                $success = 'Event created successfully.';
                $activeTab = 'community';
            } else {
                $errors[] = 'Unable to save event data.';
            }
        }
    }

    if (isset($_POST['delete_gallery'])) {
        $index = intval($_POST['gallery_index'] ?? -1);
        if ($index >= 0 && isset($communityData['gallery'][$index])) {
            array_splice($communityData['gallery'], $index, 1);
            if (saveCommunityData($communityData)) {
                $success = 'Gallery image deleted.';
            }
        }
    }

    if (isset($_POST['delete_event'])) {
        $index = intval($_POST['event_index'] ?? -1);
        if ($index >= 0 && isset($communityData['events'][$index])) {
            array_splice($communityData['events'], $index, 1);
            if (saveCommunityData($communityData)) {
                $success = 'Event deleted.';
            }
        }
    }

    // ============ CONTACT MANAGEMENT ============
    if (isset($_POST['add_contact'])) {
        $name = sanitizeText($_POST['contact_name'] ?? '');
        $email = sanitizeText($_POST['contact_email'] ?? '');
        $company = sanitizeText($_POST['contact_company'] ?? '');
        $service = sanitizeText($_POST['contact_service'] ?? '');
        $message = sanitizeText($_POST['contact_message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            $errors[] = 'Please fill in name, email, and message.';
        }

        if (empty($errors)) {
            if (
                addContactSubmission([
                    'name' => $name,
                    'email' => $email,
                    'company' => $company,
                    'service' => $service,
                    'message' => $message
                ])
            ) {
                $success = 'Contact submission added.';
                $activeTab = 'contacts';
                $contactData = getContactData();
            } else {
                $errors[] = 'Unable to save contact submission.';
            }
        }
    }

    if (isset($_POST['delete_contact'])) {
        $id = sanitizeText($_POST['contact_id'] ?? '');
        if (deleteContactSubmission($id)) {
            $success = 'Contact submission deleted.';
            $contactData = getContactData();
        }
    }

    if (isset($_POST['update_contact_status'])) {
        $id = sanitizeText($_POST['contact_id'] ?? '');
        $status = sanitizeText($_POST['contact_status'] ?? '');
        if (in_array($status, ['new', 'replied', 'archived'], true)) {
            if (updateContactSubmissionStatus($id, $status)) {
                $success = 'Contact status updated.';
                $contactData = getContactData();
            }
        }
    }

    // ============ ABOUT MANAGEMENT ============
    if (isset($_POST['update_about'])) {
        $aboutData['hero_title'] = sanitizeText($_POST['hero_title'] ?? '');
        $aboutData['hero_subtitle'] = sanitizeText($_POST['hero_subtitle'] ?? '');
        $aboutData['hero_description'] = sanitizeText($_POST['hero_description'] ?? '');
        $aboutData['mission'] = sanitizeText($_POST['mission'] ?? '');
        $aboutData['vision'] = sanitizeText($_POST['vision'] ?? '');

        if (saveAboutData($aboutData)) {
            $success = 'About section updated successfully.';
            $activeTab = 'about';
        } else {
            $errors[] = 'Unable to save about data.';
        }
    }

    // ============ SETTINGS MANAGEMENT ============
    if (isset($_POST['update_settings'])) {
        $settings['site_name'] = sanitizeText($_POST['site_name'] ?? '');
        $settings['site_email'] = sanitizeText($_POST['site_email'] ?? '');
        $settings['site_phone'] = sanitizeText($_POST['site_phone'] ?? '');
        $settings['site_address'] = sanitizeText($_POST['site_address'] ?? '');
        $settings['site_description'] = sanitizeText($_POST['site_description'] ?? '');

        $settings['social_links'] = [
            'facebook' => sanitizeText($_POST['social_facebook'] ?? ''),
            'twitter' => sanitizeText($_POST['social_twitter'] ?? ''),
            'linkedin' => sanitizeText($_POST['social_linkedin'] ?? ''),
            'instagram' => sanitizeText($_POST['social_instagram'] ?? '')
        ];

        if (saveSettings($settings)) {
            $success = 'Settings updated successfully.';
            $activeTab = 'settings';
        } else {
            $errors[] = 'Unable to save settings.';
        }
    }
}

function handleUploadImage(array $file, array &$errors): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Image upload failed. Please choose a valid file.';
        return null;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $fileName = $file['name'];
    $fileParts = pathinfo($fileName);
    $extension = strtolower($fileParts['extension'] ?? '');

    if (!in_array($extension, $allowedExtensions, true)) {
        $errors[] = 'Supported image formats: JPG, JPEG, PNG, WEBP.';
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = 'The uploaded file is too large. Maximum size is 5MB.';
        return null;
    }

    $basename = time() . '-' . makeFilename($fileParts['filename']) . '.' . $extension;
    $destination = buildUploadPath($basename);
    ensureCommunityStorage();

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'Unable to save the uploaded image. Please check folder permissions.';
        return null;
    }

    return buildUploadUrl($basename);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComeCode Admin | Complete Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        .admin-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e0e0e0;
            flex-wrap: wrap;
        }

        .admin-tabs button {
            padding: 12px 20px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .admin-tabs button.active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
        }

        .admin-tabs button:hover {
            color: #7c3aed;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 12px;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 800;
            margin: 10px 0 0 0;
        }

        .submission-item {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #7c3aed;
        }

        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .submission-name {
            font-weight: 600;
            color: #333;
        }

        .submission-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .submission-status.new {
            background: #fef3c7;
            color: #92400e;
        }

        .submission-status.replied {
            background: #dbeafe;
            color: #1e40af;
        }

        .submission-status.archived {
            background: #e5e7eb;
            color: #4b5563;
        }

        .submission-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        .submission-message {
            background: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #333;
        }

        .submission-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .submission-actions select,
        .submission-actions button {
            font-size: 12px;
            padding: 6px 12px;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .admin-grid.two-column {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        @media (max-width: 768px) {
            .admin-tabs {
                overflow-x: auto;
            }

            .submission-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="grid-lines"></div>

    <script>
        AOS.init();
    </script>

    <header>
        <a href="../index.html">
            <div class="logo">C</div>
        </a>

        <nav id="navbar">
            <ul class="nav-links">
                <li><span class="dot"></span><a href="admin.php">DASHBOARD</a></li>
                <li><span class="dot"></span><a href="community.php">COMMUNITY</a></li>
                <li><span class="dot"></span><a href="../contact.html">CONTACT</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <a href="community.php"><button class="btn-outline desktop-only">VIEW COMMUNITY</button></a>
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </div>
    </header>

    <main class="admin-dashboard">
        <section class="dashboard-hero" data-aos="fade-up" data-aos-duration="1000">
            <div class="dashboard-header">
                <div>
                    <span class="badge badge-secondary">ADMIN PANEL</span>
                    <h1>Complete Dashboard</h1>
                    <p>Manage community, contacts, about page, and site settings all in one place.</p>
                </div>
                <div class="dashboard-actions">
                    <a href="community.php"><button class="btn-purple">Preview Community</button></a>
                    <a href="../index.html"><button class="btn-outline">View Site</button></a>
                </div>
            </div>
        </section>

        <section class="admin-notice">
            <?php if ($success): ?>
                <div class="alert success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>

        <!-- TAB NAVIGATION -->
        <div class="admin-tabs">
            <button class="tab-button <?= $activeTab === 'dashboard' ? 'active' : '' ?>"
                onclick="switchTab('dashboard')">Dashboard</button>
            <button class="tab-button <?= $activeTab === 'community' ? 'active' : '' ?>"
                onclick="switchTab('community')">Community</button>
            <button class="tab-button <?= $activeTab === 'contacts' ? 'active' : '' ?>"
                onclick="switchTab('contacts')">Contacts</button>
            <button class="tab-button <?= $activeTab === 'about' ? 'active' : '' ?>"
                onclick="switchTab('about')">About</button>
            <button class="tab-button <?= $activeTab === 'settings' ? 'active' : '' ?>"
                onclick="switchTab('settings')">Settings</button>
        </div>

        <!-- ============ DASHBOARD TAB ============ -->
        <div id="dashboard" class="tab-content <?= $activeTab === 'dashboard' ? 'active' : '' ?>" data-aos="fade-up">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Gallery Images</h3>
                    <div class="stat-value"><?= count($communityData['gallery'] ?? []) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Community Events</h3>
                    <div class="stat-value"><?= count($communityData['events'] ?? []) ?></div>
                </div>
                <div class="stat-card">
                    <h3>Contact Submissions</h3>
                    <div class="stat-value"><?= count($contactData['submissions'] ?? []) ?></div>
                </div>
                <div class="stat-card">
                    <h3>New Messages</h3>
                    <div class="stat-value">
                        <?= count(array_filter($contactData['submissions'] ?? [], fn($s) => $s['status'] === 'new')) ?>
                    </div>
                </div>
            </div>

            <section class="admin-grid two-column">
                <div class="admin-card">
                    <h2>Recent Gallery Images</h2>
                    <div class="gallery-preview">
                        <?php if (!empty($communityData['gallery'])): ?>
                            <?php foreach (array_slice($communityData['gallery'], -3) as $item): ?>
                                <div class="preview-item">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="Gallery preview"
                                        style="max-width: 100%; border-radius: 8px;">
                                    <p><?= htmlspecialchars($item['caption'] ?? 'No caption') ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No gallery images yet. Add some from the Community tab.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="admin-card">
                    <h2>Upcoming Events</h2>
                    <div class="event-preview">
                        <?php if (!empty($communityData['events'])): ?>
                            <?php foreach (array_slice($communityData['events'], -3) as $event): ?>
                                <div class="preview-event">
                                    <strong><?= htmlspecialchars($event['title']) ?></strong>
                                    <span><?= htmlspecialchars($event['date']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No events scheduled yet. Create your first event from the Community tab.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>

        <!-- ============ COMMUNITY TAB ============ -->
        <div id="community" class="tab-content <?= $activeTab === 'community' ? 'active' : '' ?>" data-aos="fade-up">
            <div class="admin-grid two-column">
                <div class="admin-card">
                    <h2>Upload Gallery Image</h2>
                    <form method="post" enctype="multipart/form-data">
                        <label>Image file</label>
                        <input type="file" name="gallery_image" accept="image/*" required>
                        <label>Caption</label>
                        <input type="text" name="gallery_caption" placeholder="Add a short caption">
                        <button type="submit" name="upload_gallery" class="btn-purple">Upload Image</button>
                    </form>
                </div>

                <div class="admin-card">
                    <h2>Create New Event</h2>
                    <form method="post" enctype="multipart/form-data">
                        <label>Event title</label>
                        <input type="text" name="event_title" placeholder="Enter event title" required>
                        <label>Event date</label>
                        <input type="date" name="event_date" required>
                        <label>Description</label>
                        <textarea name="event_description" rows="4" placeholder="Write a short event description"
                            required></textarea>
                        <label>Event image</label>
                        <input type="file" name="event_image" accept="image/*" required>
                        <button type="submit" name="create_event" class="btn-purple">Publish Event</button>
                    </form>
                </div>
            </div>

            <section class="admin-grid">
                <div class="admin-card">
                    <h2>Manage Gallery</h2>
                    <?php if (!empty($communityData['gallery'])): ?>
                        <?php foreach ($communityData['gallery'] as $index => $item): ?>
                            <div class="submission-item">
                                <div class="submission-header">
                                    <div class="submission-name"><?= htmlspecialchars($item['caption'] ?? 'Untitled') ?></div>
                                </div>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Gallery preview"
                                    style="width: 100%; max-height: 150px; object-fit: cover; border-radius: 6px; margin-bottom: 10px;">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="gallery_index" value="<?= $index ?>">
                                    <button type="submit" name="delete_gallery" class="btn-outline"
                                        style="padding: 6px 12px; font-size: 12px;">Delete Image</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No gallery images yet.</p>
                    <?php endif; ?>
                </div>

                <div class="admin-card">
                    <h2>Manage Events</h2>
                    <?php if (!empty($communityData['events'])): ?>
                        <?php foreach ($communityData['events'] as $index => $event): ?>
                            <div class="submission-item">
                                <div class="submission-header">
                                    <div>
                                        <div class="submission-name"><?= htmlspecialchars($event['title']) ?></div>
                                        <div class="submission-details"><?= htmlspecialchars($event['date']) ?></div>
                                    </div>
                                </div>
                                <div class="submission-message"><?= htmlspecialchars($event['description']) ?></div>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="event_index" value="<?= $index ?>">
                                    <button type="submit" name="delete_event" class="btn-outline"
                                        style="padding: 6px 12px; font-size: 12px;">Delete Event</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No events yet.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <!-- ============ CONTACTS TAB ============ -->
        <div id="contacts" class="tab-content <?= $activeTab === 'contacts' ? 'active' : '' ?>" data-aos="fade-up">
            <div class="admin-card">
                <h2>Add Contact Submission Manually</h2>
                <form method="post" class="admin-grid">
                    <div>
                        <label>Name</label>
                        <input type="text" name="contact_name" placeholder="Contact name" required>
                    </div>
                    <div>
                        <label>Email</label>
                        <input type="email" name="contact_email" placeholder="Email address" required>
                    </div>
                    <div>
                        <label>Company</label>
                        <input type="text" name="contact_company" placeholder="Company name">
                    </div>
                    <div>
                        <label>Service Interested</label>
                        <input type="text" name="contact_service" placeholder="Service name">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label>Message</label>
                        <textarea name="contact_message" rows="4" placeholder="Contact message" required></textarea>
                    </div>
                    <button type="submit" name="add_contact" class="btn-purple"
                        style="grid-column: 1 / -1; width: fit-content;">Add Contact</button>
                </form>
            </div>

            <div class="admin-card">
                <h2>Contact Submissions (<?= count($contactData['submissions'] ?? []) ?>)</h2>
                <?php if (!empty($contactData['submissions'])): ?>
                    <?php foreach (array_reverse($contactData['submissions']) as $submission): ?>
                        <div class="submission-item">
                            <div class="submission-header">
                                <div>
                                    <div class="submission-name"><?= htmlspecialchars($submission['name']) ?></div>
                                    <div class="submission-details">
                                        <span><?= htmlspecialchars($submission['email']) ?></span>
                                        <?php if (!empty($submission['company'])): ?>
                                            | <span><?= htmlspecialchars($submission['company']) ?></span>
                                        <?php endif; ?>
                                        | <span><?= $submission['date'] ?></span>
                                    </div>
                                </div>
                                <span
                                    class="submission-status <?= htmlspecialchars($submission['status']) ?>"><?= ucfirst($submission['status']) ?></span>
                            </div>

                            <?php if (!empty($submission['service'])): ?>
                                <div class="submission-details"><strong>Service:</strong>
                                    <?= htmlspecialchars($submission['service']) ?></div>
                            <?php endif; ?>

                            <div class="submission-message"><?= htmlspecialchars($submission['message']) ?></div>

                            <div class="submission-actions">
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="contact_id" value="<?= htmlspecialchars($submission['id']) ?>">
                                    <select name="contact_status" onchange="this.form.submit()">
                                        <option value="">Change Status...</option>
                                        <option value="new" <?= $submission['status'] === 'new' ? 'selected' : '' ?>>New</option>
                                        <option value="replied" <?= $submission['status'] === 'replied' ? 'selected' : '' ?>>
                                            Replied</option>
                                        <option value="archived" <?= $submission['status'] === 'archived' ? 'selected' : '' ?>>
                                            Archived</option>
                                    </select>
                                    <button type="submit" name="update_contact_status" style="display: none;"></button>
                                </form>
                                <form method="post" style="display: inline;"
                                    onsubmit="return confirm('Delete this submission?');">
                                    <input type="hidden" name="contact_id" value="<?= htmlspecialchars($submission['id']) ?>">
                                    <button type="submit" name="delete_contact" class="btn-outline">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No contact submissions yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ============ ABOUT TAB ============ -->
        <div id="about" class="tab-content <?= $activeTab === 'about' ? 'active' : '' ?>" data-aos="fade-up">
            <div class="admin-card">
                <h2>Edit About Page Content</h2>
                <form method="post">
                    <label>Hero Title</label>
                    <input type="text" name="hero_title" value="<?= htmlspecialchars($aboutData['hero_title'] ?? '') ?>"
                        placeholder="Main hero title" required>

                    <label>Hero Subtitle</label>
                    <input type="text" name="hero_subtitle"
                        value="<?= htmlspecialchars($aboutData['hero_subtitle'] ?? '') ?>" placeholder="Hero subtitle"
                        required>

                    <label>Hero Description</label>
                    <textarea name="hero_description" rows="3"
                        placeholder="Brief description under hero"><?= htmlspecialchars($aboutData['hero_description'] ?? '') ?></textarea>

                    <label>Mission Statement</label>
                    <textarea name="mission" rows="4"
                        placeholder="Company mission"><?= htmlspecialchars($aboutData['mission'] ?? '') ?></textarea>

                    <label>Vision Statement</label>
                    <textarea name="vision" rows="4"
                        placeholder="Company vision"><?= htmlspecialchars($aboutData['vision'] ?? '') ?></textarea>

                    <button type="submit" name="update_about" class="btn-purple">Save About Content</button>
                </form>
            </div>

            <div class="admin-card">
                <h2>Current About Content Preview</h2>
                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                    <h3><?= htmlspecialchars($aboutData['hero_title'] ?? 'N/A') ?></h3>
                    <p><strong><?= htmlspecialchars($aboutData['hero_subtitle'] ?? 'N/A') ?></strong></p>
                    <p><?= htmlspecialchars($aboutData['hero_description'] ?? 'N/A') ?></p>
                    <hr>
                    <p><strong>Mission:</strong> <?= htmlspecialchars($aboutData['mission'] ?? 'N/A') ?></p>
                    <p><strong>Vision:</strong> <?= htmlspecialchars($aboutData['vision'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- ============ SETTINGS TAB ============ -->
        <div id="settings" class="tab-content <?= $activeTab === 'settings' ? 'active' : '' ?>" data-aos="fade-up">
            <div class="admin-card">
                <h2>Site Settings</h2>
                <form method="post">
                    <label>Site Name</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"
                        placeholder="Site name" required>

                    <label>Site Email</label>
                    <input type="email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>"
                        placeholder="Contact email" required>

                    <label>Site Phone</label>
                    <input type="text" name="site_phone" value="<?= htmlspecialchars($settings['site_phone'] ?? '') ?>"
                        placeholder="Contact phone">

                    <label>Site Address</label>
                    <input type="text" name="site_address"
                        value="<?= htmlspecialchars($settings['site_address'] ?? '') ?>" placeholder="Physical address">

                    <label>Site Description</label>
                    <textarea name="site_description" rows="3"
                        placeholder="Short site description"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>

                    <h3 style="margin-top: 30px;">Social Media Links</h3>

                    <label>Facebook URL</label>
                    <input type="url" name="social_facebook"
                        value="<?= htmlspecialchars($settings['social_links']['facebook'] ?? '') ?>"
                        placeholder="https://facebook.com/...">

                    <label>Twitter URL</label>
                    <input type="url" name="social_twitter"
                        value="<?= htmlspecialchars($settings['social_links']['twitter'] ?? '') ?>"
                        placeholder="https://twitter.com/...">

                    <label>LinkedIn URL</label>
                    <input type="url" name="social_linkedin"
                        value="<?= htmlspecialchars($settings['social_links']['linkedin'] ?? '') ?>"
                        placeholder="https://linkedin.com/...">

                    <label>Instagram URL</label>
                    <input type="url" name="social_instagram"
                        value="<?= htmlspecialchars($settings['social_links']['instagram'] ?? '') ?>"
                        placeholder="https://instagram.com/...">

                    <button type="submit" name="update_settings" class="btn-purple">Save Settings</button>
                </form>
            </div>

            <div class="admin-card">
                <h2>Current Settings Preview</h2>
                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px;">
                    <p><strong>Site Name:</strong> <?= htmlspecialchars($settings['site_name'] ?? 'N/A') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($settings['site_email'] ?? 'N/A') ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($settings['site_phone'] ?? 'N/A') ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($settings['site_address'] ?? 'N/A') ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($settings['site_description'] ?? 'N/A') ?></p>
                    <hr>
                    <p><strong>Social Links:</strong></p>
                    <ul>
                        <li>Facebook: <a href="<?= htmlspecialchars($settings['social_links']['facebook'] ?? '#') ?>"
                                target="_blank"><?= htmlspecialchars($settings['social_links']['facebook'] ?? 'Not set') ?></a>
                        </li>
                        <li>Twitter: <a href="<?= htmlspecialchars($settings['social_links']['twitter'] ?? '#') ?>"
                                target="_blank"><?= htmlspecialchars($settings['social_links']['twitter'] ?? 'Not set') ?></a>
                        </li>
                        <li>LinkedIn: <a href="<?= htmlspecialchars($settings['social_links']['linkedin'] ?? '#') ?>"
                                target="_blank"><?= htmlspecialchars($settings['social_links']['linkedin'] ?? 'Not set') ?></a>
                        </li>
                        <li>Instagram: <a href="<?= htmlspecialchars($settings['social_links']['instagram'] ?? '#') ?>"
                                target="_blank"><?= htmlspecialchars($settings['social_links']['instagram'] ?? 'Not set') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <script src="../main.js"></script>
    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');

            // Add active class to clicked button
            event.target.classList.add('active');

            // Update URL
            window.history.replaceState({}, '', `?tab=${tabName}`);
        }
    </script>
</body>

</html>
<h2>Scheduled Events</h2>
<div class="event-preview">
    <?php foreach ($data['events'] as $event): ?>
        <div class="preview-event">
            <?php if (!empty($event['image'])): ?>
                <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['title']) ?>">
            <?php endif; ?>
            <div>
                <strong><?= htmlspecialchars($event['title']) ?></strong>
                <span><?= htmlspecialchars($event['date']) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>
</aside>
</section>
</main>

<script src="../main.js"></script>
</body>

</html>