<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTO - Digital Transformation Office</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/misLogo.jpg" alt="DTO Logo">
                <span>DTO</span>
            </a>
            <button class="nav-menu-toggle" id="navMenuToggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="/" class="<?php echo $section == 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/?section=announcements" class="<?php echo $section == 'announcements' ? 'active' : ''; ?>">Announcements</a></li>
                <li><a href="/?section=news" class="<?php echo $section == 'news' ? 'active' : ''; ?>">News</a></li>
                <li><a href="/?section=systems" class="<?php echo $section == 'systems' ? 'active' : ''; ?>">Systems</a></li>
            </ul>
        </div>
    </nav>
