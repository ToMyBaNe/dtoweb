<?php
session_start();
header('Cache-Control: public, max-age=3600');
header('Pragma: cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

require_once 'config.php';
initializeDB();

$section = isset($_GET['section']) ? $_GET['section'] : 'home';
$pdo = getDB();
?>
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
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        :root {
            --primary: #6b1212;
            --primary-light: #8b2828;
            --primary-dark: #4a0808;
            --secondary: #f5f5f5;
            --accent: #d4af37;
            --text: #1a1a1a;
            --text-light: #666;
            --bg: #fafafa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Geist', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', serif;
            font-weight: 900;
            line-height: 1.2;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(107, 18, 18, 0.1);
            z-index: 1000;
            padding: 1rem 0;
        }

        nav .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--primary);
            font-weight: 800;
            font-size: 1.5rem;
        }

        .logo img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(107, 18, 18, 0.15);
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            position: relative;
            transition: color 0.3s;
        }

        nav a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        nav a.active {
            color: var(--primary);
        }

        nav a.active::after {
            width: 100%;
        }

        .btn {
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 24px rgba(107, 18, 18, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(107, 18, 18, 0.35);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero {
            margin-top: 70px;
            min-height: 100vh;
            background: linear-gradient(135deg, rgb(117, 10, 10) 0%, rgba(212, 175, 55, 0.05) 100%),
                        url('/assets/DTO-hero.jpg') center/cover;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.5) 100%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: left;
            max-width: 900px;
            padding: 0 2rem;
            animation: fadeInUp 1s ease-out;
        }

        .hero h1 {
            font-size: clamp(4rem, 12vw, 8rem);
            margin-bottom: 1rem;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.84);
            line-height: 1.1;
            word-spacing: 9999px;
        }

        .hero p {
            font-size: clamp(1rem, 3vw, 1.5rem);
            color: rgb(226 232 240 / var(--tw-bg-opacity, 1));           
            margin-bottom: 3rem;
            letter-spacing: 2px;
            font-weight: 300;
            text-align: left;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Section Styles */
        .section {
            padding: 6rem 2rem;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .section-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            margin-bottom: 0.5rem;
            color: var(--primary);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: var(--text-light);
            margin-bottom: 3rem;
        }

        /* Carousel */
        .carousel-section {
            background: linear-gradient(135deg, #fff 0%, #f9f9f9 100%);
        }

        .swiper {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(107, 18, 18, 0.15);
        }

        .swiper-slide {
            height: 450px;
        }

        .calendar-carousel .swiper-slide {
            height: auto;
            padding: 1rem;
        }

        .carousel-item {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
        }

        .carousel-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.3));
            z-index: 1;
        }

        .carousel-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 2rem;
            width: 100%;
        }

        .carousel-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .swiper-pagination-bullet {
            background: rgba(107, 18, 18, 0.3);
        }

        .swiper-pagination-bullet-active {
            background: var(--primary);
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            color: var(--primary);
            font-weight: 900;
            font-size: 20px;
        }

        /* Card Styles */
        .card-grid {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
        }

        .card-grid::-webkit-scrollbar {
            height: 8px;
        }

        .card-grid::-webkit-scrollbar-track {
            background: rgba(107, 18, 18, 0.05);
            border-radius: 10px;
        }

        .card-grid::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
            transition: background 0.3s;
        }

        .card-grid::-webkit-scrollbar-thumb:hover {
            background: var(--primary-light);
        }

        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(107, 18, 18, 0.08);
            animation: fadeInUp 0.6s ease-out;
            flex: 0 0 calc(20% - 1.6rem);
            min-width: 280px;
            scroll-snap-align: center;
            scroll-snap-stop: always;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px rgba(107, 18, 18, 0.12);
            border-color: rgba(107, 18, 18, 0.15);
        }

        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        .card-body {
            padding: 1.75rem;
        }

        .card-date {
            font-size: 0.85rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
            line-height: 1.3;
        }

        .card-text {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .card-link:hover {
            gap: 1rem;
            color: var(--primary-light);
        }

        /* Systems Grid */
        .systems-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .system-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(107, 18, 18, 0.1);
            transition: all 0.3s ease-out;
            animation: fadeInUp 0.8s ease-out;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .system-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(107, 18, 18, 0.2);
        }

        .system-card-image {
            width: 100%;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            overflow: hidden;
        }

        .system-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 1rem;
        }

        .system-card-body {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .system-title {
            font-size: 1.35rem;
            margin-bottom: 0.75rem;
            color: var(--primary);
            line-height: 1.3;
        }

        .system-description {
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .system-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            align-self: flex-start;
            padding: 0.5rem 0;
            margin-top: auto;
        }

        .system-link:hover {
            gap: 1rem;
            color: var(--primary-light);
        }

        /* Full Article Display */
        .article-full {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary);
            animation: slideInLeft 0.6s ease-out;
        }

        .article-full img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 1.5rem 0;
            max-height: 400px;
            object-fit: cover;
            width: 100%;
        }

        .article-full h2 {
            font-size: 2.5rem;
            margin: 1.5rem 0 1rem;
            color: var(--primary);
        }

        .article-meta {
            display: flex;
            gap: 2rem;
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .article-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .article-content {
            color: var(--text);
            line-height: 1.8;
            font-size: 1.05rem;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 4rem 2rem 2rem;
            margin-top: 6rem;
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            pointer-events: none;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
            position: relative;
            z-index: 2;
        }

        .footer-col h3 {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.3s;
            margin-bottom: 0.8rem;
        }

        .footer-col a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 2rem;
            text-align: center;
            position: relative;
            z-index: 2;
            color: rgba(255,255,255,0.8);
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 16px;
            border: 2px dashed rgba(107, 18, 18, 0.2);
        }

        .empty-state i {
            font-size: 3rem;
            color: rgba(107, 18, 18, 0.3);
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav ul {
                gap: 1rem;
            }

            nav a {
                font-size: 0.9rem;
            }

            .hero {
                margin-top: 60px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .section {
                padding: 3rem 1rem;
            }

            .article-full {
                padding: 1.5rem;
            }

            .card {
                flex: 0 0 calc(50% - 1rem);
                min-width: 240px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .logo {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .card {
                flex: 0 0 100%;
                min-width: 100%;
            }
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        .modal-header {
            position: relative;
            height: 300px;
            background-size: cover;
            background-position: center;
            border-radius: 20px 20px 0 0;
            display: flex;
            align-items: flex-end;
            padding: 2rem;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
            border-radius: 20px 20px 0 0;
        }

        .modal-header-content {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .modal-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--accent);
            color: var(--text);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            width: fit-content;
        }

        .modal-title {
            font-size: 2.2rem;
            color: white;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-info {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modal-info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .modal-description {
            font-size: 1rem;
            line-height: 1.8;
            color: var(--text);
            margin-bottom: 2rem;
        }

        .modal-footer {
            text-align: right;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .modal-close {
            display: flex;
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            z-index: 2001;
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: white;
            transform: rotate(90deg);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <a href="/" class="logo">
                <img src="/assets/misLogo.jpg" alt="DTO Logo">
                <span>DTO</span>
            </a>
            <ul>
                <li><a href="/" class="<?php echo $section == 'home' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/?section=announcements" class="<?php echo $section == 'announcements' ? 'active' : ''; ?>">Announcements</a></li>
                <li><a href="/?section=news" class="<?php echo $section == 'news' ? 'active' : ''; ?>">News</a></li>
                <li><a href="/?section=systems" class="<?php echo $section == 'systems' ? 'active' : ''; ?>">Systems</a></li>
            </ul>
        </div>
    </nav>

    <?php if ($section == 'home'): ?>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>DIGITAL TRANSFORMATION OFFICE</h1>
            <p>Foresight And Futures Thinking Office</p>
        </div>
    </section>

    <!-- Featured Carousel -->
    <section class="carousel-section section">
        <div class="container">
            <h2 class="section-title">Featured Updates</h2>
            <p class="section-subtitle">Stay informed with our latest announcements and news</p>

            <?php
            $stmt = $pdo->prepare("SELECT id, title, content, image, date_created as date, 'announcement' as type FROM announcements WHERE active = 1 ORDER BY date_created DESC LIMIT 5");
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $pdo->prepare("SELECT id, title, content, image, date_published as date, 'news' as type FROM news WHERE active = 1 ORDER BY date_published DESC LIMIT 5");
            $stmt->execute();
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $carousel_items = array_merge($announcements, $news);
            usort($carousel_items, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            $carousel_items = array_slice($carousel_items, 0, 8);
            ?>

            <?php if (count($carousel_items) > 0): ?>
            <div class="swiper carousel-container">
                <div class="swiper-wrapper">
                    <?php foreach ($carousel_items as $item): ?>
                    <div class="swiper-slide">
                        <div class="carousel-item" style="background-image: url('<?php echo isset($item['image']) && $item['image'] ? ($item['type'] == 'announcement' ? '/assets/uploads/announcements/' : '/assets/uploads/news/') . htmlspecialchars($item['image']) : '/assets/DTO-hero.jpg'; ?>')">
                            <div class="carousel-content">
                                <div class="carousel-tag">
                                    <i data-lucide="<?php echo $item['type'] == 'announcement' ? 'megaphone' : 'newspaper'; ?>" style="width: 14px; height: 14px;"></i>
                                    <?php echo ucfirst($item['type']); ?>
                                </div>
                                <h3 style="font-size: 1.8rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars(substr($item['title'], 0, 60)); ?></h3>
                                <p style="margin-bottom: 1rem; font-size: 0.95rem; line-height: 1.5;"><?php echo htmlspecialchars(substr($item['content'], 0, 100)); ?>...</p>
                                <a href="/?section=<?php echo $item['type']; ?>" class="btn btn-primary">
                                    <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i> Explore
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Announcements Section -->
    <section class="section" style="background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);">
        <div class="container">
            <h2 class="section-title">Latest Announcements</h2>
            <p class="section-subtitle">Important updates and announcements</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM announcements WHERE active = 1 ORDER BY date_created DESC LIMIT 3");
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($announcements) > 0):
            ?>
            <div class="card-grid">
                <?php foreach ($announcements as $index => $item): ?>
                <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <?php if (isset($item['image']) && $item['image']): ?>
                        <img src="/assets/uploads/announcements/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-image">
                    <?php else: ?>
                        <div class="card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="megaphone" style="width: 50px; height: 50px; color: rgba(255,255,255,0.5);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-date">
                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                            <?php echo date('M d, Y', strtotime($item['date_created'])); ?>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($item['content']); ?></p>
                        <button onclick="openModal('<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>', 'announcement')" class="card-link" style="background: none; border: none; cursor: pointer; padding: 0;">
                            Read more
                            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <h3>No announcements yet</h3>
                <p>Check back soon for updates</p>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="/?section=announcements" class="btn btn-primary">
                    View All Announcements
                </a>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Latest News</h2>
            <p class="section-subtitle">Stay updated with our news and stories</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM news WHERE active = 1 ORDER BY date_published DESC LIMIT 3");
            $stmt->execute();
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($news) > 0):
            ?>
            <div class="card-grid">
                <?php foreach ($news as $index => $item): ?>
                <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <?php if (isset($item['image']) && $item['image']): ?>
                        <img src="/assets/uploads/news/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-image">
                    <?php else: ?>
                        <div class="card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="newspaper" style="width: 50px; height: 50px; color: rgba(255,255,255,0.5);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-date">
                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                            <?php echo date('M d, Y', strtotime($item['date_published'])); ?>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($item['content']); ?></p>
                        <button onclick="openModal('<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>', 'news')" class="card-link" style="background: none; border: none; cursor: pointer; padding: 0;">
                            Read more
                            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <h3>No news yet</h3>
                <p>Check back soon for stories</p>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="/?section=news" class="btn btn-primary">
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- Calendar Events Carousel -->
    <section class="section carousel-section">
        <div class="container">
            <h2 class="section-title">Upcoming Events</h2>
            <p class="section-subtitle">Check our calendar for important dates and events</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM calendar_events WHERE active = 1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 8");
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if (count($events) > 0): ?>
            <div class="swiper calendar-carousel">
                <div class="swiper-wrapper">
                    <?php foreach ($events as $event): ?>
                    <div class="swiper-slide">
                        <div style="background: white; border-radius: 16px; padding: 2rem; height: 100%; display: flex; flex-direction: column; border-left: 5px solid <?php echo htmlspecialchars($event['color']); ?>; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 12px 32px rgba(107, 18, 18, 0.2)'; this.style.transform='translateY(-4px)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; this.style.transform='translateY(0)'">
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; color: var(--text-light); font-size: 0.9rem; font-weight: 500;">
                                <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                            </div>
                            <h3 style="font-size: 1.4rem; margin-bottom: 0.75rem; color: var(--primary); flex-grow: 1;"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <?php if ($event['start_time']): ?>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; color: var(--text-light); font-size: 0.9rem;">
                                <i data-lucide="clock" style="width: 16px; height: 16px;"></i>
                                <?php echo date('g:i A', strtotime($event['start_time'])); ?> - <?php echo date('g:i A', strtotime($event['end_time'])); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($event['location']): ?>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; color: var(--text-light); font-size: 0.9rem;">
                                <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                                <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($event['description']): ?>
                            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($event['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <?php else: ?>
            <div class="empty-state" style="padding: 2rem;">
                <i data-lucide="calendar" style="font-size: 2.5rem; color: rgba(107, 18, 18, 0.3); margin-bottom: 1rem;"></i>
                <h3>No upcoming events</h3>
                <p>Check back soon for scheduled events</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php elseif ($section == 'announcements'): ?>
    <section class="section" style="margin-top: 70px; background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);">
        <div class="container">
            <h2 class="section-title">All Announcements</h2>
            <p class="section-subtitle">Browse all announcements</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM announcements WHERE active = 1 ORDER BY date_created DESC");
            $stmt->execute();
            $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($announcements) > 0):
            ?>
            <div class="card-grid">
                <?php foreach ($announcements as $index => $item): ?>
                <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <?php if (isset($item['image']) && $item['image']): ?>
                        <img src="/assets/uploads/announcements/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-image">
                    <?php else: ?>
                        <div class="card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="megaphone" style="width: 50px; height: 50px; color: rgba(107, 18, 18, 0.3);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-date">
                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                            <?php echo date('M d, Y', strtotime($item['date_created'])); ?>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($item['content']); ?></p>
                        <button onclick="openModal('<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>', 'announcement')" class="card-link" style="background: none; border: none; cursor: pointer; padding: 0;">
                            Read more
                            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <h3>No announcements</h3>
                <p>No announcements available at this time</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php elseif ($section == 'news'): ?>
    <section class="section" style="margin-top: 70px;">
        <div class="container">
            <h2 class="section-title">All News</h2>
            <p class="section-subtitle">Browse all news and updates</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM news WHERE active = 1 ORDER BY date_published DESC");
            $stmt->execute();
            $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($news) > 0):
            ?>
            <div class="card-grid">
                <?php foreach ($news as $index => $item): ?>
                <div class="card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <?php if (isset($item['image']) && $item['image']): ?>
                        <img src="/assets/uploads/news/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="card-image">
                    <?php else: ?>
                        <div class="card-image" style="display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="newspaper" style="width: 50px; height: 50px; color: rgba(107, 18, 18, 0.3);"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-date">
                            <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                            <?php echo date('M d, Y', strtotime($item['date_published'])); ?>
                        </div>
                        <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-text"><?php echo htmlspecialchars($item['content']); ?></p>
                        <button onclick="openModal('<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>', 'news')" class="card-link" style="background: none; border: none; cursor: pointer; padding: 0;">
                            Read more
                            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <h3>No news</h3>
                <p>No news available at this time</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php elseif ($section == 'systems'): ?>
    <section class="section" style="margin-top: 70px;">
        <div class="container">
            <h2 class="section-title">Systems</h2>
            <p class="section-subtitle">Explore the systems and platforms</p>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM systems WHERE active = 1 ORDER BY display_order ASC, created_at DESC");
            $stmt->execute();
            $systems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($systems) > 0):
            ?>
            <div class="systems-grid">
                <?php foreach ($systems as $index => $system): ?>
                <div class="system-card" style="animation-delay: <?php echo $index * 0.1; ?>s">
                    <div class="system-card-image" style="background-color: <?php echo htmlspecialchars($system['icon_color']); ?>;">
                        <?php if (isset($system['logo']) && $system['logo']): ?>
                            <img src="/assets/uploads/systems/<?php echo htmlspecialchars($system['logo']); ?>" alt="<?php echo htmlspecialchars($system['name']); ?>" class="system-logo">
                        <?php else: ?>
                            <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                <i data-lucide="box" style="width: 60px; height: 60px; color: white; opacity: 0.7;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="system-card-body">
                        <h3 class="system-title"><?php echo htmlspecialchars($system['name']); ?></h3>
                        <?php if ($system['description']): ?>
                        <p class="system-description"><?php echo htmlspecialchars(substr($system['description'], 0, 150)); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($system['url']); ?>" target="_blank" class="system-link">
                            Access System
                            <i data-lucide="external-link" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i data-lucide="inbox"></i>
                <h3>No systems available</h3>
                <p>Check back soon for updates</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>About DTO</h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">Digital Transformation Office - Empowering innovation through seamless communication and cutting-edge solutions.</p>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/"><i data-lucide="home" style="width: 16px; height: 16px;"></i> Home</a></li>
                        <li><a href="/?section=announcements"><i data-lucide="megaphone" style="width: 16px; height: 16px;"></i> Announcements</a></li>
                        <li><a href="/?section=news"><i data-lucide="newspaper" style="width: 16px; height: 16px;"></i> News</a></li>
                        <li><a href="/?section=systems"><i data-lucide="box" style="width: 16px; height: 16px;"></i> Systems</a></li>
                        <?php if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']): ?>
                            <li><a href="/admin/"><i data-lucide="lock" style="width: 16px; height: 16px;"></i> Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact</h3>
                    <ul>
                        <li><a href="mailto:info@dto.com"><i data-lucide="mail" style="width: 16px; height: 16px;"></i> dto@basc.edu.ph</a></li>
                        <li><a href="tel:+1234567890"><i data-lucide="phone" style="width: 16px; height: 16px;"></i> (044) 931 8660 </a></li>
                        <li><i data-lucide="map-pin" style="width: 16px; height: 16px; display: inline; margin-right: 0.5rem;"></i>Pinaod, San Ildefonso Bulacan</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Follow Us</h3>
                    <ul>
                        <li><a href="https://www.facebook.com/profile.php?id=61587088024158"><i data-lucide="facebook" style="width: 16px; height: 16px;"></i> Facebook</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Digital Transformation Office. All rights reserved. | Crafted with care.</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }

            // Featured carousel
            const swiper = new Swiper('.carousel-container', {
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                effect: 'fade',
                fadeEffect: {
                    crossFade: true,
                },
            });

            // Calendar events carousel
            const calendarCarousel = new Swiper('.calendar-carousel', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: false,
                mousewheel: true,
                keyboard: {
                    enabled: true,
                    onlyInViewport: true,
                },
                pagination: {
                    el: '.calendar-carousel .swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.calendar-carousel .swiper-button-next',
                    prevEl: '.calendar-carousel .swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                    1440: {
                        slidesPerView: 4,
                        spaceBetween: 30,
                    },
                },
            });
        });

        // Modal Functions
        function openModal(itemJson, type) {
            try {
                const item = JSON.parse(itemJson);
                const modal = document.getElementById('contentModal');
                const modalTag = document.getElementById('modalTag');
                const modalTitle = document.getElementById('modalTitle');
                const modalDate = document.getElementById('modalDate');
                const modalTime = document.getElementById('modalTime');
                const modalLocation = document.getElementById('modalLocation');
                const modalDescription = document.getElementById('modalDescription');
                const modalImage = document.getElementById('modalImage');
                const timeContainer = document.getElementById('timeContainer');
                const locationContainer = document.getElementById('locationContainer');

                // Set header image
                if (item.image) {
                    const imageUrl = type === 'announcement' ? '/assets/uploads/announcements/' + item.image : '/assets/uploads/news/' + item.image;
                    modalImage.style.backgroundImage = 'url(' + imageUrl + ')';
                } else {
                    modalImage.style.backgroundImage = 'url(/assets/DTO-hero.jpg)';
                }

                // Set content
                modalTag.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                modalTag.innerHTML = (type === 'announcement' ? '<i data-lucide="megaphone" style="width: 16px; height: 16px;"></i>' : '<i data-lucide="newspaper" style="width: 16px; height: 16px;"></i>') + ' ' + modalTag.textContent;
                modalTitle.textContent = item.title;

                // Set date
                const dateField = type === 'announcement' ? 'date_created' : 'date_published';
                const dateValue = item[dateField] || item.date;
                modalDate.textContent = new Date(dateValue).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                // Set time if available
                if (item.start_time) {
                    const startTime = new Date('1970-01-01 ' + item.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    const endTime = new Date('1970-01-01 ' + item.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    modalTime.textContent = startTime + ' - ' + endTime;
                    timeContainer.style.display = 'flex';
                } else {
                    timeContainer.style.display = 'none';
                }

                // Set location if available
                if (item.location) {
                    modalLocation.textContent = item.location;
                    locationContainer.style.display = 'flex';
                } else {
                    locationContainer.style.display = 'none';
                }

                // Set description
                modalDescription.textContent = item.content || item.description || '';

                // Show modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                lucide.createIcons();
            } catch (e) {
                console.error('Error opening modal:', e);
            }
        }

        function closeModal() {
            const modal = document.getElementById('contentModal');
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('contentModal').addEventListener('click', function(e) {
            if (e.target.id === 'contentModal') {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

    <!-- Modal -->
    <div class="modal" id="contentModal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal();">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
            <div class="modal-header" id="modalImage">
                <div class="modal-header-content">
                    <div class="modal-tag" id="modalTag"></div>
                    <h2 class="modal-title" id="modalTitle"></h2>
                </div>
            </div>
            <div class="modal-body">
                <div class="modal-info">
                    <div class="modal-info-item">
                        <i data-lucide="calendar" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalDate"></span>
                    </div>
                    <div class="modal-info-item" id="timeContainer" style="display: none;">
                        <i data-lucide="clock" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalTime"></span>
                    </div>
                    <div class="modal-info-item" id="locationContainer" style="display: none;">
                        <i data-lucide="map-pin" style="width: 18px; height: 18px; color: var(--primary);"></i>
                        <span id="modalLocation"></span>
                    </div>
                </div>
                <div class="modal-description" id="modalDescription"></div>
            </div>
        </div>
    </div>
</body>
</html>