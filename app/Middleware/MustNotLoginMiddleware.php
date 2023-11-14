<?php 

namespace Bramasta\Belajar\PHP\MVC\Middleware;

use Bramasta\Belajar\PHP\MVC\App\View;
use Bramasta\Belajar\PHP\MVC\config\Database;
use Bramasta\Belajar\PHP\MVC\Repository\SessionRepository;
use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;
use Bramasta\Belajar\PHP\MVC\Service\SessionService;

class MustNotLoginMiddleware implements Middleware
{
    private SessionService $sessionService;

    public function __construct()
    {
        $sessionRepository = new SessionRepository(Database::getConnection());
        $userRepository = new UserRepository(Database::getConnection());
        $this->sessionService = new SessionService($sessionRepository, $userRepository);
    }

    function before(): void
    {
        $user = $this->sessionService->current();
        if ($user != null) {
            View::redirect('/');
        }       
    }
}