<?php

class CVE{
    private mysqli $connection;
    
    
    public function __construct(mysqli $connection){
        $this->connection = $connection;
    }


    public function getAll(){
        return $this->connection->query(
            "SELECT cve_id, name, status, severity, date_reported, description FROM cve_list ORDER BY severity DESC"
        );
    }

    public function getById(string $cveId)
    {
        $stmt = $this->connection->prepare(
            "SELECT * FROM cve_list WHERE cve_id = ? LIMIT 1"
        );
        $stmt->bind_param('s', $cveId);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function update(string $cveId, string $name, string $status, float $severity, string $dateReported, string $description): bool{
        $sql = "
            UPDATE cve_list
            SET name = ?, status = ?, severity = ?, date_reported = ?, description = ?
            WHERE cve_id = ?
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('ssdss s', $name, $status, $severity, $dateReported, $description, $cveId);

        return $stmt->execute();
    }

    public function delete(string $cveId): bool{
        $stmt = $this->connection->prepare(
            "DELETE FROM cve_list WHERE cve_id = ?"
        );
        $stmt->bind_param('s', $cveId);

        return $stmt->execute();
    }

    public function getCount(): int{
        $result = $this->connection->query("SELECT COUNT(*) as count FROM cve_list");
        return $result->fetch_assoc()['count'];
    }

    public function getStats(){
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(status != 'Patched') AS unpatched,
                SUM(status = 'Patched') AS patched,
                SUM(severity >= 8) AS high,
                SUM(severity <= 7) AS medium
            FROM cve_list
        ";

        return $this->connection->query($sql)->fetch_assoc();
    }
}
?>