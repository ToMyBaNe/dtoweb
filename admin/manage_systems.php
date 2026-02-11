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
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Geist', sans-serif;
        }

        .maroon-gradient {
            background: linear-gradient(135deg, #6b1212 0%, #8b2828 100%);
        }

        .sidebar-item {
            position: relative;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #d4af37;
            border-radius: 0 4px 4px 0;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-item.active::before {
            opacity: 1;
        }

        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: #6b1212;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 0.95rem;
        }

        .form-input, .form-textarea {
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s;
            font-family: 'Geist', sans-serif;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #6b1212;
            box-shadow: 0 0 0 3px rgba(107, 18, 18, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6b1212, #8b2828);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(107, 18, 18, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #1a1a1a;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fee2e2;
            border: 2px solid #fecaca;
            color: #991b1b;
        }

        .alert-success {
            background: #dcfce7;
            border: 2px solid #bbf7d0;
            color: #166534;
        }

        .image-preview {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(107, 18, 18, 0.15);
            margin-bottom: 1rem;
            max-width: 300px;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: 80%;
                height: 100vh;
                z-index: 1000;
                display: none;
            }

            .sidebar.active {
                display: flex;
            }

            .sidebar-toggle {
                display: block;
            }

            .top-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .form-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="sidebar maroon-gradient text-white w-72 flex flex-col shadow-2xl">
            <div class="p-8 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                        <img src="/assets/misLogo.jpg" alt="DTO Logo" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">DTO Admin</h1>
                        <p class="text-xs text-white/60">Management</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
                <p class="text-xs font-bold text-white/50 uppercase tracking-wider mb-4">Back</p>
                <a 
                    href="/admin/dashboard.php?page=systems" 
                    class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10"
                >
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </nav>

            <div class="p-6 border-t border-white/10">
                <a href="/admin/logout.php" class="w-full flex items-center justify-center gap-2 bg-red-500/20 hover:bg-red-500/30 text-red-200 px-4 py-3 rounded-xl font-semibold transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="top-bar bg-white border-b border-gray-200 px-4 md:px-8 py-4 md:py-6 flex justify-between items-center shadow-sm">
                <button class="sidebar-toggle md:hidden" id="sidebarToggle">
                    <i data-lucide="menu" style="width: 24px; height: 24px;"></i>
                </button>
                <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <?php if ($id): ?>
                        <div class="p-2 bg-emerald-100 rounded-lg"><i data-lucide="edit" class="w-6 h-6 text-emerald-600"></i></div>Edit System
                    <?php else: ?>
                        <div class="p-2 bg-emerald-100 rounded-lg"><i data-lucide="box" class="w-6 h-6 text-emerald-600"></i></div>New System
                    <?php endif; ?>
                </h2>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-4 md:p-8">
                <div class="max-w-4xl mx-auto">
                    <?php if ($error): ?>
                    <div class="alert alert-error">
                        <div class="flex items-center gap-2">
                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span><?php echo $success; ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md p-6 md:p-8 space-y-6">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <i data-lucide="box" class="w-4 h-4 inline-block mr-2"></i>System Name
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="form-input" 
                                placeholder="Enter system name..."
                                value="<?php echo $system ? htmlspecialchars($system['name']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="url" class="form-label">
                                <i data-lucide="link" class="w-4 h-4 inline-block mr-2"></i>System URL
                            </label>
                            <input 
                                type="url" 
                                id="url" 
                                name="url" 
                                class="form-input"
                                placeholder="https://example.com"
                                value="<?php echo $system ? htmlspecialchars($system['url']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i>Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="6"
                                class="form-textarea" 
                                placeholder="Enter system description..."
                            ><?php echo $system ? htmlspecialchars($system['description']) : ''; ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="icon_color" class="form-label">
                                    <i data-lucide="palette" class="w-4 h-4 inline-block mr-2"></i>Icon Color
                                </label>
                                <div class="flex gap-3 items-center">
                                    <input 
                                        type="color" 
                                        id="icon_color" 
                                        name="icon_color" 
                                        class="h-12 w-20 border-2 border-gray-300 rounded-lg cursor-pointer"
                                        value="<?php echo $system ? htmlspecialchars($system['icon_color']) : '#6b1212'; ?>"
                                    >
                                    <span id="color-value" class="text-gray-600 font-medium"><?php echo $system ? htmlspecialchars($system['icon_color']) : '#6b1212'; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="display_order" class="form-label">
                                    <i data-lucide="sort" class="w-4 h-4 inline-block mr-2"></i>Display Order
                                </label>
                                <input 
                                    type="number" 
                                    id="display_order" 
                                    name="display_order" 
                                    class="form-input"
                                    placeholder="0"
                                    value="<?php echo $system ? intval($system['display_order']) : 0; ?>"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="logo" class="form-label">
                                <i data-lucide="image" class="w-4 h-4 inline-block mr-2"></i>Logo/Icon
                            </label>
                            <?php if ($system && isset($system['logo']) && $system['logo']): ?>
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-3 font-medium">Current Logo:</p>
                                <img src="/assets/uploads/systems/<?php echo htmlspecialchars($system['logo']); ?>" alt="Logo" class="image-preview">
                            </div>
                            <?php endif; ?>
                            <input 
                                type="file" 
                                id="logo" 
                                name="logo" 
                                class="form-input"
                                accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml"
                            >
                            <p class="text-xs text-gray-500 mt-2">Max 20MB. Formats: JPG, PNG, GIF, WebP, SVG</p>
                        </div>

                        <?php if ($system): ?>
                        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
                            <p class="text-sm text-gray-700"><strong>Created:</strong> <?php echo date('F d, Y g:i A', strtotime($system['created_at'])); ?></p>
                            <p class="text-sm text-gray-700"><strong>Last Updated:</strong> <?php echo date('F d, Y g:i A', strtotime($system['updated_at'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex form-buttons gap-4 pt-4">
                            <button 
                                type="submit" 
                                class="btn-primary"
                            >
                                <i data-lucide="<?php echo $id ? 'save' : 'plus-circle'; ?>" class="w-5 h-5"></i><?php echo $id ? 'Update System' : 'Create System'; ?>
                            </button>
                            <a 
                                href="/admin/dashboard.php?page=systems" 
                                class="btn-secondary"
                            >
                                <i data-lucide="x" class="w-5 h-5"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });

        // Color picker value display
        document.getElementById('icon_color').addEventListener('input', function() {
            document.getElementById('color-value').textContent = this.value;
        });

        // Mobile Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking on a nav item
            document.querySelectorAll('.sidebar a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('active');
                    }
                });
            });

            // Close sidebar when clicking outside on mobile
            if (mainContent) {
                mainContent.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('active');
                    }
                });
            }
        }
    </script>
</body>
</html>
