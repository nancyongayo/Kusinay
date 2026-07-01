<?php
/**
 * PantryModel
 * 
 * Handles Process 21: Replenishing Pantry
 * - Tracks food stock for households
 * - Linked to Process 19 (Grocery List) for auto-replenishment
 */
class PantryModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all pantry items for a family/user
     */
    public function getPantryItems(int $familyId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM household_pantry 
            WHERE family_id = :family_id 
            ORDER BY category, item_name
        ");
        $stmt->execute([':family_id' => $familyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add or Update pantry item (Replenish)
     */
    public function replenishItem(array $data): bool {
        try {
            $this->db->beginTransaction();

            // Check if item already exists in pantry
            $stmt = $this->db->prepare("
                SELECT pantry_id, quantity FROM household_pantry 
                WHERE family_id = :family_id AND item_name = :item_name
            ");
            $stmt->execute([
                ':family_id' => $data['family_id'],
                ':item_name' => $data['item_name']
            ]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update existing item
                $pantryId = $existing['pantry_id'];
                $newQuantity = $existing['quantity'] + $data['quantity'];
                
                $stmt = $this->db->prepare("
                    UPDATE household_pantry SET 
                        quantity = :quantity,
                        last_replenished = CURRENT_TIMESTAMP,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE pantry_id = :pantry_id
                ");
                $stmt->execute([
                    ':quantity'  => $newQuantity,
                    ':pantry_id' => $pantryId
                ]);
            } else {
                // Insert new item
                $stmt = $this->db->prepare("
                    INSERT INTO household_pantry (
                        family_id, user_id, item_name, category, 
                        quantity, unit, notes
                    ) VALUES (
                        :family_id, :user_id, :item_name, :category, 
                        :quantity, :unit, :notes
                    )
                ");
                $stmt->execute([
                    ':family_id' => $data['family_id'],
                    ':user_id'   => $data['user_id'],
                    ':item_name' => $data['item_name'],
                    ':category'  => $data['category'] ?? null,
                    ':quantity'  => $data['quantity'],
                    ':unit'      => $data['unit'],
                    ':notes'     => $data['notes'] ?? null
                ]);
                $pantryId = $this->db->lastInsertId();
            }

            // Record history
            $stmt = $this->db->prepare("
                INSERT INTO pantry_history (
                    pantry_id, change_type, quantity_change, 
                    reference_id, performed_by_user_id, notes
                ) VALUES (
                    :pantry_id, 'Replenishment', :change, 
                    :ref_id, :user_id, :notes
                )
            ");
            $stmt->execute([
                ':pantry_id' => $pantryId,
                ':change'    => $data['quantity'],
                ':ref_id'    => $data['reference_id'] ?? null,
                ':user_id'   => $data['user_id'],
                ':notes'     => $data['notes'] ?? 'Replenished from grocery list'
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Pantry replenishment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Consume item from pantry
     */
    public function consumeItem(int $pantryId, float $quantity, int $userId, ?int $refId = null, string $notes = 'Used for meal'): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT quantity FROM household_pantry WHERE pantry_id = ?");
            $stmt->execute([$pantryId]);
            $current = $stmt->fetchColumn();

            if ($current < $quantity) {
                // Not enough stock, but we'll allow it to go negative or just set to 0
                // For now, let's just set to 0 and record what was actually consumed
                $quantity = $current;
            }

            $newQuantity = $current - $quantity;

            $stmt = $this->db->prepare("UPDATE household_pantry SET quantity = ? WHERE pantry_id = ?");
            $stmt->execute([$newQuantity, $pantryId]);

            // Record history
            $stmt = $this->db->prepare("
                INSERT INTO pantry_history (
                    pantry_id, change_type, quantity_change, 
                    reference_id, performed_by_user_id, notes
                ) VALUES (
                    ?, 'Consumption', ?, ?, ?, ?
                )
            ");
            $stmt->execute([$pantryId, -$quantity, $refId, $userId, $notes]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * ✨ PROCESS 22: Get pantry history for a family
     */
    public function getPantryHistory(int $familyId, int $limit = 50): array {
        $stmt = $this->db->prepare("
            SELECT 
                ph.*,
                hp.item_name,
                hp.category,
                hp.unit,
                u.first_name,
                u.last_name
            FROM pantry_history ph
            JOIN household_pantry hp ON hp.pantry_id = ph.pantry_id
            JOIN users u ON u.user_id = ph.performed_by_user_id
            WHERE hp.family_id = :family_id
            ORDER BY ph.action_date DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':family_id', $familyId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
