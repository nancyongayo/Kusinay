<?php
/**
 * PantryController
 * Handles Process 21: Pantry Management for Mothers
 */

require_once __DIR__ . '/../models/PantryModel.php';

class PantryController {
    private PDO $db;
    private PantryModel $model;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->model = new PantryModel($db);
    }

    /**
     * Show pantry inventory
     */
    public function showPantry(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        // Get user's family_id from family_profiles
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $familyRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$familyRow) {
            $_SESSION['flash_error'] = 'No family profile found. Please complete your family profile first.';
            header('Location: index.php?action=motherWizard');
            exit;
        }

        $familyId = $familyRow['family_id'];
        
        // Get all pantry items
        $pantryItems = $this->model->getPantryItems($familyId);
        
        // Group by category
        $itemsByCategory = [];
        foreach ($pantryItems as $item) {
            $category = $item['category'] ?? 'Other';
            if (!isset($itemsByCategory[$category])) {
                $itemsByCategory[$category] = [];
            }
            $itemsByCategory[$category][] = $item;
        }

        // Calculate statistics
        $totalItems = count($pantryItems);
        $totalValue = array_sum(array_column($pantryItems, 'quantity'));
        $categories = count($itemsByCategory);

        include __DIR__ . '/../views/mother/pantry.php';
    }

    /**
     * Add item to pantry manually
     */
    public function addItem(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: index.php?action=pantry');
            exit;
        }

        // Get family_id from family_profiles
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $familyRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$familyRow) {
            $_SESSION['flash_error'] = 'No family profile found.';
            header('Location: index.php?action=pantry');
            exit;
        }

        $data = [
            'family_id' => $familyRow['family_id'],
            'user_id' => $_SESSION['user_id'],
            'item_name' => $_POST['item_name'],
            'category' => $_POST['category'],
            'quantity' => (float)$_POST['quantity'],
            'unit' => $_POST['unit'],
            'notes' => $_POST['notes'] ?? 'Manually added'
        ];

        $success = $this->model->replenishItem($data);

        if ($success) {
            $_SESSION['flash_success'] = 'Item added to pantry successfully!';
        } else {
            $_SESSION['flash_error'] = 'Failed to add item to pantry.';
        }

        header('Location: index.php?action=pantry');
        exit;
    }

    /**
     * Update pantry item quantity
     */
    public function updateQuantity(): void {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $pantryId = (int)$_POST['pantry_id'];
        $newQuantity = (float)$_POST['quantity'];

        try {
            $stmt = $this->db->prepare("
                UPDATE household_pantry 
                SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP 
                WHERE pantry_id = :pantry_id
            ");
            $success = $stmt->execute([
                ':quantity' => $newQuantity,
                ':pantry_id' => $pantryId
            ]);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Delete pantry item
     */
    public function deleteItem(): void {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            exit;
        }

        $pantryId = (int)$_POST['pantry_id'];

        try {
            $stmt = $this->db->prepare("DELETE FROM household_pantry WHERE pantry_id = :pantry_id");
            $success = $stmt->execute([':pantry_id' => $pantryId]);

            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * ✨ PROCESS 22: Consume item from pantry (for cooking/eating)
     */
    public function consumeItem(): void {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $pantryId = (int)$_POST['pantry_id'];
        $quantity = (float)$_POST['quantity'];
        $notes = $_POST['notes'] ?? 'Used for cooking';

        try {
            $success = $this->model->consumeItem($pantryId, $quantity, $_SESSION['user_id'], null, $notes);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item consumed successfully!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to consume item. Check if quantity is available.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * ✨ PROCESS 22: View consumption history
     */
    public function showHistory(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        // Get user's family_id
        $stmt = $this->db->prepare("
            SELECT family_id FROM family_profiles 
            WHERE source_user_id = :user_id 
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $familyRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$familyRow) {
            $_SESSION['flash_error'] = 'No family profile found.';
            header('Location: index.php?action=pantry');
            exit;
        }

        $familyId = $familyRow['family_id'];
        
        // Get pantry history for this family
        $history = $this->model->getPantryHistory($familyId);

        include __DIR__ . '/../views/mother/pantry_history.php';
    }
}
