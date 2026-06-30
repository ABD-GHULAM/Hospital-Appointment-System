<?php
/**
 * Doctor model - database operations for doctors
 */

class DoctorModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(string $search = '', string $specialization = '', int $limit = 10, int $offset = 0): array
    {
        $sql = 'SELECT d.*, u.full_name, u.email, u.profile_image
                FROM doctors d
                JOIN users u ON d.user_id = u.id
                WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (u.full_name LIKE ? OR d.specialization LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($specialization) {
            $sql .= ' AND d.specialization = ?';
            $params[] = $specialization;
        }

        $sql .= ' ORDER BY u.full_name ASC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', string $specialization = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM doctors d JOIN users u ON d.user_id = u.id WHERE 1=1';
        $params = [];

        if ($search) {
            $sql .= ' AND (u.full_name LIKE ? OR d.specialization LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($specialization) {
            $sql .= ' AND d.specialization = ?';
            $params[] = $specialization;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.full_name, u.email, u.profile_image
             FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT d.*, u.full_name, u.email, u.profile_image
             FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.user_id = ?'
        );
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $userData, array $doctorData): int
    {
        $this->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $userId = $userModel->create(array_merge($userData, ['role' => 'doctor']));

            $stmt = $this->db->prepare(
                'INSERT INTO doctors (user_id, specialization, phone, experience, qualification, available_days, consultation_fee)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $userId,
                $doctorData['specialization'],
                $doctorData['phone'],
                $doctorData['experience'],
                $doctorData['qualification'],
                $doctorData['available_days'],
                $doctorData['consultation_fee'],
            ]);

            $this->db->commit();
            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $userData, array $doctorData): bool
    {
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        $this->db->beginTransaction();
        try {
            $userModel = new UserModel();
            $userModel->update($doctor['user_id'], $userData);

            $stmt = $this->db->prepare(
                'UPDATE doctors SET specialization=?, phone=?, experience=?, qualification=?, available_days=?, consultation_fee=? WHERE id=?'
            );
            $stmt->execute([
                $doctorData['specialization'],
                $doctorData['phone'],
                $doctorData['experience'],
                $doctorData['qualification'],
                $doctorData['available_days'],
                $doctorData['consultation_fee'],
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
        $doctor = $this->findById($id);
        if (!$doctor) return false;

        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$doctor['user_id']]);
    }

    public function getSpecializations(): array
    {
        $stmt = $this->db->query('SELECT DISTINCT specialization FROM doctors ORDER BY specialization');
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
    }

    public function createForExistingUser(int $userId, array $doctorData): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO doctors (user_id, specialization, phone, experience, qualification, available_days, consultation_fee)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $userId,
            $doctorData['specialization'],
            $doctorData['phone'],
            $doctorData['experience'],
            $doctorData['qualification'],
            $doctorData['available_days'],
            $doctorData['consultation_fee'],
        ]);
    }
}
