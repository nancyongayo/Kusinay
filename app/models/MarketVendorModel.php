<?php
/**
 * MarketVendorModel
 * 
 * Handles Process 20: Selling Goods (Market Vendor Product Management)
 * - Market vendors manage their products
 * - Track product inventory, pricing
 * - Record sales transactions
 */
class MarketVendorModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // ========================================================================
    // PROCESS 20: Vendor Products
    // ========================================================================

    /**
     * Create a new product
     */
    public function createProduct(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO vendor_products (
                vendor_user_id, product_name, category, description,
                unit, price_per_unit, stock_quantity, nutritional_info,
                product_image, is_available, is_featured
            ) VALUES (
                :vendor_user_id, :product_name, :category, :description,
                :unit, :price_per_unit, :stock_quantity, :nutritional_info,
                :product_image, :is_available, :is_featured
            )
        ");
        
        $stmt->execute([
            ':vendor_user_id'   => $data['vendor_user_id'],
            ':product_name'     => $data['product_name'],
            ':category'         => $data['category'] ?? null,
            ':description'      => $data['description'] ?? null,
            ':unit'             => $data['unit'],
            ':price_per_unit'   => $data['price_per_unit'],
            ':stock_quantity'   => $data['stock_quantity'] ?? 0,
            ':nutritional_info' => $data['nutritional_info'] ?? null,
            ':product_image'    => $data['product_image'] ?? null,
            ':is_available'     => $data['is_available'] ?? 1,
            ':is_featured'      => $data['is_featured'] ?? 0,
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update product
     */
    public function updateProduct(int $productId, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE vendor_products SET
                product_name = :product_name,
                category = :category,
                description = :description,
                unit = :unit,
                price_per_unit = :price_per_unit,
                stock_quantity = :stock_quantity,
                nutritional_info = :nutritional_info,
                product_image = :product_image,
                is_available = :is_available,
                is_featured = :is_featured
            WHERE product_id = :product_id
        ");
        
        return $stmt->execute([
            ':product_id'       => $productId,
            ':product_name'     => $data['product_name'],
            ':category'         => $data['category'] ?? null,
            ':description'      => $data['description'] ?? null,
            ':unit'             => $data['unit'],
            ':price_per_unit'   => $data['price_per_unit'],
            ':stock_quantity'   => $data['stock_quantity'],
            ':nutritional_info' => $data['nutritional_info'] ?? null,
            ':product_image'    => $data['product_image'] ?? null,
            ':is_available'     => $data['is_available'],
            ':is_featured'      => $data['is_featured'],
        ]);
    }

    /**
     * Get product by ID
     */
    public function getProductById(int $productId): ?array {
        $stmt = $this->db->prepare("
            SELECT 
                vp.*,
                u.first_name AS vendor_first_name,
                u.last_name AS vendor_last_name
            FROM vendor_products vp
            JOIN users u ON u.user_id = vp.vendor_user_id
            WHERE vp.product_id = :product_id
        ");
        $stmt->execute([':product_id' => $productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all products for a vendor
     */
    public function getProductsByVendor(int $vendorUserId, ?string $search = null, ?string $category = null, ?bool $availableOnly = false): array {
        $where = "WHERE vp.vendor_user_id = :vendor_user_id";
        $params = [':vendor_user_id' => $vendorUserId];
        
        if ($availableOnly) {
            $where .= " AND vp.is_available = 1";
        }
        
        // Add search filter
        if (!empty($search)) {
            $where .= " AND vp.product_name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        
        // Add category filter
        if (!empty($category)) {
            $where .= " AND vp.category = :category";
            $params[':category'] = $category;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                vp.*,
                (SELECT COUNT(*) FROM product_sales WHERE product_id = vp.product_id) AS total_sales_count,
                (SELECT SUM(quantity_sold) FROM product_sales WHERE product_id = vp.product_id) AS total_quantity_sold
            FROM vendor_products vp
            {$where}
            ORDER BY vp.is_featured DESC, vp.product_name ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all available products (for mothers/parents to browse)
     */
    public function getAllAvailableProducts(?string $category = null, ?string $search = null): array {
        $where = ["vp.is_available = 1"];
        $params = [];
        
        if ($category) {
            $where[] = "vp.category = :category";
            $params[':category'] = $category;
        }
        
        if ($search) {
            $where[] = "(vp.product_name LIKE :search1 OR vp.description LIKE :search2)";
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                vp.*,
                u.first_name AS vendor_first_name,
                u.last_name AS vendor_last_name
            FROM vendor_products vp
            JOIN users u ON u.user_id = vp.vendor_user_id
            {$whereClause}
            ORDER BY vp.is_featured DESC, vp.product_name ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get product categories
     */
    public function getCategories(): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT category
            FROM vendor_products
            WHERE category IS NOT NULL AND category != ''
            ORDER BY category ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $productId): bool {
        $stmt = $this->db->prepare("DELETE FROM vendor_products WHERE product_id = :product_id");
        return $stmt->execute([':product_id' => $productId]);
    }

    /**
     * Toggle product availability
     */
    public function toggleAvailability(int $productId): bool {
        $stmt = $this->db->prepare("
            UPDATE vendor_products SET
                is_available = NOT is_available
            WHERE product_id = :product_id
        ");
        return $stmt->execute([':product_id' => $productId]);
    }

    /**
     * Update stock quantity
     */
    public function updateStock(int $productId, float $quantity, string $operation = 'set'): bool {
        if ($operation === 'add') {
            $stmt = $this->db->prepare("
                UPDATE vendor_products SET
                    stock_quantity = stock_quantity + :quantity
                WHERE product_id = :product_id
            ");
        } elseif ($operation === 'subtract') {
            $stmt = $this->db->prepare("
                UPDATE vendor_products SET
                    stock_quantity = GREATEST(0, stock_quantity - :quantity)
                WHERE product_id = :product_id
            ");
        } else {
            $stmt = $this->db->prepare("
                UPDATE vendor_products SET
                    stock_quantity = :quantity
                WHERE product_id = :product_id
            ");
        }
        
        return $stmt->execute([
            ':product_id' => $productId,
            ':quantity'   => $quantity,
        ]);
    }

    // ========================================================================
    // Product Sales/Transactions
    // ========================================================================

    /**
     * Record a sale
     */
    public function recordSale(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO product_sales (
                product_id, vendor_user_id, buyer_user_id, grocery_list_item_id,
                quantity_sold, unit, price_per_unit, total_amount,
                payment_method, notes
            ) VALUES (
                :product_id, :vendor_user_id, :buyer_user_id, :grocery_list_item_id,
                :quantity_sold, :unit, :price_per_unit, :total_amount,
                :payment_method, :notes
            )
        ");
        
        $stmt->execute([
            ':product_id'          => $data['product_id'],
            ':vendor_user_id'      => $data['vendor_user_id'],
            ':buyer_user_id'       => $data['buyer_user_id'] ?? null,
            ':grocery_list_item_id'=> $data['grocery_list_item_id'] ?? null,
            ':quantity_sold'       => $data['quantity_sold'],
            ':unit'                => $data['unit'],
            ':price_per_unit'      => $data['price_per_unit'],
            ':total_amount'        => $data['total_amount'],
            ':payment_method'      => $data['payment_method'] ?? 'Cash',
            ':notes'               => $data['notes'] ?? null,
        ]);
        
        $saleId = (int) $this->db->lastInsertId();
        
        // Update product stock
        $this->updateStock($data['product_id'], $data['quantity_sold'], 'subtract');
        
        return $saleId;
    }

    /**
     * Get sales by vendor
     */
    public function getSalesByVendor(int $vendorUserId, ?array $filters = []): array {
        $where = ["ps.vendor_user_id = :vendor_user_id"];
        $params = [':vendor_user_id' => $vendorUserId];
        
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(ps.sale_date) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(ps.sale_date) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        
        if (!empty($filters['product_id'])) {
            $where[] = "ps.product_id = :product_id";
            $params[':product_id'] = $filters['product_id'];
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                ps.*,
                vp.product_name,
                u.first_name AS buyer_first_name,
                u.last_name AS buyer_last_name
            FROM product_sales ps
            JOIN vendor_products vp ON vp.product_id = ps.product_id
            LEFT JOIN users u ON u.user_id = ps.buyer_user_id
            {$whereClause}
            ORDER BY ps.sale_date DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get sales statistics for vendor
     */
    public function getVendorSalesStats(int $vendorUserId, ?string $dateFrom = null, ?string $dateTo = null): array {
        $where = ["vendor_user_id = :vendor_user_id"];
        $params = [':vendor_user_id' => $vendorUserId];
        
        if ($dateFrom) {
            $where[] = "DATE(sale_date) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        
        if ($dateTo) {
            $where[] = "DATE(sale_date) <= :date_to";
            $params[':date_to'] = $dateTo;
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_sales,
                SUM(quantity_sold) as total_quantity_sold,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as average_sale_amount
            FROM product_sales
            {$whereClause}
        ");
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get top selling products for vendor
     */
    public function getTopSellingProducts(int $vendorUserId, int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT 
                vp.product_id,
                vp.product_name,
                vp.category,
                COUNT(ps.sale_id) as sales_count,
                SUM(ps.quantity_sold) as total_quantity_sold,
                SUM(ps.total_amount) as total_revenue
            FROM vendor_products vp
            LEFT JOIN product_sales ps ON ps.product_id = vp.product_id
            WHERE vp.vendor_user_id = :vendor_user_id
            GROUP BY vp.product_id
            ORDER BY total_revenue DESC, sales_count DESC
            LIMIT :limit
        ");
        $stmt->execute([
            ':vendor_user_id' => $vendorUserId,
            ':limit'          => $limit,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get vendor dashboard statistics
     */
    public function getVendorDashboardStats(int $vendorUserId): array {
        // Product count
        $stmtProducts = $this->db->prepare("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_products,
                SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_products
            FROM vendor_products
            WHERE vendor_user_id = :vendor_user_id
        ");
        $stmtProducts->execute([':vendor_user_id' => $vendorUserId]);
        $productStats = $stmtProducts->fetch(PDO::FETCH_ASSOC);
        
        // Sales stats (today)
        $stmtToday = $this->db->prepare("
            SELECT 
                COUNT(*) as today_sales_count,
                SUM(total_amount) as today_revenue
            FROM product_sales
            WHERE vendor_user_id = :vendor_user_id
              AND DATE(sale_date) = CURDATE()
        ");
        $stmtToday->execute([':vendor_user_id' => $vendorUserId]);
        $todayStats = $stmtToday->fetch(PDO::FETCH_ASSOC);
        
        // Sales stats (this month)
        $stmtMonth = $this->db->prepare("
            SELECT 
                COUNT(*) as month_sales_count,
                SUM(total_amount) as month_revenue
            FROM product_sales
            WHERE vendor_user_id = :vendor_user_id
              AND YEAR(sale_date) = YEAR(CURDATE())
              AND MONTH(sale_date) = MONTH(CURDATE())
        ");
        $stmtMonth->execute([':vendor_user_id' => $vendorUserId]);
        $monthStats = $stmtMonth->fetch(PDO::FETCH_ASSOC);
        
        return array_merge(
            $productStats ?: [],
            $todayStats ?: [],
            $monthStats ?: []
        );
    }
}
