<?php
// ---- กันคนอื่นเรียก (แก้ค่า SECRET ให้เป็นโทเคนของคุณเอง) ----
if (($_GET['token'] ?? '') !== 'YOUR_ONE_TIME_SECRET') {
  http_response_code(403);
  exit('forbidden');
}

ini_set('display_errors', '1');
error_reporting(E_ALL);
set_time_limit(120);

$projectBase = realpath(__DIR__ . '/../pos_system'); // ชี้ไปโฟลเดอร์โปรเจกต์
if (!$projectBase) { exit("Project base not found\n"); }

// Boot Laravel
require $projectBase . '/vendor/autoload.php';
$app = require $projectBase . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

$kernel = $app->make(ConsoleKernel::class);

// รายการคำสั่งที่จะรันต่อกัน
$commands = [
  'optimize:clear',
  'config:clear',
  'route:clear',
  'view:clear',
  'cache:clear',
  // 'key:generate --ansi',   // ต้องการก็เปิดบรรทัดนี้
  'config:cache',
];

header('Content-Type: text/plain; charset=utf-8');

foreach ($commands as $cmd) {
  echo ">>> artisan {$cmd}\n";
  try {
    $exitCode = $kernel->call($cmd);
    echo $kernel->output() ?: "(exit code: {$exitCode})\n";
  } catch (Throwable $e) {
    echo "ERROR running '{$cmd}': " . $e->getMessage() . "\n";
    // ถ้าพังกลางทาง ให้หยุดเพื่อจะได้เห็นสาเหตุชัด
    break;
  }
  echo "\n";
}

echo "✅ done\n";
