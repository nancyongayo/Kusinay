<?php
/**
 * MarketVendorController
 * 
 * Handles Process 20: Selling Goods (Market Vendor Management)
 * - Vendors manage their products
 * - View and manage orders from mothers/parents
 * - Track sales and inventory
 */

require_once __DIR__ . '/../models/MarketVendorModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

class MarketVendorController {
    private PDO $db;
    private MarketVendorModel $vendorModel;
    private OrderModel $orderModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->vendorModel = new MarketVendorModel($db);
        $this->orderModel = new OrderModel($db);
    }

    // ========================================================================
    // DASHBOARD
    // ========================================================================

    /**
     * Show vendor dashboard with statistics
     */
    public function showDashboard(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
            header('Location: index.php?action=login');
            exit;
        }

        $vendorId = $_SESSION['user_id'];
        $userName = $_SESSION['first_name'] ?? 'Vendor';

        // Get dashboard stats
        $stats = $this->vendorModel->getVendorDashboardStats($vendorId);

        // Get recent orders (from shopping cart orders)
        $recentOrders = $this->getVendorOrders($vendorId, 5);

        include __DIR__ . '/../views/market_vendor/dashboard.php';
    }

    // ========================================================================
    // PRODUCTS MANAGEMENT
    // ========================================================================

    /**
     * Show products list
     */
    public function showProducts(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
            header('Location: index.php?action=login');
            exit;
        }

        $vendorId = $_SESSION['user_id'];
        
        // Get search and filter parameters
        $search = $_GET['search'] ?? null;
        $category = $_GET['category'] ?? null;
        
        // Get products with optional filters
        $products = $this->vendorModel->getProductsByVendor($vendorId, $search, $category);

        include __DIR__ . '/../views/market_vendor/products.php';
    }

    /**
     * Show add/edit product form
     */
    public function showProductForm(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
            header('Location: index.php?action=login');
            exit;
        }

        $productId = $_GET['id'] ?? null;
        $product = null;

        if ($productId) {
            $product = $this->vendorModel->getProductById((int)$productId);
            
            // Verify ownership
            if (!$product || $product['vendor_user_id'] != $_SESSION['user_id']) {
                $_SESSION['flash'] = 'Product not found or unauthorized.';
                header('Location: index.php?action=vendorProducts');
                exit;
            }
        }

        include __DIR__ . '/../views/market_vendor/product_form.php';
    }

    /**
     * Save product (create or update)
     */
    public function saveProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=vendorProducts');
            exit;
        }

        $productId = $_POST['product_id'] ?? null;
        $vendorId = $_SESSION['user_id'];

        // Handle image upload - camera capture (base64) or file upload
        $imagePath = null;
        
        // Check if camera photo was captured (base64 data)
        if (!empty($_POST['product_image_data'])) {
            $imagePath = $this->saveBase64Image($_POST['product_image_data']);
        }
        // Fallback to traditional file upload
        elseif (!empty($_FILES['product_image']['name'])) {
            $imagePath = $this->uploadProductImage($_FILES['product_image']);
        } 
        // Keep existing image if editing
        elseif ($productId && !empty($_POST['existing_image'])) {
            $imagePath = $_POST['existing_image'];
        }

        $data = [
            'vendor_user_id'   => $vendorId,
            'product_name'     => $_POST['product_name'],
            'category'         => $_POST['category'],
            'description'      => $_POST['description'] ?? null,
            'unit'             => $_POST['unit'],
            'price_per_unit'   => (float)$_POST['price_per_unit'],
            'stock_quantity'   => (float)$_POST['stock_quantity'],
            'nutritional_info' => null, // Vendors don't provide nutritional info
            'product_image'    => $imagePath,
            'is_available'     => isset($_POST['is_available']) ? 1 : 0,
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
        ];

        try {
            if ($productId) {
                // Update existing product
                $product = $this->vendorModel->getProductById((int)$productId);
                if ($product && $product['vendor_user_id'] == $vendorId) {
                    $this->vendorModel->updateProduct((int)$productId, $data);
                    $_SESSION['flash'] = 'Product updated successfully!';
                } else {
                    $_SESSION['flash'] = 'Unauthorized or product not found.';
                }
            } else {
                // Create new product
                $newId = $this->vendorModel->createProduct($data);
                $_SESSION['flash'] = 'Product added successfully!';
            }
        } catch (Exception $e) {
            $_SESSION['flash'] = 'Error saving product: ' . $e->getMessage();
        }

        header('Location: index.php?action=vendorProducts');
        exit;
    }

    /**
     * Delete product
     */
    public function deleteProduct(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            header('Location: index.php?action=vendorProducts');
            exit;
        }

        $productId = (int)$_GET['id'];
        $product = $this->vendorModel->getProductById($productId);

        // Verify ownership
        if ($product && $product['vendor_user_id'] == $_SESSION['user_id']) {
            $this->vendorModel->deleteProduct($productId);
            $_SESSION['flash'] = 'Product deleted successfully.';
        } else {
            $_SESSION['flash'] = 'Unauthorized or product not found.';
        }

        header('Location: index.php?action=vendorProducts');
        exit;
    }

    /**
     * Toggle product availability
     */
    public function toggleProductAvailability(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $productId = (int)$_GET['id'];
        $product = $this->vendorModel->getProductById($productId);

        // Verify ownership
        if ($product && $product['vendor_user_id'] == $_SESSION['user_id']) {
            $this->vendorModel->toggleAvailability($productId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        }
        exit;
    }

    // ========================================================================
    // ORDERS MANAGEMENT
    // ========================================================================

    /**
     * Show orders for vendor
     */
    public function showOrders(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
            header('Location: index.php?action=login');
            exit;
        }

        $vendorId = $_SESSION['user_id'];
        $orders = $this->getVendorOrders($vendorId);

        include __DIR__ . '/../views/market_vendor/orders.php';
    }

    /**
     * Show order details
     */
    public function showOrderDetail(): void {
        if (!isset($_SESSION['user_id']) || empty($_GET['id'])) {
            header('Location: index.php?action=vendorOrders');
            exit;
        }

        $orderId = (int)$_GET['id'];
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order) {
            $_SESSION['flash'] = 'Order not found.';
            header('Location: index.php?action=vendorOrders');
            exit;
        }

        // Get customer details
        $stmt = $this->db->prepare("
            SELECT first_name, last_name, email 
            FROM users 
            WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $order['user_id']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Merge customer details into order array
        if ($customer) {
            $order['first_name'] = $customer['first_name'];
            $order['last_name'] = $customer['last_name'];
            $order['email'] = $customer['email'];
        }

        include __DIR__ . '/../views/market_vendor/order_detail.php';
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $orderId = (int)$_POST['order_id'];
        $status = $_POST['status'];

        try {
            $this->orderModel->updateOrderStatus($orderId, $status);
            echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    // ========================================================================
    // SALES REPORTS
    // ========================================================================

    /**
     * Show sales reports
     */
    public function showSalesReports(): void {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Market Vendor') {
            header('Location: index.php?action=login');
            exit;
        }

        $vendorId = $_SESSION['user_id'];
        
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01'); // First day of month
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');     // Today

        $salesStats = $this->vendorModel->getVendorSalesStats($vendorId, $dateFrom, $dateTo);
        $topProducts = $this->vendorModel->getTopSellingProducts($vendorId, 10);
        $salesHistory = $this->vendorModel->getSalesByVendor($vendorId, [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        include __DIR__ . '/../views/market_vendor/sales_reports.php';
    }

    // ========================================================================
    // HELPER METHODS
    // ========================================================================

    /**
     * Upload product image
     */
    private function uploadProductImage(array $file): ?string {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP allowed.');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }

        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'product_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'uploads/products/' . $filename;
        }

        throw new Exception('Failed to upload image.');
    }

    /**
     * Save base64 image from camera capture
     */
    private function saveBase64Image(string $base64Data): ?string {
        // Validate base64 image data
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
            throw new Exception('Invalid image data.');
        }

        $imageType = $matches[1];
        $allowedTypes = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
        
        if (!in_array(strtolower($imageType), $allowedTypes)) {
            throw new Exception('Invalid image type. Only JPG, PNG, GIF, and WebP allowed.');
        }

        // Remove the data URL prefix
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        $imageData = base64_decode($base64Image);

        if ($imageData === false) {
            throw new Exception('Failed to decode image data.');
        }

        // Validate file size (max 5MB)
        if (strlen($imageData) > 5 * 1024 * 1024) {
            throw new Exception('Image too large. Maximum size is 5MB.');
        }

        $uploadDir = __DIR__ . '/../../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = 'product_camera_' . time() . '_' . mt_rand(1000, 9999) . '.' . $imageType;
        $filepath = $uploadDir . $filename;

        if (file_put_contents($filepath, $imageData)) {
            return 'uploads/products/' . $filename;
        }

        throw new Exception('Failed to save image.');
    }

    /**
     * Get orders containing vendor's products
     */
    private function getVendorOrders(int $vendorId, ?int $limit = null): array {
        $limitClause = $limit ? "LIMIT {$limit}" : "";
        
        $stmt = $this->db->prepare("
            SELECT DISTINCT
                o.*,
                u.first_name,
                u.last_name,
                u.email,
                (SELECT COUNT(*) FROM order_items oi 
                 INNER JOIN vendor_products vp ON vp.product_id = oi.product_id 
                 WHERE oi.order_id = o.order_id AND vp.vendor_user_id = :vendor_id) as vendor_items_count
            FROM orders o
            INNER JOIN users u ON u.user_id = o.user_id
            INNER JOIN order_items oi ON oi.order_id = o.order_id
            INNER JOIN vendor_products vp ON vp.product_id = oi.product_id
            WHERE vp.vendor_user_id = :vendor_id2
            ORDER BY o.order_date DESC
            {$limitClause}
        ");
        $stmt->execute([
            ':vendor_id' => $vendorId,
            ':vendor_id2' => $vendorId,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
