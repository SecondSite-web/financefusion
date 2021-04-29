<?php
// Load the setup Once only

if (! isset($dashLoaded)) {
    $dashLoaded = true;

    // Load Database Settings
    require_once __DIR__ . '/dash-config.php';
    // Load all included php dependencies
    require_once ROOT_PATH . 'dash-settings.php';
}
