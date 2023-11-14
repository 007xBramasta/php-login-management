<?php

namespace Bramasta\Belajar\PHP\MVC\Repository;

use Bramasta\Belajar\PHP\MVC\Domain\Session;

class SessionRepository
{   
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Session $session): Session
    {
        $statment = $this->connection->prepare("INSERT INTO sessions(id, user_id) VALUES (?, ?)");
        $statment->execute([$session->id, $session->userId]);
        return $session;
    }

    public function findById(string $id): ?Session 
    {
        $statment = $this->connection->prepare("SELECT id, user_id from sessions WHERE id = ?");
        $statment->execute([$id]);

       try{
          if($row = $statment->fetch()){
                $session =  new Session();
                $session->id = $row['id'];
                $session->userId = $row['user_id'];
                return $session;
            }else{
                return null;
            }
        }finally{
            $statment->closeCursor();
        }
    }   

    public function deleteById(string $id): void
    {
        $statment = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $statment->execute([$id]);
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}