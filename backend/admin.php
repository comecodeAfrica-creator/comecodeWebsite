<?php
require_once __DIR__ . '/config.php';
$data = getCommunityData();
$errors = [];
$success = null;

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_gallery'])) {
        $caption = sanitizeText($_POST['gallery_caption'] ?? '');
        $imageUrl = handleUploadImage($_FILES['gallery_image'] ?? [], $errors);

        if ($imageUrl && empty($errors)) {
            $data['gallery'][] = [
                'image' => $imageUrl,
                'caption' => $caption
            ];
            if (saveCommunityData($data)) {
                $success = 'Gallery image uploaded successfully.';
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
            $data['events'][] = [
                'title' => $title,
                'date' => $date,
                'description' => $description,
                'image' => $imageUrl
            ];
            if (saveCommunityData($data)) {
                $success = 'Event created and uploaded successfully.';
            } else {
                $errors[] = 'Unable to save event data.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComeCode Admin | Community Uploads</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500&display=swap"
        rel="stylesheet">
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
                    <h1>Community Management</h1>
                    <p>Upload gallery images and publish community events with one simple interface.</p>
                </div>
                <div class="dashboard-actions">
                    <a href="community.php"><button class="btn-purple">Preview Community Page</button></a>
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

        <section class="admin-grid" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
            <div class="admin-column admin-form-column">
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

            <aside class="admin-column admin-preview-column">
                <div class="admin-card preview-card">
                    <h2>Current Community Gallery</h2>
                    <div class="gallery-preview">
                        <?php foreach ($data['gallery'] as $item): ?>
                            <div class="preview-item">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Gallery preview">
                                <p><?= htmlspecialchars($item['caption'] ?? 'No caption') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="admin-card preview-card">
                    <h2>Scheduled Events</h2>
                    <div class="event-preview">
                        <?php foreach ($data['events'] as $event): ?>
                            <div class="preview-event">
                                <?php if (!empty($event['image'])): ?>
                                    <img src="<?= htmlspecialchars($event['image']) ?>"
                                        alt="<?= htmlspecialchars($event['title']) ?>">
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