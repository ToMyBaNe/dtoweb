<?php
require_once '../config.php';

// Initialize database tables
initializeDB();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}

$pdo = getDB();
$action = isset($_GET['action']) ? $_GET['action'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 'announcements';

// Handle delete
if ($action == 'delete' && isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    
    if ($type == 'announcement') {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'news') {
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'event') {
        $stmt = $pdo->prepare("DELETE FROM calendar_events WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'system') {
        // Delete logo if exists
        $stmt = $pdo->prepare("SELECT logo FROM systems WHERE id = ?");
        $stmt->execute([$id]);
        $system = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($system && $system['logo']) {
            $logo_path = __DIR__ . '/../assets/uploads/systems/' . $system['logo'];
            if (file_exists($logo_path)) {
                unlink($logo_path);
            }
        }
        $stmt = $pdo->prepare("DELETE FROM systems WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    if ($type === 'event') {
        $redirect_page = 'calendar';
    } else {
        $redirect_page = $type;
    }
    header("Location: /admin/dashboard.php?page=$redirect_page");
    exit;
}

// Handle toggle active
if ($action == 'toggle' && isset($_GET['id']) && isset($_GET['type'])) {
    $id = intval($_GET['id']);
    $type = $_GET['type'];
    
    if ($type == 'announcement') {
        $stmt = $pdo->prepare("UPDATE announcements SET active = NOT active WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'news') {
        $stmt = $pdo->prepare("UPDATE news SET active = NOT active WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'event') {
        $stmt = $pdo->prepare("UPDATE calendar_events SET active = NOT active WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($type == 'system') {
        $stmt = $pdo->prepare("UPDATE systems SET active = NOT active WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    if ($type === 'event') {
        $redirect_page = 'calendar';
    } else {
        $redirect_page = $type;
    }
    header("Location: /admin/dashboard.php?page=$redirect_page");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DTO</title>
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
        
        .maroon-dark {
            background: linear-gradient(135deg, #4a0808 0%, #6b1212 100%);
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
        
        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(107, 18, 18, 0.03), transparent);
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .status-active {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #ef5350, #e53935);
            color: white;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        
        .btn-edit {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }
        
        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-toggle {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
        }
        
        .btn-toggle:hover {
            background: rgba(245, 158, 11, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-delete {
            color: #ef5350;
            background: rgba(239, 83, 80, 0.1);
        }
        
        .btn-delete:hover {
            background: rgba(239, 83, 80, 0.2);
            transform: translateY(-2px);
        }
        
        .new-btn {
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
        
        .new-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(107, 18, 18, 0.3);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-state-icon {
            color: rgba(107, 18, 18, 0.2);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="maroon-gradient text-white w-72 flex flex-col shadow-2xl">
            <div class="p-8 border-b border-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur">
                        <img src="/assets/misLogo.jpg" alt="DTO Logo" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">DTO Admin</h1>
                        <p class="text-xs text-white/60">Control Panel</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-6 py-8 space-y-2 overflow-y-auto">
                <p class="text-xs font-bold text-white/50 uppercase tracking-wider mb-4">Management</p>
                <a 
                    href="/admin/dashboard.php?page=announcements" 
                    class="sidebar-item <?php echo $page === 'announcements' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10"
                >
                    <i data-lucide="megaphone" class="w-5 h-5"></i>
                    <span class="font-medium">Announcements</span>
                </a>
                <a 
                    href="/admin/dashboard.php?page=news" 
                    class="sidebar-item <?php echo $page === 'news' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10"
                >
                    <i data-lucide="newspaper" class="w-5 h-5"></i>
                    <span class="font-medium">News</span>
                </a>
                <a 
                    href="/admin/dashboard.php?page=calendar" 
                    class="sidebar-item <?php echo $page === 'calendar' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10"
                >
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    <span class="font-medium">Calendar Events</span>
                </a>
                <a 
                    href="/admin/dashboard.php?page=systems" 
                    class="sidebar-item <?php echo $page === 'systems' ? 'active' : ''; ?> flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10"
                >
                    <i data-lucide="box" class="w-5 h-5"></i>
                    <span class="font-medium">Systems</span>
                </a>
            </nav>

            <div class="p-6 border-t border-white/10">
                <div class="bg-white/10 backdrop-blur rounded-xl p-4 mb-4">
                    <p class="text-xs text-white/60 mb-2">Logged in as</p>
                    <p class="font-semibold text-white truncate"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                </div>
                <a href="/admin/logout.php" class="w-full flex items-center justify-center gap-2 bg-red-500/20 hover:bg-red-500/30 text-red-200 px-4 py-3 rounded-xl font-semibold transition">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white border-b border-gray-200 px-8 py-6 flex justify-between items-center shadow-sm">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <?php if ($page === 'announcements'): ?>
                            <div class="p-2 bg-blue-100 rounded-lg"><i data-lucide="megaphone" class="w-6 h-6 text-blue-600"></i></div>Manage Announcements
                        <?php elseif ($page === 'news'): ?>
                            <div class="p-2 bg-purple-100 rounded-lg"><i data-lucide="newspaper" class="w-6 h-6 text-purple-600"></i></div>Manage News
                        <?php elseif ($page === 'calendar'): ?>
                            <div class="p-2 bg-orange-100 rounded-lg"><i data-lucide="calendar" class="w-6 h-6 text-orange-600"></i></div>Manage Calendar Events
                        <?php else: ?>
                            <div class="p-2 bg-green-100 rounded-lg"><i data-lucide="box" class="w-6 h-6 text-green-600"></i></div>Manage Systems
                        <?php endif; ?>
                    </h2>
                </div>
                <a 
                    href="/" 
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-semibold transition"
                >
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to Website
                </a>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto p-8">
                <?php if ($page === 'announcements'): ?>
                <div class="mb-8">
                    <a 
                        href="/admin/manage_announcements.php" 
                        class="new-btn"
                    >
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        New Announcement
                    </a>
                </div>

                <?php
                $stmt = $pdo->query("SELECT * FROM announcements ORDER BY date_created DESC");
                $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <?php if (count($announcements) > 0): ?>
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                            <tr>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Title</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Date</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Status</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($announcements as $ann): ?>
                            <tr class="table-row-hover transition">
                                <td class="px-8 py-4 font-semibold text-gray-900">
                                    <?php echo htmlspecialchars(substr($ann['title'], 0, 50)); ?>...
                                </td>
                                <td class="px-8 py-4 text-gray-600 text-sm">
                                    <?php echo date('F d, Y', strtotime($ann['date_created'])); ?>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="status-badge <?php echo $ann['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $ann['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 flex gap-2">
                                    <a 
                                        href="/admin/manage_announcements.php?id=<?php echo $ann['id']; ?>" 
                                        class="action-btn btn-edit"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        Edit
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=toggle&id=<?php echo $ann['id']; ?>&type=announcement" 
                                        class="action-btn btn-toggle"
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        <?php echo $ann['active'] ? 'Hide' : 'Show'; ?>
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=delete&id=<?php echo $ann['id']; ?>&type=announcement" 
                                        class="action-btn btn-delete"
                                        onclick="return confirm('Are you sure?');"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon text-6xl mb-4">
                            <i data-lucide="inbox" class="w-16 h-16 mx-auto"></i>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mb-2">No announcements yet.</p>
                        <p class="text-gray-600 mb-6">Create your first announcement to get started</p>
                        <a 
                            href="/admin/manage_announcements.php" 
                            class="new-btn"
                        >
                            <i data-lucide="plus" class="w-5 h-5"></i>
                            Create First Announcement
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <?php elseif ($page === 'news'): ?>
                <div class="mb-8">
                    <a 
                        href="/admin/manage_news.php" 
                        class="new-btn"
                    >
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        New News
                    </a>
                </div>

                <?php
                $stmt = $pdo->query("SELECT * FROM news ORDER BY date_published DESC");
                $news_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <?php if (count($news_items) > 0): ?>
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
                            <tr>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Title</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Date</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Status</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($news_items as $news): ?>
                            <tr class="table-row-hover transition">
                                <td class="px-8 py-4 font-semibold text-gray-900">
                                    <?php echo htmlspecialchars(substr($news['title'], 0, 50)); ?>...
                                </td>
                                <td class="px-8 py-4 text-gray-600 text-sm">
                                    <?php echo date('F d, Y', strtotime($news['date_published'])); ?>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="status-badge <?php echo $news['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $news['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 flex gap-2">
                                    <a 
                                        href="/admin/manage_news.php?id=<?php echo $news['id']; ?>" 
                                        class="action-btn btn-edit"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        Edit
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=toggle&id=<?php echo $news['id']; ?>&type=news" 
                                        class="action-btn btn-toggle"
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        <?php echo $news['active'] ? 'Hide' : 'Show'; ?>
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=delete&id=<?php echo $news['id']; ?>&type=news" 
                                        class="action-btn btn-delete"
                                        onclick="return confirm('Are you sure?');"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon text-6xl mb-4">
                            <i data-lucide="inbox" class="w-16 h-16 mx-auto"></i>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mb-2">No news yet.</p>
                        <p class="text-gray-600 mb-6">Create your first news article to get started</p>
                        <a 
                            href="/admin/manage_news.php" 
                            class="new-btn"
                        >
                            <i data-lucide="plus" class="w-5 h-5"></i>
                            Create First News
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <?php elseif ($page === 'calendar'): ?>
                <div class="mb-8">
                    <a 
                        href="/admin/manage_calendar.php" 
                        class="new-btn"
                    >
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        New Event
                    </a>
                </div>

                <?php
                $stmt = $pdo->query("SELECT * FROM calendar_events ORDER BY event_date DESC");
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <?php if (count($events) > 0): ?>
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-orange-50 to-orange-100 border-b border-orange-200">
                            <tr>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Title</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Date & Time</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Location</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Status</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($events as $evt): ?>
                            <tr class="table-row-hover transition">
                                <td class="px-8 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full" style="background-color: <?php echo htmlspecialchars($evt['color']); ?>;"></div>
                                        <?php echo htmlspecialchars(substr($evt['title'], 0, 40)); ?>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-gray-600 text-sm">
                                    <?php echo date('M d, Y', strtotime($evt['event_date'])); ?>
                                    <?php if ($evt['start_time']): ?>
                                        <br><span class="text-xs text-gray-500"><?php echo date('g:i A', strtotime($evt['start_time'])); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-4 text-gray-600 text-sm">
                                    <?php echo $evt['location'] ? htmlspecialchars($evt['location']) : '—'; ?>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="status-badge <?php echo $evt['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $evt['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 flex gap-2">
                                    <a 
                                        href="/admin/manage_calendar.php?id=<?php echo $evt['id']; ?>" 
                                        class="action-btn btn-edit"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=toggle&id=<?php echo $evt['id']; ?>&type=event" 
                                        class="action-btn btn-toggle"
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=delete&id=<?php echo $evt['id']; ?>&type=event" 
                                        class="action-btn btn-delete"
                                        onclick="return confirm('Are you sure?');"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon text-6xl mb-4">
                            <i data-lucide="inbox" class="w-16 h-16 mx-auto"></i>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mb-2">No events yet.</p>
                        <p class="text-gray-600 mb-6">Create your first calendar event to get started</p>
                        <a 
                            href="/admin/manage_calendar.php" 
                            class="new-btn"
                        >
                            <i data-lucide="plus" class="w-5 h-5"></i>
                            Create First Event
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <?php elseif ($page === 'systems'): ?>
                <div class="mb-8">
                    <a 
                        href="/admin/manage_systems.php" 
                        class="new-btn"
                    >
                        <i data-lucide="plus" class="w-5 h-5"></i>
                        New System
                    </a>
                </div>

                <?php
                $stmt = $pdo->query("SELECT * FROM systems ORDER BY display_order ASC, created_at DESC");
                $systems = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                    <?php if (count($systems) > 0): ?>
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                            <tr>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">System Name</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">URL</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Status</th>
                                <th class="px-8 py-4 text-left font-bold text-gray-900 text-sm uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($systems as $system): ?>
                            <tr class="table-row-hover transition">
                                <td class="px-8 py-4 font-semibold text-gray-900">
                                    <div class="flex items-center gap-3">
                                        <?php if ($system['logo']): ?>
                                            <img src="/assets/uploads/systems/<?php echo htmlspecialchars($system['logo']); ?>" alt="<?php echo htmlspecialchars($system['name']); ?>" class="h-10 w-10 object-cover rounded-lg">
                                        <?php else: ?>
                                            <div class="h-10 w-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                                <i data-lucide="box" class="w-6 h-6 text-gray-600"></i>
                                            </div>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($system['name']); ?>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-gray-600 text-sm">
                                    <a href="<?php echo htmlspecialchars($system['url']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium"><?php echo htmlspecialchars(parse_url($system['url'], PHP_URL_HOST) ?: $system['url']); ?></a>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="status-badge <?php echo $system['active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $system['active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 flex gap-2">
                                    <a 
                                        href="/admin/manage_systems.php?id=<?php echo $system['id']; ?>" 
                                        class="action-btn btn-edit"
                                    >
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        Edit
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=toggle&id=<?php echo $system['id']; ?>&type=system" 
                                        class="action-btn btn-toggle"
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        <?php echo $system['active'] ? 'Hide' : 'Show'; ?>
                                    </a>
                                    <a 
                                        href="/admin/dashboard.php?action=delete&id=<?php echo $system['id']; ?>&type=system" 
                                        class="action-btn btn-delete"
                                        onclick="return confirm('Are you sure? This will delete the system and its logo.');"
                                    >
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon text-6xl mb-4">
                            <i data-lucide="inbox" class="w-16 h-16 mx-auto"></i>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mb-2">No systems yet.</p>
                        <p class="text-gray-600 mb-6">Create your first system to get started</p>
                        <a 
                            href="/admin/manage_systems.php" 
                            class="new-btn"
                        >
                            <i data-lucide="plus" class="w-5 h-5"></i>
                            Create First System
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <?php endif; ?>
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
