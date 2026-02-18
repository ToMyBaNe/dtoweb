<?php
// Home Section - Featured Carousel
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

<!-- Featured Carousel -->
<section class="carousel-section section">
    <div class="container">
        <h2 class="section-title">Featured Updates</h2>
        <p class="section-subtitle">Stay informed with our latest announcements and news</p>

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

<?php
// Announcements Section
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE active = 1 ORDER BY date_created DESC LIMIT 3");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Announcements Section -->
<section class="section" style="background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);">
    <div class="container">
        <h2 class="section-title">Latest Announcements</h2>
        <p class="section-subtitle">Important updates and announcements</p>

        <?php if (count($announcements) > 0): ?>
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

<?php
// News Section
$stmt = $pdo->prepare("SELECT * FROM news WHERE active = 1 ORDER BY date_published DESC LIMIT 3");
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- News Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Latest News</h2>
        <p class="section-subtitle">Stay updated with our news and stories</p>

        <?php if (count($news) > 0): ?>
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

<?php
// Calendar Events Section
$stmt = $pdo->prepare("SELECT * FROM calendar_events WHERE active = 1 AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 8");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Calendar Events Carousel -->
<section class="section carousel-section">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        <p class="section-subtitle">Check our calendar for important dates and events</p>

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
