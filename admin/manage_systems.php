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
$system = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM systems WHERE id = ?");
    $stmt->execute([$id]);
    $system = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$system) {
        header('Location: /admin/dashboard.php?page=systems');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $url = isset($_POST['url']) ? trim($_POST['url']) : '';
    $icon_color = isset($_POST['icon_color']) ? trim($_POST['icon_color']) : '#6b1212';
    $display_order = isset($_POST['display_order']) ? intval($_POST['display_order']) : 0;
    $logo = ($system && isset($system['logo'])) ? $system['logo'] : '';

    if (empty($name) || empty($url)) {
        $error = 'System name and URL are required.';
    } else {
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
            $file = $_FILES['logo'];
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $filename = strtolower($file['name']);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (!in_array($ext, $allowed)) {
                $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp, svg';
            } elseif ($file['size'] > 20971520) { // 20MB limit
                $error = 'File size exceeds 20MB limit.';
            } else {
                // Create uploads directory if it doesn't exist
                $upload_dir = __DIR__ . '/../assets/uploads/systems';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $new_filename = uniqid('sys_') . '.' . $ext;
                $upload_path = $upload_dir . '/' . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Delete old logo if exists
                    if ($system && isset($system['logo']) && $system['logo']) {
                        $old_path = __DIR__ . '/../assets/uploads/systems/' . $system['logo'];
                        if (file_exists($old_path)) {
                            unlink($old_path);
                        }
                    }
                    $logo = $new_filename;
                } else {
                    $error = 'Failed to upload file.';
                }
            }
        }
        
        if (!$error) {
            if ($id) {
                // Update existing
                $stmt = $pdo->prepare("UPDATE systems SET name = ?, description = ?, url = ?, logo = ?, icon_color = ?, display_order = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$name, $description, $url, $logo, $icon_color, $display_order, $id]);
                $success = 'System updated successfully!';
            } else {
                // Create new
                $stmt = $pdo->prepare("INSERT INTO systems (name, description, url, logo, icon_color, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $url, $logo, $icon_color, $display_order]);
                $success = 'System created successfully!';
                $id = $pdo->lastInsertId();
            }

            // Reload the system
            $stmt = $pdo->prepare("SELECT * FROM systems WHERE id = ?");
            $stmt->execute([$id]);
            $system = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Create'; ?> System - DTO Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;900&display=swap" rel="stylesheet">
    <style>
        .maroon-gradient {
            background: linear-gradient(135deg, #800000 0%, #4D0000 100%);
        }
        body {
            font-family: 'Geist', sans-serif;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: #800000;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        @media (max-width: 768px) {
            .maroon-gradient {
                position: fixed;
                left: 0;
                top: 0;
                width: 80%;
                max-width: 300px;
                height: 100vh;
                z-index: 999;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .maroon-gradient.active {
                transform: translateX(0);
            }

            .w-64 {
                width: 80%;
                max-width: 300px;
            }

            .sidebar-toggle {
                display: block;
            }

            .flex.h-screen {
                flex-direction: column;
                height: auto;
            }

            .flex-1.flex.flex-col {
                width: 100%;
            }

            .bg-white.shadow {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .flex.justify-between.items-center {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .space-y-2 {
                gap: 0.5rem;
            }

            form {
                padding: 1rem;
            }

            .flex.gap-4 {
                flex-direction: column;
            }

            button[type="submit"],
            a.button {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .w-64 {
                width: 100%;
            }

            .text-2xl {
                font-size: 1.25rem;
            }

            form {
                padding: 0.75rem;
            }

            input[type="text"],
            input[type="url"],
            input[type="number"],
            input[type="color"],
            textarea {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="maroon-gradient text-white w-64 flex flex-col" id="adminSidebar">
            <div class="p-6 flex items-center space-x-3">
                <div class="flex items-center justify-center">
                    <img src="/assets/misLogo.jpg" alt="DTO Logo" class="h-10 w-auto object-cover rounded-full" loading="lazy">
                </div>
                <h1 class="text-xl font-bold">DTO Admin</h1>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <a href="/admin/dashboard.php?page=announcements" class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition">
                    <i data-lucide="megaphone" class="w-5 h-5"></i>Announcements
                </a>
                <a href="/admin/dashboard.php?page=news" class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition">
                    <i data-lucide="newspaper" class="w-5 h-5"></i>News
                </a>
                <a href="/admin/dashboard.php?page=calendar" class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition">
                    <i data-lucide="calendar" class="w-5 h-5"></i>Calendar Events
                </a>
                <a href="/admin/dashboard.php?page=systems" class="flex items-center gap-2 px-4 py-3 rounded-lg bg-maroon-800 hover:bg-maroon-800 transition">
                    <i data-lucide="box" class="w-5 h-5"></i>Systems
                </a>
            </nav>

            <div class="p-4 border-t border-maroon-600">
                <p class="text-sm text-maroon-100 mb-3">Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></p>
                <a href="/admin/logout.php" class="block w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition text-center">
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <div class="bg-white shadow p-6 flex justify-between items-center">
                <button class="sidebar-toggle md:hidden" id="sidebarToggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-maroon-900 flex items-center gap-2">
                        <i data-lucide="box" class="w-8 h-8"></i><?php echo $id ? 'Edit System' : 'Create System'; ?>
                    </h2>
                    <a href="/admin/dashboard.php?page=systems" class="text-maroon-900 hover:text-maroon-700 font-semibold transition">
                        ← Back to Systems
                    </a>
                </div>
            </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <div class="bg-white rounded-lg shadow max-w-2xl">
                    <div class="p-6">
                        <?php if ($error): ?>
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label for="name" class="block text-gray-700 font-semibold mb-2">System Name</label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="<?php echo $system ? htmlspecialchars($system['name']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    placeholder="Enter system name..."
                                    required
                                >
                            </div>

                            <div>
                                <label for="url" class="block text-gray-700 font-semibold mb-2">System URL</label>
                                <input 
                                    type="url" 
                                    id="url" 
                                    name="url" 
                                    value="<?php echo $system ? htmlspecialchars($system['url']) : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    placeholder="https://example.com"
                                    required
                                >
                            </div>

                            <div>
                                <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="5"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    placeholder="Enter system description..."
                                ><?php echo $system ? htmlspecialchars($system['description']) : ''; ?></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="icon_color" class="block text-gray-700 font-semibold mb-2">Icon Color</label>
                                    <input 
                                        type="color" 
                                        id="icon_color" 
                                        name="icon_color" 
                                        value="<?php echo $system ? htmlspecialchars($system['icon_color']) : '#6b1212'; ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900 h-12 cursor-pointer"
                                    >
                                </div>
                                <div>
                                    <label for="display_order" class="block text-gray-700 font-semibold mb-2">Display Order</label>
                                    <input 
                                        type="number" 
                                        id="display_order" 
                                        name="display_order" 
                                        value="<?php echo $system ? intval($system['display_order']) : 0; ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                        placeholder="0"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="logo" class="block text-gray-700 font-semibold mb-2">Logo/Icon</label>
                                <?php if ($system && isset($system['logo']) && $system['logo']): ?>
                                <div class="mb-4 flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <img src="/assets/uploads/systems/<?php echo htmlspecialchars($system['logo']); ?>" alt="Logo" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Current logo: <?php echo htmlspecialchars($system['logo']); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <input 
                                    type="file" 
                                    id="logo" 
                                    name="logo" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                                >
                                <p class="text-xs text-gray-500 mt-2">Max 20MB. Formats: JPG, PNG, GIF, WebP, SVG</p>
                            </div>

                            <?php if ($system): ?>
                            <div class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                                <p>Created: <?php echo date('F d, Y g:i A', strtotime($system['created_at'])); ?></p>
                                <p>Last updated: <?php echo date('F d, Y g:i A', strtotime($system['updated_at'])); ?></p>
                            </div>
                            <?php endif; ?>

                            <div class="flex gap-4">
                                <button 
                                    type="submit" 
                                    class="maroon-gradient text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition flex items-center gap-2"
                                >
                                    <i data-lucide="<?php echo $id ? 'save' : 'plus-circle'; ?>" class="w-5 h-5"></i><?php echo $id ? 'Update System' : 'Create System'; ?>
                                </button>
                                <a 
                                    href="/admin/dashboard.php?page=systems" 
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
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Mobile Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('adminSidebar');

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking on a nav item
            document.querySelectorAll('.maroon-gradient a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (!sidebarToggle.contains(e.target) && !sidebar.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>
