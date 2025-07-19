<?php
defined('_JEXEC') or die;

echo '<h2 style="color: green;">✅ Admin feeds view is rendering!</h2>';

if (!empty($this->items)) {
    echo '<ul>';
    foreach ($this->items as $item) {
        echo '<li><strong>' . htmlspecialchars($item->title) . '</strong> — ' . htmlspecialchars($item->url) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">⚠️ No feed items available.</p>';
}
