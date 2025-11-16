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

    public static function loginUser(mysqli $connection, string $email, string $password){

        $user = User::findByEmail($connection, $email);

        if(!$user){
            return ResponseService::response(404, "User not found");
        }

        if($user->getIsActive() == 0){
            return ResponseService::response(403, "User is inactive");
        }

        $checkpass = password_verify($password, $user->getPassword());

         if(!$checkpass){
            return   ResponseService::response(401, "Invalid Credentials");
        }
        
        // $token = $user->getAuthToken();

        $userData = [
            "id" => $user->getId(),
            "email" => $user->getEmail(),
            "role" => $user->getRole(),
            "token" => $user->getAuthToken(),
        ];

        return ResponseService::response(200, "Loging Successful", $userData);
    } 


}

?>