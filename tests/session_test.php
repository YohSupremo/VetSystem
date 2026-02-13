<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use Laravel session helper
session(['__test_value' => 'ok']);
$value = session('__test_value');

echo "SESSION_VALUE: $value\n";

$sessionsDir = __DIR__ . '/../storage/framework/sessions';
if (!is_dir($sessionsDir)) {
    echo "SESSIONS_DIR_MISSING: $sessionsDir\n";
    exit(1);
}
$files = array_values(array_filter(scandir($sessionsDir), function($f) use ($sessionsDir) {
    return $f !== '.' && $f !== '..' && is_file($sessionsDir . DIRECTORY_SEPARATOR . $f);
}));

echo 'SESSION_FILES_COUNT: ' . count($files) . PHP_EOL;
if (count($files)) {
    $latest = end($files);
    echo 'LATEST_SESSION_FILE: ' . $latest . PHP_EOL;
    echo 'LATEST_CONTENT:' . PHP_EOL;
    echo file_get_contents($sessionsDir . DIRECTORY_SEPARATOR . $latest) . PHP_EOL;
}
