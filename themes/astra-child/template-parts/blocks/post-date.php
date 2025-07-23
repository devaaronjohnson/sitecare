<?php
$published = get_the_date('M j, Y');
$updated = get_the_modified_date('M j, Y');

if ($published !== $updated): ?>
    <p class="post-dates">
        Updated: <?php echo esc_html($updated); ?> | Published: <?php echo esc_html($published); ?>
    </p>
<?php else: ?>
    <p class="post-dates">
        Published: <?php echo esc_html($published); ?>
    </p>
<?php endif; ?>