<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
    public function __construct(private Database $db)
    {
    }

    public function isEmailTaken(string $email)
    {
        $emailCount = $this->db->query(
            "select COUNT(*) from users where email = :email",
            [
                'email' => $email
            ]
        )->count();

        if ($emailCount > 0) {
            throw new ValidationException(["email" => "email taken"]);
        }
    }

    public function create(array $formData)
    {
        $hashedPass = password_hash($formData["password"], PASSWORD_BCRYPT, ["cost" => 12]);

        $this->db->query(
            "insert into users (email, password, age, country, social_media_url) 
            values(:email, :password, :age, :country, :url)",
            [
                "email" => $formData["email"],
                "password" => $hashedPass,
                "age" => $formData["age"],
                "country" => $formData["country"],
                "social_media_url" => $formData["socialMediaUrl"]
            ]
        );
    }

    public function login(array $formData)
    {
        $user = $this->db->query(
            "select * from users where email = :email",
            [
                "email" => $formData["email"]
            ]
        )->find();

        $passwordsMatch = password_verify($formData["password"], $user["password"] ?? '');

        if (!$user || !$passwordsMatch) {
            throw new ValidationException([
                "password" => ["invalid credentials"]
            ]);
        }

        session_regenerate_id();

        $_SESSION["user"] = $user["id"];
    }

    public function logout()
    {
        unset($_SESSION["user"]);
        session_regenerate_id();
    }
}
