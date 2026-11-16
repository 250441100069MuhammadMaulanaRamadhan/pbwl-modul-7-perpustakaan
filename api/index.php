<?php

/**
 * [MODUL 7 - SOAL 3a] Pintu masuk aplikasi Laravel di Vercel.
 * Filesystem Vercel baca-saja; hanya /tmp yang boleh ditulisi,
 * jadi lokasi tulis Laravel dialihkan ke /tmp.
 */

$dirTulis = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirTulis as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

$env = [
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'VIEW_COMPILED_PATH'   => '/tmp/storage/framework/views',
    'APP_CONFIG_CACHE'     => '/tmp/bootstrap/cache/config.php',
    'APP_EVENTS_CACHE'     => '/tmp/bootstrap/cache/events.php',
    'APP_PACKAGES_CACHE'   => '/tmp/bootstrap/cache/packages.php',
    'APP_ROUTES_CACHE'     => '/tmp/bootstrap/cache/routes.php',
    'APP_SERVICES_CACHE'   => '/tmp/bootstrap/cache/services.php',
    'LOG_CHANNEL'          => 'stderr',
];

foreach ($env as $kunci => $nilai) {
    putenv("{$kunci}={$nilai}");
    $_ENV[$kunci]    = $nilai;
    $_SERVER[$kunci] = $nilai;
}

require __DIR__.'/../public/index.php';