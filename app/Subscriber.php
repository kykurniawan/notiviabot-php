<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.0 403 Forbidden', TRUE, 403);
    exit();
}

require_once('Model.php');

class Subscriber extends Model
{
    protected string $table = 'subscribers';

    public function all()
    {
        $statement = $this->database->prepare("SELECT * FROM $this->table ORDER BY created_at DESC;");
        $statement->execute();
        if ($statement->rowCount() < 1) return [];

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $statement = $this->database->prepare("SELECT * FROM $this->table WHERE id = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();
        if ($statement->rowCount() < 1) return null;

        return $statement->fetchObject();
    }

    public function findByChatId($chatId)
    {
        $statement = $this->database->prepare("SELECT * FROM $this->table WHERE chat_id = :chat_id");
        $statement->bindParam(':chat_id', $chatId);
        $statement->execute();
        if ($statement->rowCount() < 1) return null;

        return $statement->fetchObject();
    }

    public function create()
    {
        $id = uniqid();
        $statement = $this->database->prepare("INSERT INTO $this->table (id) VALUES (:id);");
        $statement->bindParam(':id', $id);
        $statement->execute();

        // hit api 

        return $id;
    }

    public function update($id, $name = null, $chat_id = null)
    {
        $statement = $this->database->prepare("UPDATE $this->table SET name = :name, chat_id = :chat_id WHERE id = :id");
        return $statement->execute([
            'name' => $name,
            'chat_id' => $chat_id,
            'id' => $id,
        ]);
    }

    public function delete($id)
    {
        $statement = $this->database->prepare("DELETE FROM $this->table WHERE id = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->rowCount() > 0;
    }
}
