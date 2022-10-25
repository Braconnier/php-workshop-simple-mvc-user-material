<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    public function selectOneByEmail(string $email): array | false
    {
        $query = "SELECT * FROM " . self::TABLE . " WHERE email = :email";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":email", $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
