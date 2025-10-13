<?php
echo "PHP Version: " . phpversion() . "\n";

if (extension_loaded('gd')) {
    echo "✅ GD Extension: LOADED\n";
    $gd_info = gd_info();
    echo "GD Version: " . ($gd_info['GD Version'] ?? 'Unknown') . "\n";
} else {
    echo "❌ GD Extension: NOT LOADED\n";
    echo "Loaded extensions:\n";
    foreach (get_loaded_extensions() as $ext) {
        echo " - $ext\n";
    }
}
?>