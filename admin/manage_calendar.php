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
$event = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM calendar_events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$event) {
        header('Location: /admin/dashboard.php?page=calendar');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#6b1212';
    $active = isset($_POST['active']) ? 1 : 0;

    if (empty($title) || empty($event_date)) {
        $error = 'Title and event date are required.';
    } else {
        if ($id) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE calendar_events SET title = ?, description = ?, event_date = ?, start_time = ?, end_time = ?, location = ?, color = ?, active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$title, $description, $event_date, $start_time, $end_time, $location, $color, $active, $id]);
            $success = 'Event updated successfully!';
        } else {
            // Create new
            $stmt = $pdo->prepare("INSERT INTO calendar_events (title, description, event_date, start_time, end_time, location, color, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $event_date, $start_time, $end_time, $location, $color, $active]);
            $success = 'Event created successfully!';
            $id = $pdo->lastInsertId();
            // Redirect after creation
            header('Location: /admin/dashboard.php?page=calendar');
            exit;
        }

        // Reload the event
        $stmt = $pdo->prepare("SELECT * FROM calendar_events WHERE id = ?");
        $stmt->execute([$id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Create'; ?> Calendar Event - DTO Admin</title>
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
            padding: 0.5rem;
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

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: 80%;
                max-width: 300px;
                height: 100vh;
                z-index: 1000;
                display: none;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                flex-direction: column;
            }

            .sidebar.active {
                display: flex;
                transform: translateX(0);
            }

            .sidebar-toggle {
                display: block;
            }

            .top-bar {
                flex-direction: row;
                gap: 0.5rem;
                align-items: center;
                padding: 1rem;
            }

            .top-bar h2 {
                font-size: 1.25rem;
                flex: 1;
            }

            .main-content {
                width: 100%;
            }

            .form-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                justify-content: center;
            }

            .flex-1.overflow-auto {
                padding: 1rem;
            }

            .max-w-4xl {
                max-width: 100%;
            }

            .bg-white.rounded-xl {
                padding: 1rem;
                border-radius: 0.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .top-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .top-bar h2 {
                font-size: 1rem;
            }

            .sidebar {
                width: 100%;
                max-width: 100%;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }

            .form-input,
            .form-textarea {
                font-size: 16px;
            }

            .btn-primary,
            .btn-secondary {
                padding: 0.65rem 1rem;
                font-size: 0.9rem;
            }

            input[type="date"],
            input[type="time"],
            input[type="color"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="sidebar maroon-gradient text-white w-72 flex flex-col shadow-2xl" id="adminSidebar">
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
                    href="/admin/dashboard.php?page=calendar" 
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <?php if ($id): ?>
                        <div class="p-2 bg-amber-100 rounded-lg"><i data-lucide="edit" class="w-6 h-6 text-amber-600"></i></div>Edit Calendar Event
                    <?php else: ?>
                        <div class="p-2 bg-amber-100 rounded-lg"><i data-lucide="calendar" class="w-6 h-6 text-amber-600"></i></div>New Calendar Event
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

                    <form method="POST" class="bg-white rounded-xl shadow-md p-6 md:p-8 space-y-6">
                        <div class="form-group">
                            <label for="title" class="form-label">
                                <i data-lucide="heading-1" class="w-4 h-4 inline-block mr-2"></i>Event Title
                            </label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="form-input" 
                                placeholder="Enter event title"
                                value="<?php echo $event ? htmlspecialchars($event['title']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="event_date" class="form-label">
                                    <i data-lucide="calendar" class="w-4 h-4 inline-block mr-2"></i>Event Date
                                </label>
                                <input 
                                    type="date" 
                                    id="event_date" 
                                    name="event_date" 
                                    class="form-input"
                                    value="<?php echo $event ? htmlspecialchars($event['event_date']) : ''; ?>"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="location" class="form-label">
                                    <i data-lucide="map-pin" class="w-4 h-4 inline-block mr-2"></i>Location
                                </label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    name="location" 
                                    class="form-input"
                                    placeholder="Event location"
                                    value="<?php echo $event ? htmlspecialchars($event['location']) : ''; ?>"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="start_time" class="form-label">
                                    <i data-lucide="clock" class="w-4 h-4 inline-block mr-2"></i>Start Time
                                </label>
                                <input 
                                    type="time" 
                                    id="start_time" 
                                    name="start_time" 
                                    class="form-input"
                                    value="<?php echo $event ? htmlspecialchars($event['start_time']) : ''; ?>"
                                >
                            </div>

                            <div class="form-group">
                                <label for="end_time" class="form-label">
                                    <i data-lucide="clock" class="w-4 h-4 inline-block mr-2"></i>End Time
                                </label>
                                <input 
                                    type="time" 
                                    id="end_time" 
                                    name="end_time" 
                                    class="form-input"
                                    value="<?php echo $event ? htmlspecialchars($event['end_time']) : ''; ?>"
                                >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i data-lucide="file-text" class="w-4 h-4 inline-block mr-2"></i>Description
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="8"
                                class="form-textarea" 
                                placeholder="Enter event description..."
                            ><?php echo $event ? htmlspecialchars($event['description']) : ''; ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="color" class="form-label">
                                    <i data-lucide="palette" class="w-4 h-4 inline-block mr-2"></i>Event Color
                                </label>
                                <div class="flex gap-3 items-center">
                                    <input 
                                        type="color" 
                                        id="color" 
                                        name="color" 
                                        class="h-12 w-20 border-2 border-gray-300 rounded-lg cursor-pointer"
                                        value="<?php echo $event ? htmlspecialchars($event['color']) : '#6b1212'; ?>"
                                    >
                                    <span id="color-value" class="text-gray-600 font-medium"><?php echo $event ? htmlspecialchars($event['color']) : '#6b1212'; ?></span>
                                </div>
                            </div>

                            <div class="form-group flex items-end pb-0.5">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="active" 
                                        class="w-5 h-5 rounded border-2 border-gray-300"
                                        <?php echo (!$event || $event['active']) ? 'checked' : ''; ?>
                                    >
                                    <span class="text-gray-700 font-semibold">Active</span>
                                </label>
                            </div>
                        </div>

                        <?php if ($event): ?>
                        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
                            <p class="text-sm text-gray-700"><strong>Created:</strong> <?php echo date('F d, Y g:i A', strtotime($event['created_at'])); ?></p>
                            <p class="text-sm text-gray-700"><strong>Last Updated:</strong> <?php echo date('F d, Y g:i A', strtotime($event['updated_at'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex form-buttons gap-4 pt-4">
                            <button 
                                type="submit" 
                                class="btn-primary"
                            >
                                <i data-lucide="<?php echo $id ? 'save' : 'plus-circle'; ?>" class="w-5 h-5"></i><?php echo $id ? 'Update Event' : 'Create Event'; ?>
                            </button>
                            <a 
                                href="/admin/dashboard.php?page=calendar" 
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
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('color-value').textContent = this.value;
        });

        // Mobile Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('adminSidebar');
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
