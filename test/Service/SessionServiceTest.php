<?php 

namespace Bramasta\Belajar\PHP\MVC\Service;

require_once __DIR__ .'/../Helper/helper.php';

use PHPUnit\Framework\TestCase;
use Bramasta\Belajar\PHP\MVC\config\Database;
use Bramasta\Belajar\PHP\MVC\Domain\Session;
use Bramasta\Belajar\PHP\MVC\Domain\User;
use Bramasta\Belajar\PHP\MVC\Repository\SessionRepository;
use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;

class SessionServiceTest extends TestCase
{
    private SessionService $sessionService;
    private SessionRepository $sessionRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->sessionRepository = new SessionRepository(Database::getConnection());
        $this->userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($this->sessionRepository, $this->userRepository);

        $this->sessionRepository->deleteAll();
        $this->userRepository->deleteAll();

        $user = new User();
        $user->id = "bram";
        $user->name = "Bram";
        $user->password = "rahasia";
        $this->userRepository->save($user);
    }

    public function testCreate()
    {
        $session = $this->sessionService->create("bram");

        $this->expectOutputRegex("[X-BRAM-SESSION: $session->id]");

        $result = $this->sessionRepository->findById($session->id);

        self::assertEquals("bram", $result->userId);
    }

    public function testDestroy()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "bram";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $this->sessionService->destroy();

        $this->expectOutputRegex("[X-BRAM-SESSION: ]");

        $result = $this->sessionRepository->findById($session->id);
        self::assertNull($result);
    }

    public function testCurrent()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "bram";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }   

    public function testCurrentNotFound()
    {
        $session = new Session();
        $session->id = uniqid();
        $session->userId = "bram";

        $this->sessionRepository->save($session);

        $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

        $user = $this->sessionService->current();

        self::assertEquals($session->userId, $user->id);
    }   
}