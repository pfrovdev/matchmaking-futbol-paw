<?php if (!empty($messages)): ?>
    <section class="alert alert-<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
        <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        <?php foreach ($messages as $msg): ?>
            <p><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endforeach; ?>
    </section>
<?php endif; ?>