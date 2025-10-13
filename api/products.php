<?php
include '../includes/config.php';

// معالجة طلبات CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// بيانات افتراضية للطوارئ
$fallback_products = [
    [
        'id' => 1,
        'name' => 'BARRE DE TOIE DOBLO',
        'price' => '12000',
        'image' => 'images/porte-babage-doblo.jpg',
        'description' => 'قضبان السقف لدوبلو - مثالية لنقل الأمتعة والأغراض الكبيرة',
        'category' => 'interior',
        'created_at' => '2024-01-01 00:00:00'
    ],
    [
        'id' => 2, 
        'name' => 'ELARGISSUR DOBLO',
        'price' => '14000',
        'image' => 'images/elargisseur-doblo.jpeg',
        'description' => 'موسعات دوبلو - تزيد من مساحة التحميل بشكل ملحوظ',
        'category' => 'interior',
        'created_at' => '2024-01-01 00:00:00'
    ]
];

// الحصول على جميع المنتجات
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $category = $_GET['category'] ?? '';
        
        if ($category && $category !== 'all') {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
            $stmt->execute([$category]);
        } else {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        }
        
        $products = $stmt->fetchAll();
        
        // إذا لم توجد منتجات في قاعدة البيانات، استخدم البيانات الافتراضية
        if (empty($products)) {
            $products = $fallback_products;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'source' => empty($products) ? 'fallback' : 'database'
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // في حالة الخطأ، استخدم البيانات الافتراضية
        echo json_encode([
            'success' => true,
            'data' => $fallback_products,
            'source' => 'fallback (due to error)',
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('بيانات غير صالحة');
        }
        
        $name = $input['name'] ?? '';
        $price = $input['price'] ?? 0;
        $image = $input['image'] ?? '';
        $description = $input['description'] ?? '';
        $category = $input['category'] ?? 'interior';
        
        if (empty($name) || empty($price) || empty($image)) {
            throw new Exception('جميع الحقول مطلوبة');
        }
        
        $stmt = $pdo->prepare("INSERT INTO products (name, price, image, description, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $image, $description, $category]);
        
        echo json_encode([
            'success' => true,
            'message' => 'تم إضافة المنتج بنجاح',
            'id' => $pdo->lastInsertId()
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'فشل في إضافة المنتج: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// تحديث منتج
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('بيانات غير صالحة');
        }
        
        $id = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $price = $input['price'] ?? 0;
        $image = $input['image'] ?? '';
        $description = $input['description'] ?? '';
        $category = $input['category'] ?? 'interior';
        
        if (empty($id) || empty($name) || empty($price) || empty($image)) {
            throw new Exception('جميع الحقول مطلوبة');
        }
        
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, image=?, description=?, category=? WHERE id=?");
        $stmt->execute([$name, $price, $image, $description, $category, $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'تم تحديث المنتج بنجاح'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('المنتج غير موجود');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'فشل في تحديث المنتج: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// حذف منتج
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? 0;
        
        if (empty($id)) {
            throw new Exception('معرف المنتج مطلوب');
        }
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            throw new Exception('المنتج غير موجود');
        }
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'فشل في حذف المنتج: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

// إذا وصلنا هنا، الطلب غير معروف
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'طلب غير معروف'
], JSON_UNESCAPED_UNICODE);
?>