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

require_once 'includes/header.php';
?>

    <?php if ($section == 'home'): ?>
    <section class="hero">
        <div class="hero-content">
            <h1>DIGITAL TRANSFORMATION OFFICE</h1>
            <p>Foresight And Futures Thinking Office</p>
        </div>
    </section>

    <?php require_once 'sections/home.php'; ?>

    <?php elseif ($section == 'announcements'): ?>
        <?php require_once 'sections/announcements.php'; ?>

    <?php elseif ($section == 'news'): ?>
        <?php require_once 'sections/news.php'; ?>

    <?php elseif ($section == 'systems'): ?>
        <?php require_once 'sections/systems.php'; ?>

    <?php elseif ($section == 'developer'): ?>
        <?php require_once 'sections/developer.php'; ?>

    <?php endif; ?>

    <?php require_once 'includes/footer.php'; ?>

