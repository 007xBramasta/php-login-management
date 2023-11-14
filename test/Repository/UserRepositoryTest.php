<?php

namespace Bramasta\Belajar\PHP\MVC\Repository;

use PHPUnit\Framework\TestCase;
use Bramasta\Belajar\PHP\MVC\Config\Database;
use Bramasta\Belajar\PHP\MVC\Domain\User;

class UserRepositoryTest extends TestCase
{
    
    private UserRepository $userRepository;
    private SessionRepository $sessionRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->sessionRepository->deleteAll();

        $this->userRepository = new UserRepository(Database::getConnection());
        $this->userRepository->deleteAll();
    }

    public function testSaveSuccess()
    {
        $user = new User();
        $user->id = "bram";
        $user->name = "Bram";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

    public function testFindByIdNotFound()
    {
        $user = $this->userRepository->findById("not found");
        self::assertNull($user);
    }

    public function testUpdate()
    {
        $user = new User();
        $user->id = "bram";
        $user->name = "Bram";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $user->name = "Domes";
        $this->userRepository->update($user);

        $result = $this->userRepository->findById($user->id);

        self::assertEquals($user->id, $result->id);
        self::assertEquals($user->name, $result->name);
        self::assertEquals($user->password, $result->password);
    }

}