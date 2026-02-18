<?php
$stmt = $pdo->prepare("SELECT * FROM systems WHERE active = 1 ORDER BY display_order ASC, created_at DESC");
$stmt->execute();
$systems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section" style="margin-top: 70px;">
    <div class="container">
        <h2 class="section-title">Systems</h2>
        <p class="section-subtitle">Explore the systems and platforms</p>

        <?php if (count($systems) > 0): ?>
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
