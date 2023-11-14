<?php

namespace Bramasta\Belajar\PHP\MVC\Middleware
{
    require_once __DIR__ . '/..Helper/helper.php';
    
    use Bramasta\Belajar\PHP\MVC\config\Database;
    use Bramasta\Belajar\PHP\MVC\Domain\Session;
    use Bramasta\Belajar\PHP\MVC\Domain\User;
    use Bramasta\Belajar\PHP\MVC\Repository\SessionRepository;
    use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;
    use Bramasta\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    class MustLoginMiddlewareTest extends TestCase
    {
        private MustLoginMiddleware $middlware;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->middlware = new MustLoginMiddleware();
            putenv("mode=test");

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->sessionRepository = new SessionRepository(Database::getConnection());

            $this->sessionRepository->deleteAll();
            $this->userRepository->deleteAll();
        }

        public function testBefore()
        {
            $this->middlware->before();
            $this->expectOutputRegex("[Location: /users/login]");
        }

        public function testBeforeLoginUser()
        {
            $user = new User;
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = "rahasia";
            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;
            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->middlware->before();
            $this->expectOutputString("");
        }
    }
}

