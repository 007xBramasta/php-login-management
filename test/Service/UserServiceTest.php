<?php

namespace Bramasta\Belajar\PHP\MVC\Service;

use Bramasta\Belajar\PHP\MVC\config\Database;
use Bramasta\Belajar\PHP\MVC\Exception\ValidationException;
use Bramasta\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;
use Bramasta\Belajar\PHP\MVC\Domain\User;
use Bramasta\Belajar\PHP\MVC\Model\UserLoginRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Bramasta\Belajar\PHP\MVC\Repository\SessionRepository;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class UserServiceTest extends TestCase
{
     private UserService $userService;
     private UserRepository $userRepository;
     private SessionRepository $sessionRepository;

     protected function setUp(): void
     {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new UserService($this->userRepository);
        $this->sessionRepository = new SessionRepository($connection);

        $this->sessionRepository->deleteAll();      
        $this->userRepository->deleteAll();
     }

     public function  testRegisterSuccess()
     {
        $request =  new UserRegisterRequest();
        $request->id = "bram";
        $request->name = "tio";
        $request->password = "rahasia";

        $response = $this->userService->register($request);

        self::assertEquals($request->id, $response->user->id);
        self::assertEquals($request->name, $response->user->name);
        self::assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
     }

     public function testFailed()
     {
        $this->expectException(ValidationException::class);

        $request =  new UserRegisterRequest();
        $request->id = "";
        $request->name = "";
        $request->password = "";

        $this->userService->register($request);
     }

     public function testDuplicate()
     {
        $user =  new User();
        $user->id = "bram";
        $user->name = "tio";
        $user->password = "rahasia";

        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request =  new UserRegisterRequest();
        $request->id = "bram";
        $request->name = "tio";
        $request->password = "rahasia";

        $this->userService->register($request); 

     }

     public function testLoginNotFound()
     {
         $this->expectException(ValidationException::class);

         $request = new UserLoginRequest();
         $request->id = "bram";
         $request->password = "Bram";

         $this->userService->login($request);
     }

     public function testLoginWrongPassword()
     {
         $user = new User();
         $user->id = "bram";
         $user->name = "Bram";
         $user->password = password_hash("bram", PASSWORD_BCRYPT);

         $this->expectException(ValidationException::class);

         $request = new UserLoginRequest();
         $request->id = "bram";
         $request->password = "salah";

         $this->userService->login($request);
     }

     public function testLoginSuccess()
     {
         $user = new User();
         $user->id = "bram";
         $user->name = "Bram";
         $user->password = password_hash("bram", PASSWORD_BCRYPT);

         $this->expectException(ValidationException::class);

         $request = new UserLoginRequest();
         $request->id = "bram";
         $request->password = "bram";

         $response = $this->userService->login($request);

         self::assertEquals($request->id, $response->user->id);
         self::assertTrue(password_verify($request->password, $response->user->password));
     }

     public function testUpdateSuccess()
     {
         $user = new User();
         $user->id = "bram";
         $user->name = "Bram";
         $user->password = password_hash("bram", PASSWORD_BCRYPT);
         $this->userRepository->save($user);

         $request = new UserProfileUpdateRequest();   
         $request->id = "bram";
         $request->name = "Domes";

         $this->userService->updateProfile($request);

         $result = $this->userRepository->findById($user->id);

         assertEquals($request->name, $result->name);
     }

     public function testValidationError()
     {
         $this->expectException(ValidationException::class);

         $request = new UserProfileUpdateRequest();   
         $request->id = "";
         $request->name = "";

         $this->userService->updateProfile($request);
     }

     public function testUpdateNotFound()
     {
         $this->expectException(ValidationException::class);
         
         $request = new UserProfileUpdateRequest();   
         $request->id = "bram";
         $request->name = "Domes";

         $this->userService->updateProfile($request);
     }

     public function testUpdatePasswordSuccess()
     {
         $user = new User();
         $user->id = "bram";
         $user->name = "Bram";
         $user->password = password_hash("bram", PASSWORD_BCRYPT);
         $this->userRepository->save($user);

         $request = new UserPasswordUpdateRequest();
         $request->id = "bram";
         $request->oldPassowrd = "bram";
         $request->newPassword = "new";

         $this->userService->updatePassword($request);

         $result = $this->userRepository->findById($user->id);
         self::assertTrue(password_verify($request->newPassword, $result->password));
     }

     public function testUpdatePasswordValidationError()
     {
         $this->expectException(ValidationException::class);
         
         $request = new UserPasswordUpdateRequest();
         $request->id = "bram";
         $request->oldPassowrd = "";
         $request->newPassword = "";

         $this->userService->updatePassword($request);
     }   

     public function testUpdatePasswordWrongOldPaswword()
     {
         $this->expectException(ValidationException::class);
         
         $user = new User();
         $user->id = "bram";
         $user->name = "Bram";
         $user->password = password_hash("bram", PASSWORD_BCRYPT);
         $this->userRepository->save($user);

         $request = new UserPasswordUpdateRequest();
         $request->id = "bram";
         $request->oldPassowrd = "salah";
         $request->newPassword = "new";

         $this->userService->updatePassword($request);
     }

     public function testUpdatePasswordNotFound()
     {
         $this->expectException(ValidationException::class);
         
         $request = new UserPasswordUpdateRequest();
         $request->id = "bram";
         $request->oldPassowrd = "salah";
         $request->newPassword = "new";

         $this->userService->updatePassword($request);
     }

}