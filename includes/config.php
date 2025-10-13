<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// 🔧 إعدادات قاعدة البيانات - قم بتعديلها حسب بيانات استضافتك
$host = 'localhost';
$dbname = 'auto_bbs';                    // ⚠️ غير هذا لاسم قاعدة البيانات الحقيقي
$username = 'YOUR_DB_USERNAME';          // ⚠️ غير هذا لاسم المستخدم الحقيقي
$password = 'YOUR_DB_PASSWORD';          // ⚠️ غير هذا لكلمة المرور الحقيقية

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'فشل في الاتصال بقاعدة البيانات: ' . $e->getMessage(),
        'instructions' => '⚠️ الرجاء تعديل إعدادات قاعدة البيانات في ملف includes/config.php'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>