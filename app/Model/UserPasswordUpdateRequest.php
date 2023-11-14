<?php

namespace Bramasta\Belajar\PHP\MVC\Model;

class UserPasswordUpdateRequest
{
    public ?string $id = null;
    public ?string $oldPassowrd = null;
    public ?string $newPassword = null;
}