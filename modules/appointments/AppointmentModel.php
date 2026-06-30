<?php
/**
 * Appointment model - database operations for appointments
 */

class AppointmentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Build filtered query for appointments
     */
    private function buildFilterQuery(array $filters, bool $count = false): array
    {
        $select = $count
            ? 'SELECT COUNT(*)'
            : 'SELECT a.*, 
               pu.full_name AS patient_name, pu.email AS patient_email,
               du.full_name AS doctor_name, d.specialization,
               p.phone AS patient_phone';

        $sql = "{$select}
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (pu.full_name LIKE ? OR du.full_name LIKE ? OR a.reason LIKE ?)';
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND a.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['doctor_id'])) {
            $sql .= ' AND a.doctor_id = ?';
            $params[] = $filters['doctor_id'];
        }

        if (!empty($filters['patient_id'])) {
            $sql .= ' AND a.patient_id = ?';
            $params[] = $filters['patient_id'];
        }

        if (!empty($filters['date'])) {
            $sql .= ' AND a.appointment_date = ?';
            $params[] = $filters['date'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= ' AND a.appointment_date >= ?';
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= ' AND a.appointment_date <= ?';
            $params[] = $filters['date_to'];
        }

        if (!empty($filters['today'])) {
            $sql .= ' AND a.appointment_date = CURDATE()';
        }

        if (!empty($filters['upcoming'])) {
            $sql .= ' AND a.appointment_date >= CURDATE() AND a.status IN ("pending", "approved")';
        }

        return ['sql' => $sql, 'params' => $params];
    }

    public function getAll(array $filters = [], int $limit = 10, int $offset = 0, string $orderBy = 'a.appointment_date DESC, a.appointment_time DESC'): array
    {
        $query = $this->buildFilterQuery($filters);
        $sql = $query['sql'] . " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $params = array_merge($query['params'], [$limit, $offset]);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $query = $this->buildFilterQuery($filters, true);
        $stmt = $this->db->prepare($query['sql']);
        $stmt->execute($query['params']);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, 
                    pu.full_name AS patient_name, pu.email AS patient_email,
                    du.full_name AS doctor_name, d.specialization, d.consultation_fee,
                    p.gender, p.age, p.blood_group, p.phone AS patient_phone, p.address
             FROM appointments a
             JOIN patients p ON a.patient_id = p.id
             JOIN users pu ON p.user_id = pu.id
             JOIN doctors d ON a.doctor_id = d.id
             JOIN users du ON d.user_id = du.id
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status, reason)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['patient_id'],
            $data['doctor_id'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['status'] ?? 'pending',
            $data['reason'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        $allowed = ['patient_id', 'doctor_id', 'appointment_date', 'appointment_time', 'status', 'reason', 'notes'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $params[] = $id;
        $sql = 'UPDATE appointments SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): bool
    {
        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        return $this->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM appointments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM appointments WHERE status = ?');
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function countToday(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE()"
        )->fetchColumn();
    }

    public function getMonthlyStats(int $months = 6): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(appointment_date, '%Y-%m') AS month,
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled
             FROM appointments
             WHERE appointment_date >= DATE_SUB(CURDATE(), INTERVAL {$months} MONTH)
             GROUP BY month
             ORDER BY month ASC"
        );
        return $stmt->fetchAll();
    }

    public function getStatusDistribution(): array
    {
        $stmt = $this->db->query(
            "SELECT status, COUNT(*) AS count FROM appointments GROUP BY status"
        );
        return $stmt->fetchAll();
    }

    public function getRecent(int $limit = 5): array
    {
        return $this->getAll([], $limit, 0, 'a.created_at DESC');
    }

    public function isSlotAvailable(int $doctorId, string $date, string $time, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM appointments 
                WHERE doctor_id = ? AND appointment_date = ? AND appointment_time = ?
                AND status NOT IN ('cancelled', 'rejected')";
        $params = [$doctorId, $date, $time];

        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() === 0;
    }
}
