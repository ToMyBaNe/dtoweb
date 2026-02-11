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
    <title><?php echo $id ? 'Edit' : 'Create'; ?> Calendar Event - DTC Admin</title>
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
                <a 
                    href="/admin/dashboard.php?page=calendar" 
                    class="flex items-center gap-2 px-4 py-3 rounded-lg hover:bg-maroon-800 transition"
                >
                    <i data-lucide="calendar" class="w-5 h-5"></i>Calendar Events
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
                        <i data-lucide="edit" class="w-8 h-8"></i>Edit Calendar Event
                    <?php else: ?>
                        <i data-lucide="calendar" class="w-8 h-8"></i>New Calendar Event
                    <?php endif; ?>
                </h2>
                <a 
                    href="/admin/dashboard.php?page=calendar" 
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
                    </div>
                    <?php endif; ?>

                    <form method="POST" class="bg-white rounded-lg shadow-md p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="title" class="block text-gray-700 font-semibold mb-2">Event Title</label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    placeholder="Event title"
                                    value="<?php echo $event ? htmlspecialchars($event['title']) : ''; ?>"
                                    required
                                >
                            </div>

                            <div>
                                <label for="event_date" class="block text-gray-700 font-semibold mb-2">Event Date</label>
                                <input 
                                    type="date" 
                                    id="event_date" 
                                    name="event_date" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    value="<?php echo $event ? htmlspecialchars($event['event_date']) : ''; ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_time" class="block text-gray-700 font-semibold mb-2">Start Time</label>
                                <input 
                                    type="time" 
                                    id="start_time" 
                                    name="start_time" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    value="<?php echo $event ? htmlspecialchars($event['start_time']) : ''; ?>"
                                >
                            </div>

                            <div>
                                <label for="end_time" class="block text-gray-700 font-semibold mb-2">End Time</label>
                                <input 
                                    type="time" 
                                    id="end_time" 
                                    name="end_time" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                    value="<?php echo $event ? htmlspecialchars($event['end_time']) : ''; ?>"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-gray-700 font-semibold mb-2">Location</label>
                            <input 
                                type="text" 
                                id="location" 
                                name="location" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                placeholder="Event location"
                                value="<?php echo $event ? htmlspecialchars($event['location']) : ''; ?>"
                            >
                        </div>

                        <div>
                            <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="8"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900"
                                placeholder="Event description..."
                            ><?php echo $event ? htmlspecialchars($event['description']) : ''; ?></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="color" class="block text-gray-700 font-semibold mb-2">Event Color</label>
                                <div class="flex gap-2 items-center">
                                    <input 
                                        type="color" 
                                        id="color" 
                                        name="color" 
                                        class="h-12 w-20 border border-gray-300 rounded-lg cursor-pointer"
                                        value="<?php echo $event ? htmlspecialchars($event['color']) : '#6b1212'; ?>"
                                    >
                                    <span id="color-value" class="text-gray-600"><?php echo $event ? htmlspecialchars($event['color']) : '#6b1212'; ?></span>
                                </div>
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="active" 
                                        class="w-5 h-5 rounded border-gray-300"
                                        <?php echo (!$event || $event['active']) ? 'checked' : ''; ?>
                                    >
                                    <span class="text-gray-700 font-semibold">Active</span>
                                </label>
                            </div>
                        </div>

                        <?php if ($event): ?>
                        <div class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg">
                            <p>Created: <?php echo date('F d, Y g:i A', strtotime($event['created_at'])); ?></p>
                            <p>Last updated: <?php echo date('F d, Y g:i A', strtotime($event['updated_at'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="flex gap-4">
                            <button 
                                type="submit" 
                                class="maroon-gradient text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition flex items-center gap-2"
                            >
                                <i data-lucide="<?php echo $id ? 'save' : 'plus-circle'; ?>" class="w-5 h-5"></i><?php echo $id ? 'Update Event' : 'Create Event'; ?>
                            </button>
                            <a 
                                href="/admin/dashboard.php?page=calendar" 
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
        // Color picker value display
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('color-value').textContent = this.value;
        });

        // Initialize Lucide icons after DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
