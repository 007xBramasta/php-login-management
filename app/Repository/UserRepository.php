<?php

namespace Bramasta\Belajar\PHP\MVC\Repository;

use Bramasta\Belajar\PHP\MVC\Domain\User;


class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {   
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statment = $this->connection->prepare("INSERT INTO users(id, name, password) VALUES (?, ?, ?)");
        $statment->execute([
            $user->id, $user->name, $user->password
        ]);
        return $user;
    }   

    public function update(User $user): User
    {
        $statment = $this->connection->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
        $statment->execute([
            $user->name, $user->password, $user->id
        ]);
        return $user;
    }

    public function findById(string $id): ?User 
    {
        $statment = $this->connection->prepare("SELECT id, name, password FROM users WHERE id = ?");
        $statment->execute([$id]);

      try{
        if($row = $statment->fetch())
        {
            $user = new User();
            $user->id = $row['id'];
            $user->name = $row['name'];
            $user->password = $row['password'];
            return $user;
        }else{
            return null;
        }
      }finally{
        $statment->closeCursor();
      }
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE from users");    
    }

    
}