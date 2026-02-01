<?php

class News
{
    private mysqli $connection;

    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    public function create(string $title, string $description, string $author, string $articleUrl): bool
    {
        $sql = "INSERT INTO news (news_title, news_description, author, date_posted, article_url)
                VALUES (?, ?, ?, NOW(), ?)";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('ssss', $title, $description, $author, $articleUrl);

        return $stmt->execute();
    }

    public function getAll()
    {
        return $this->connection->query(
            "SELECT news_id, news_title, news_description, author, date_posted, article_url FROM news ORDER BY date_posted DESC"
        );
    }

    public function update(int $id, string $title, string $description, string $author, string $articleUrl): bool
    {
        $sql = "
            UPDATE news
            SET news_title = ?, news_description = ?, author = ?, article_url = ?
            WHERE news_id = ?
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('ssssi', $title, $description, $author, $articleUrl, $id);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM news WHERE news_id = ?"
        );
        $stmt->bind_param('i', $id);

        return $stmt->execute();
    }

    public function getCount(): int
    {
        $result = $this->connection->query("SELECT COUNT(*) as count FROM news");
        return $result->fetch_assoc()['count'];
    }
}