<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'DTC - News & Updates'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            50: '#FDF7F7',
                            100: '#F5E7E7',
                            200: '#ECCCCC',
                            300: '#E0B3B3',
                            400: '#D4999A',
                            500: '#B80000',
                            600: '#A00000',
                            700: '#800000',
                            800: '#660000',
                            900: '#4D0000',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .maroon-gradient {
            background: linear-gradient(135deg, #800000 0%, #4D0000 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <nav class="maroon-gradient text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-white rounded-full">
                        <span class="text-maroon-900 font-bold text-lg">DT</span>
                    </div>
                    <a href="/" class="text-xl font-bold">DTC Website</a>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="/" class="hover:text-maroon-100 transition">Home</a>
                    <a href="/?section=announcements" class="hover:text-maroon-100 transition">Announcements</a>
                    <a href="/?section=news" class="hover:text-maroon-100 transition">News</a>
                    <a href="/?section=systems" class="hover:text-maroon-100 transition">Systems</a>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <a href="/admin/dashboard.php" class="bg-white text-maroon-900 px-4 py-2 rounded hover:bg-gray-100 transition">Admin Panel</a>
                        <a href="/admin/logout.php" class="text-maroon-100 hover:text-white transition">Logout</a>
                    <?php else: ?>
                        <a href="/admin/" class="bg-white text-maroon-900 px-4 py-2 rounded hover:bg-gray-100 transition">Admin</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>
