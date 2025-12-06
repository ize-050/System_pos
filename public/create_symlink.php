<?php
$target = $_SERVER['DOCUMENT_ROOT'] . '/../pos_system/storage/app/public';
$link = $_SERVER['DOCUMENT_ROOT'] . '/storage';

// ลบถ้ามีอยู่แล้ว
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
    } else {
        // ถ้าเป็นโฟลเดอร์ให้ลบ
        rmdir($link);
    }
}

// สร้าง symlink
if (symlink($target, $link)) {
    echo '✅ Symlink created successfully!<br>';
    echo "Target: $target<br>";
    echo "Link: $link";
} else {
    echo '❌ Failed to create symlink<br>';
    echo "Target: $target<br>";
    echo "Link: $link";
}
