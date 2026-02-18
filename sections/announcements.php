<?php
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE active = 1 ORDER BY date_created DESC");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section" style="margin-top: 70px; background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);">
    <div class="container">
        <h2 class="section-title">All Announcements</h2>
        <p class="section-subtitle">Browse all announcements</p>

        <?php if (count($announcements) > 0): ?>
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
