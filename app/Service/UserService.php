<?php

namespace Bramasta\Belajar\PHP\MVC\Service;

use Bramasta\Belajar\PHP\MVC\config\Database;
use Bramasta\Belajar\PHP\MVC\Exception\ValidationException;
use Bramasta\Belajar\PHP\MVC\Model\UserRegisterRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserRegisterResponse;
use Bramasta\Belajar\PHP\MVC\Repository\UserRepository;
use Bramasta\Belajar\PHP\MVC\Domain\User;
use Bramasta\Belajar\PHP\MVC\Model\UserLoginRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserLoginResponse;
use Bramasta\Belajar\PHP\MVC\Model\UserPassowrdUpdateResponse;
use Bramasta\Belajar\PHP\MVC\Model\UserPasswordUpdateRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserProfileUpdateRequest;
use Bramasta\Belajar\PHP\MVC\Model\UserProfileUpdateResponse;
use PHPUnit\Util\Xml\Validator;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserRegisterRequest $request): UserRegisterResponse
    {
        $this->validateRegistrationRequest($request);

        try{
            Database::beginTransaction();
            $user = $this->userRepository->findById($request->id);
            if($user != null){
                throw new ValidationException("User Id already exists");
            }

            $user = new User();
            $user->id = $request->id;
            $user->name = $request->name;
            $user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($user);

            $response = new UserRegisterResponse();
            $response->user = $user;
            
            Database::commitTransaction();
            return $response;   
        }catch (\Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateRegistrationRequest(UserRegisterRequest $request)
    {
        if ($request->id == null || $request->name == null || $request->password == null ||
        trim($request->id) == "" || trim($request->name) == "" || trim($request->password) == "") {
            throw new ValidationException("Id, Name, Password can not blank");
        }
    }

    public function login(UserLoginRequest $request): UserLoginResponse
    {
        $this->validateUserLoginRequest($request);

        $user = $this->userRepository->findById($request->id);
        if($user == null){
            throw new ValidationException("Id or password is wrong");
        }

        if (password_verify($request->password, $user->password)) {
            $response = new UserLoginResponse();
            $response->user = $user;
            return $response;
        }else {
            throw new ValidationException("Id or password is wrong");
        }
    }

    private function validateUserLoginRequest(UserLoginRequest $request)
    {
        if ($request->id == null  || $request->password == null ||
        trim($request->id) == ""  || trim($request->password) == "") {
            throw new ValidationException("Id, Password can not blank");
        }
    }

    public function updateProfile(UserProfileUpdateRequest $request): UserProfileUpdateResponse 
    {
        $this->validateUserProfileUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null){
                throw new ValidationException("User is not found");
            } 

            $user->name = $request->name;
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserProfileUpdateResponse();
            $response->user = $user;
            return $response;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }   

    private function validateUserProfileUpdateRequest(UserProfileUpdateRequest $request)
    {
        if ($request->id == null  || $request->name == null ||
        trim($request->id) == ""  || trim($request->name) == "") {
            throw new ValidationException("Id, Name can not blank");
        }
    }

    public function updatePassword(UserPasswordUpdateRequest $request): UserPassowrdUpdateResponse
    {
        $this->validateUserPasswordUpdateRequest($request);

        try{
            Database::beginTransaction();

            $user = $this->userRepository->findById($request->id);
            if ($user == null) {
                throw new ValidationException("User is not found");
            }

            if (!password_verify($request->oldPassowrd, $user->password)) {
                throw new ValidationException("Old password is wrong");
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->update($user);

            Database::commitTransaction();

            $response = new UserPassowrdUpdateResponse();
            $response->user = $user;
            return $response; 
        }catch (\Exception $exception){
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserPasswordUpdateRequest(UserPasswordUpdateRequest $request)
    {
        if ($request->id == null || $request->oldPassowrd == null || $request->newPassword == null ||
        trim($request->id) == "" || trim($request->oldPassowrd) == "" || trim($request->newPassword) == "") {
            throw new ValidationException("Id, Old Password, New Password can not blank");
        }
    }
}