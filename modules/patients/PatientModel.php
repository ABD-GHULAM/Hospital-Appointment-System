<?php
/**
 * Patient model - database operations for patients
 */

class PatientModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $sql = 'SELECT p.*, u.full_name, u.email, u.profile_image
                FROM patients p
                JOIN users u ON p.user_id = u.id
                WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR p.phone LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= ' ORDER BY u.full_name ASC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM patients p JOIN users u ON p.user_id = u.id WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ? OR p.phone LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.full_name, u.email, u.profile_image
             FROM patients p JOIN users u ON p.user_id = u.id WHERE p.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, u.full_name, u.email, u.profile_image
             FROM patients p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $userData, array $patientData): int
    {
        $this->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $userId = $userModel->create(array_merge($userData, ['role' => 'patient']));

            $stmt = $this->db->prepare(
                'INSERT INTO patients (user_id, gender, age, blood_group, phone, address) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                $patientData['gender'],
                $patientData['age'],
                $patientData['blood_group'] ?? null,
                $patientData['phone'],
                $patientData['address'],
            ]);

            $this->db->commit();
            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $userData, array $patientData): bool
    {
        $patient = $this->findById($id);
        if (!$patient) return false;

        $this->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $userModel->update($patient['user_id'], $userData);

            $stmt = $this->db->prepare(
                'UPDATE patients SET gender=?, age=?, blood_group=?, phone=?, address=? WHERE id=?'
            );
            $stmt->execute([
                $patientData['gender'],
                $patientData['age'],
                $patientData['blood_group'] ?? null,
                $patientData['phone'],
                $patientData['address'],
                $id,
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $patient = $this->findById($id);
        if (!$patient) return false;

        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$patient['user_id']]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM patients')->fetchColumn();
    }

    public function createForExistingUser(int $userId, array $patientData): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO patients (user_id, gender, age, blood_group, phone, address) VALUES (?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $userId,
            $patientData['gender'],
            $patientData['age'],
            $patientData['blood_group'] ?? null,
            $patientData['phone'],
            $patientData['address'],
        ]);
    }
}
