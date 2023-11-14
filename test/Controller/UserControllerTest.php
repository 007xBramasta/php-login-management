<?php

namespace Bramasta\Belajar\PHP\MVC\Controller
{
    require_once __DIR__ . '/..Helper/helper.php';
    
    use Bramasta\Belajar\PHP\MVC\config\Database;
    use Bramasta\Belajar\PHP\MVC\Domain\Session;
    use Bramasta\Belajar\PHP\MVC\Domain\User;
    use Bramasta\Belajar\PHP\MVC\Repository\SessionRepository;
    use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;
    use Bramasta\Belajar\PHP\MVC\Service\SessionService;
    use PHPUnit\Framework\TestCase;

    use function PHPUnit\Framework\assertEquals;

    class UserControllerTest extends TestCase
    {   

        private UserController $userController;
        private UserRepository $userRepository;
        private SessionRepository $sessionRepository;

        protected function setUp(): void
        {
            $this->userController = new UserController();

            $this->sessionRepository = new SessionRepository(Database::getConnection());
            $this->sessionRepository->deleteAll();

            $this->userRepository = new UserRepository(Database::getConnection());
            $this->userRepository->deleteAll();

            putenv("mode=test");
        }


        public function testRegister()
        {
            $this->userController->register();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
        }

        public function testPostRegisterSuccess()
        {
            $_POST['id'] = 'bram';
            $_POST['name'] = 'Bram';
            $_POST['password'] = 'rahasia';
            
            $this->userController->postRegister();

            $this->expectOutputRegex("[Location: users/login]");
        }

        public function testPostValidationError()
        {
            $_POST['id'] = '';
            $_POST['name'] = '';
            $_POST['password'] = '';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[Id, Name, Password can not blank]");
        }

        public function testPostRegisterDuplicate()
        {  
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = "rahasia";

            $this->userRepository->save($user);

            $_POST['id'] = 'bram';
            $_POST['name'] = 'Bram';
            $_POST['password'] = 'rahasia';

            $this->userController->postRegister();

            $this->expectOutputRegex("[Register]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Register new User]");
            $this->expectOutputRegex("[User Id already exists]");
        }

        public function testLogin()
        {   
            $this->userController->login();

            $this->expectOutputRegex("[Login User]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
        }

        public function testLoginSuccess()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $_POST['id'] = "bram";
            $_POST['password'] = 'rahasia';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-BRAM-SESSION: ]");
        }

        public function testLoginValidationError()
        {
            $_POST['id'] = '';
            $_POST['password'] = '';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login User]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id, Password can not blank]");
        }

        public function testLoginUserNotFound()
        {
            $_POST['id'] = 'not found';
            $_POST['password'] = 'not found';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login User]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLoginWrongPassword()
        {   
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $_POST['id'] = 'bram';
            $_POST['password'] = 'salah';

            $this->userController->postLogin();

            $this->expectOutputRegex("[Login User]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[Password]");
            $this->expectOutputRegex("[Id or password is wrong]");
        }

        public function testLogout()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->logout();

            $this->expectOutputRegex("[Location: /]");
            $this->expectOutputRegex("[X-BRAM-SESSION: ]");

        }

        public function testUpdateProfile()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updateProfile();

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bram]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Bram]");
        }

        public function testUpdateProfileSuccess()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = 'Domes';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById("bram");
            assertEquals("Domes", $result->name);
        }

        public function testPostUpdateProfileValidationError()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['name'] = '';
            $this->userController->postUpdateProfile();

            $this->expectOutputRegex("[Location: /]");

            $this->expectOutputRegex("[Profile]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bram]");
            $this->expectOutputRegex("[Name]");
            $this->expectOutputRegex("[Id, Name can not blank]");
        }

        public function testUpdatePassword()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $this->userController->updatePassword();

            $this->expectOutputRegex("[Passowrd]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bram]");
        }

        public function testPostUpdatePasswordSuccess()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'rahasia';
            $_POST['newPassword'] = 'domes';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Location: /]");

            $result = $this->userRepository->findById($user->id);
            self::assertTrue(password_verify("domes", $result->password));
        }

        public function testPostUpdatePasswordValidationError()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = '';
            $_POST['newPassword'] = '';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Passowrd]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bram]");
            $this->expectOutputRegex("[Id, Old Password, New Password can not blank]");
        }

        public function testPostUpdatePasswordWrongOldPassword()
        {
            $user = new User();
            $user->id = "bram";
            $user->name = "Bram";
            $user->password = password_hash("rahasia", PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $session = new Session();
            $session->id = uniqid();
            $session->userId = $user->id;

            $this->sessionRepository->save($session);

            $_COOKIE[SessionService::$COOKIE_NAME] = $session->id;

            $_POST['oldPassword'] = 'salah';
            $_POST['newPassword'] = 'domes';

            $this->userController->postUpdatePassword();

            $this->expectOutputRegex("[Passowrd]");
            $this->expectOutputRegex("[Id]");
            $this->expectOutputRegex("[bram]");
            $this->expectOutputRegex("[Old password is wrong]");
        }
    }
}
