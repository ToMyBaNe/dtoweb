<?php
require_once '../config.php';

// Initialize database tables
initializeDB();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: /admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DTO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <style>
        .maroon-gradient {
            background: linear-gradient(135deg, #800000 0%, #4D0000 100%);
        }
    </style>
</head>
<body class="maroon-gradient min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <div class="flex items-center justify-center">
                    <img src="/assets/misLogo.jpg" alt="DTO Logo" class="h-10 w-auto object-cover rounded-full" loading="lazy">
                </div>
                <h1 class="text-3xl font-bold text-maroon-900">DTO Admin</h1>
                <p class="text-gray-600 mt-2">Manage News & Announcements</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900" 
                        placeholder="Enter your username"
                        required
                    >
                
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-maroon-900" 
                        placeholder="Enter your password"
                        required
                    >
                   
                </div>

                <button 
                    type="submit" 
                    class="w-full maroon-gradient text-white font-bold py-3 rounded-lg hover:shadow-lg transition flex items-center justify-center gap-2"
                >
                    <i data-lucide="log-in" class="w-5 h-5"></i>Sign In
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-center text-gray-600 text-sm">
                    Back to <a href="/" class="text-maroon-900 font-semibold hover:underline">Public Website</a>
                </p>
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
