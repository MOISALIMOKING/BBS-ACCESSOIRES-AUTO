<?php
include '../includes/config.php';

// معالجة طلبات CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

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
        
        echo json_encode([
            'success' => true,
            'data' => $products
        ], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'فشل في جلب المنتجات: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
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
}
?>