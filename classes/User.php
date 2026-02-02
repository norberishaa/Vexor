<?php

class User{
    private mysqli $connection;

    public function __construct(mysqli $connection){
        $this->connection = $connection;
    }

    public function create(string $name, string $lastName, string $email, string $hashedPassword): bool{
        $sql = "INSERT INTO users (emri, mbiemri, email, password, admin)
                VALUES (?, ?, ?, ?, 'no')";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('ssss', $name, $lastName, $email, $hashedPassword);

        return $stmt->execute();
    }

    public function getAll(){
        return $this->connection->query(
            "SELECT user_id, emri, mbiemri, email, data_e_krijimit, admin  FROM users ORDER BY data_e_krijimit ASC"
        );
    }

    public function getById(int $id){
        $stmt = $this->connection->prepare(
            "SELECT user_id, emri, mbiemri, email, admin, data_e_krijimit FROM users WHERE user_id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function getByEmail(string $email){
        $stmt = $this->connection->prepare(
            "SELECT user_id, emri, mbiemri, email, password, admin FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function emailExists(string $email): bool{
        $stmt = $this->connection->prepare(
            "SELECT user_id FROM users WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    public function updateUser(int $id, string $name, string $email, string $admin): bool{
        $sql = "UPDATE users SET emri = ?, email = ?, admin = ? WHERE user_id = ?";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('sssi', $name, $email, $admin, $id);

        return $stmt->execute();
    }

    public function toggleAdmin(int $id): bool{
        $sql = "UPDATE users SET admin = IF(admin = 'yes', 'no', 'yes') WHERE user_id = ?";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    public function deleteUser(int $id): bool{
        $stmt = $this->connection->prepare(
            "DELETE FROM users WHERE user_id = ?"
        );
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    public function getCount(): int{
        $result = $this->connection->query("SELECT COUNT(*) as count FROM users");
        return $result->fetch_assoc()['count'];
    }
}