<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class AuthControllerException extends AbstractClassException
{
    public static function wrongCredentialsError(): self
    {
        return new self("Неввірний логін або пароль", Response::HTTP_UNAUTHORIZED);
    }

    public static function deactivatedAuthUserError(): self
    {
        return new self("Цього користувача було деактивовано", Response::HTTP_UNAUTHORIZED);
    }

    public static function hasNotRightsAuthUserError(): self
    {
        return new self("У вас немає прав доступу до системи", Response::HTTP_UNAUTHORIZED);
    }

    public static function notAuthUserError(): self
    {
        return new self("Ви не авторизовані", Response::HTTP_UNAUTHORIZED);
    }
}
