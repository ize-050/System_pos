<?php
/**
 * PHP Information Page
 * สำหรับตรวจสอบการตั้งค่า PHP บน hosting
 * 
 * คำเตือน: ลบไฟล์นี้หลังจากตรวจสอบเสร็จแล้ว เพื่อความปลอดภัย
 */

// ตรวจสอบว่าเป็น production environment หรือไม่
$isProduction = isset($_SERVER['HTTP_HOST']) && !in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']);

if ($isProduction) {
    // แสดงข้อมูลที่จำเป็นเท่านั้นใน production
    echo "<h1>PHP Configuration Check</h1>";
    echo "<h2>Basic Information</h2>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
    echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
    echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
    
    echo "<h2>Laravel Requirements Check</h2>";
    
    // ตรวจสอบ PHP version
    $phpVersion = phpversion();
    $phpOk = version_compare($phpVersion, '8.0.0', '>=');
    echo "<p><strong>PHP >= 8.0:</strong> " . ($phpOk ? "✅ Yes ($phpVersion)" : "❌ No ($phpVersion)") . "</p>";
    
    // ตรวจสอบ extensions ที่จำเป็น
    $requiredExtensions = [
        'bcmath' => 'BCMath',
        'ctype' => 'Ctype',
        'fileinfo' => 'Fileinfo',
        'json' => 'JSON',
        'mbstring' => 'Mbstring',
        'openssl' => 'OpenSSL',
        'pdo' => 'PDO',
        'pdo_mysql' => 'PDO MySQL',
        'tokenizer' => 'Tokenizer',
        'xml' => 'XML',
        'curl' => 'cURL',
        'zip' => 'Zip'
    ];
    
    echo "<h3>Required Extensions:</h3>";
    foreach ($requiredExtensions as $ext => $name) {
        $loaded = extension_loaded($ext);
        echo "<p><strong>$name:</strong> " . ($loaded ? "✅ Loaded" : "❌ Missing") . "</p>";
    }
    
    // ตรวจสอบ file permissions
    echo "<h2>File Permissions Check</h2>";
    $paths = [
        '../storage' => 'Storage Directory',
        '../bootstrap/cache' => 'Bootstrap Cache'
    ];
    
    foreach ($paths as $path => $name) {
        if (file_exists($path)) {
            $writable = is_writable($path);
            echo "<p><strong>$name:</strong> " . ($writable ? "✅ Writable" : "❌ Not Writable") . "</p>";
        } else {
            echo "<p><strong>$name:</strong> ❌ Not Found</p>";
        }
    }
    
    // ตรวจสอบ .env file
    echo "<h2>Environment Check</h2>";
    $envExists = file_exists('../.env');
    echo "<p><strong>.env file:</strong> " . ($envExists ? "✅ Found" : "❌ Missing") . "</p>";
    
    if ($envExists) {
        $envContent = file_get_contents('../.env');
        $hasAppKey = strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=base64:') !== false;
        echo "<p><strong>APP_KEY:</strong> " . ($hasAppKey ? "✅ Set" : "❌ Missing") . "</p>";
        
        $hasDbConfig = strpos($envContent, 'DB_DATABASE=') !== false;
        echo "<p><strong>Database Config:</strong> " . ($hasDbConfig ? "✅ Set" : "❌ Missing") . "</p>";
    }
    
} else {
    // แสดงข้อมูลเต็มใน development
    echo "<h1>Development Environment - Full PHP Info</h1>";
    echo "<p style='color: red;'><strong>Warning:</strong> This is development mode. Full phpinfo() is displayed.</p>";
    phpinfo();
}

echo "<hr>";
echo "<p><strong>Generated:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p style='color: red;'><strong>Security Note:</strong> Please delete this file after checking!</p>";
?>