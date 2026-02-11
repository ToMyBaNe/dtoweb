<?php
require_once '../config.php';

// Initialize database tables
initializeDB();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}

$pdo = getDB();
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$error = '';
$success = '';
$announcement = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
    $stmt->execute([$id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$announcement) {
        header('Location: /admin/dashboard.php?page=announcements');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $image = ($announcement && isset($announcement['image'])) ? $announcement['image'] : '';

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $file = $_FILES['image'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = strtolower($file['name']);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (!in_array($ext, $allowed)) {
                $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp';
            } elseif ($file['size'] > 20242880) { // 20MB limit
                $error = 'File size exceeds 5MB limit.';
            } else {
                // Create uploads directory if it doesn't exist
                $upload_dir = __DIR__ . '/../assets/uploads/announcements';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $new_filename = uniqid('ann_') . '.' . $ext;
                $upload_path = $upload_dir . '/' . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($announcement && isset($announcement['image']) && $announcement['image']) {
                        $old_path = __DIR__ . '/../assets/uploads/announcements/' . $announcement['image'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                    $image = $new_filename;
                } else {
                    $error = 'Failed to upload file.';
                }
            }
        }
        
        if (!$error) {
            if ($id) {
                // Update existing
                $stmt = $pdo->prepare("UPDATE announcements SET title = ?, content = ?, image = ?, date_updated = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$title, $content, $image, $id]);
                $success = 'Announcement updated successfully!';
            } else {
                // Create new
                $stmt = $pdo->prepare("INSERT INTO announcements (title, content, image) VALUES (?, ?, ?)");
                $stmt->execute([$title, $content, $image]);
                $success = 'Announcement created successfully!';
                $id = $pdo->lastInsertId();
            }

            // Reload the announcement
            $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
            $stmt->execute([$id]);
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Create'; ?> Announcement - DTC Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        .maroon-gradient {
            background: linear-gradient(135deg, #800000 0%, #4D0000 100%);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="maroon-gradient text-white w-64 flex flex-col">
            <div class="p-6 flex items-center space-x-3">
                <div class="flex items-center justify-center">
                    <img src="/assets/misLogo.jpg" alt="DTO Logo" class="h-10 w-auto object-cover rounded-full" loading="lazy">
                </div>
                <h1 class="text-xl font-bold">DTC Admin</h1>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a 
                    href="/admin/dashboard.php?page=announcements" 
                    class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition"
                >
                    <i data-lucide="megaphone" class="w-5 h-5"></i>Announcements
                </a>
                <a 
                    href="/admin/dashboard.php?page=news" 
                    class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition"
                >
                    <i data-lucide="newspaper" class="w-5 h-5"></i>News
                </a>
            </nav>

            <div class="p-4 border-t border-maroon-600">
                <a href="/admin/logout.php" class="block w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-center">
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white shadow-md p-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-maroon-900 flex items-center gap-2">
                    <?php if ($id): ?>
                        <i data-lucide="edit" class="w-8 h-8"></i>Edit Announcement
                    <?php else: ?>
                        <i data-lucide="megaphone" class="w-8 h-8"></i>New Announcement
                    <?php endif; ?>
                </h2>
                <a 
                    href="/admin/dashboard.php?page=announcements" 
                    class="text-maroon-900 hover:text-maroon-700 font-semibold transition"
                >
                    ← Back
                </a>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-6">
                <div class="max-w-4xl">
                    <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        ✓ <?php echo $success; ?>
                        <?php if (!$id): ?>
                            <br><a href="/admin/manage_announcements.php?id=<?php echo $pdo->lastInsertId(); ?>" class="underline">Edit this announcement</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8 space-y-6">
                        <div>
                            <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                placeholder="Announcement title"
                                value="<?php echo $announcement ? htmlspecialchars($announcement['title']) : ''; ?>"
                                required
                            >
                        </div>

                        <div>
                            <label for="image" class="block text-gray-700 font-semibold mb-2">Featured Image</label>
                            <?php if ($announcement && $announcement['image']): ?>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <img src="/assets/uploads/announcements/<?php echo htmlspecialchars($announcement['image']); ?>" alt="Current image" class="w-48 h-auto rounded-lg shadow-md">
                            </div>
                            <?php endif; ?>
                            <input 
                                type="file" 
                                id="image" 
                                name="image" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                            <p class="text-xs text-gray-500 mt-2">Max 5MB. Formats: JPG, PNG, GIF, WebP</p>
                        </div>

                        <div>
                            <label for="content" class="block text-gray-700 font-semibold mb-2">Content</label>
                            <textarea 
                                id="content" 
                                name="content" 
                                rows="12"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                placeholder="Enter announcement content..."
                                required
                            ><?php echo $announcement ? htmlspecialchars($announcement['content']) : ''; ?></textarea>
                        </div>

                        <?php if ($announcement): ?>
                        <div class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <p>Created: <?php echo date('F d, Y g:i A', strtotime($announcement['date_created'])); ?></p>
                            <p>Last updated: <?php echo date('F d, Y g:i A', strtotime($announcement['date_updated'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex gap-4">
                            <button 
                                type="submit" 
                                class="maroon-gradient text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition flex items-center gap-2"
                            >
                                <i data-lucide="<?php echo $id ? 'save' : 'plus-circle'; ?>" class="w-5 h-5"></i><?php echo $id ? 'Update Announcement' : 'Create Announcement'; ?>
                            </button>
                            <a 
                                href="/admin/dashboard.php?page=announcements" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition"
                            >
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize Lucide icons after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
