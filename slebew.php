<?php
// FORCE DELETE .htaccess even if locked

$target = __DIR__ . "/.htaccess";

if (file_exists($target)) {
    // Remove immutable flags if exist
    @shell_exec("chattr -i " . escapeshellarg($target));

    // Try change permissions
    @chmod($target, 0777);

    // Try delete
    if (@unlink($target)) {
        echo "SUCCESS: .htaccess deleted.";
    } else {
        echo "FAILED: Cannot delete .htaccess.";
    }
} else {
    echo "File .htaccess not found.";
}
?>
