<?php 
include('../config/connection.php');
include("../models/User.php");
include("ResponseService.php");
class AuthService{
    public static function registerUser(mysqli $connection, string $email, string $password){
        $exists = User::findByEmail($connection , $email);

        if($exists){
            return ResponseService::response(400, "Account Already created");
        }
        
        $hashedpass = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $data = [
                'email'=> $email, 
                'password'=> $hashedpass,
                'role' => 'user',
                'is_active' => 1,
                'auth_token' =>$token,
            ];

        $userId = User::create($connection, $data);

        $userData = [
                    "id"    => $userId,
                    "email" => $email,
                    "role"  => "user",
                    "token" => $token,
        ];

        return ResponseService::response(201,  "User registered", $userData);
    }


}

?>