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
    public function createUser(array $user): array | false
    {
        $query = "INSERT INTO " . self::TABLE . " (email, password, pseudo, firstname, lastname) VALUES (:email, :password, :pseudo, :firstname, :lastname)";
        $statement = $this->pdo->prepare($query);
        $statement->bindValue(":email",  $_POST['email'], \PDO::PARAM_STR);
        $statement->bindValue(":password", $user['password'], \PDO::PARAM_STR);
        $statement->bindValue(":pseudo", $user['pseudo'], \PDO::PARAM_STR);
        $statement->bindValue(":firstname", $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue(":lastname", $user['lastname'], \PDO::PARAM_STR);
        $statement->execute();

        return $user;
    }
}
