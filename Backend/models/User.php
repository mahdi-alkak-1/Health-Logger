<?php 

include('Model.php');
include('../config/connection.php');
class User extends Model{
    private int $id;
    private string $email;
    private string $password;
    private string $role;
    private int $is_active; 
    private ? string $auth_token;
    private string $created_at;
    private string $updated_at;


    protected static string $table = "users";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->role = $data["role"];
        $this->is_active = $data["is_active"];
        $this->auth_token = $data["auth_token"] ?? null;
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
    }
    
    public static function findByEmail($connection, $email){
        $sql = sprintf("SELECT * FROM users WHERE email = ? LIMIT 1");
        $query = $connection->prepare($sql);
        $query->bind_param('s', $email);
        $query->execute ();

        $data = $query->get_result()->fetch_assoc();
        
        return  $data ? new User($data) : null ;    
    }
    
    // public static function updateToken(){  //// i may implement it later its impo for security issues

    // }

    public function getId(){
        return $this->id;
    }

    public function setEmail(string $email){
        $this->email = $email;
    }
    public function getEmail(){
        return $this->email;
    }

     public function setPassword(string $password){
        $this->password = $password;
    }
    public function getPassword(){
        return $this->password;
    }

    public function setRole(string $role){
        $this->role = $role;
    }
    public function getRole(){
        return $this->role;
    }

    public function setIsActive(int $isactive){
        $this->is_active = $isactive;
    }
    public function getIsActive(){
        return $this->is_active;
    }

    public function setCreatedAt(string $created){
        $this->created_at = $created;
    }
    public function getCreatedAt(){
        return $this->created_at;
    }

    public function setUpdatedAt(string $updated){
        $this->updated_at = $updated;
    }
    public function getUpdatedAt(){
        return $this->updated_at;
    }

    public function setAuthToken(string $token){
        $this->auth_token = $token;
    }
    public function getAuthToken(){
        return $this->auth_token;
    }

    public function __toString(){
        return $this->id . " | " . $this->email . " | " . $this->password. " | " . $this->role . " | " . $this->is_active . " | " . $this->created_at . " | " . $this->updated_at . " | " . $this->auth_token;
    }
    
    public function toArray(){
        return ["id" => $this->id, "email" => $this->email, "password" => $this->password, "role" => $this->role, "is_active" => $this->is_active, "created_at" => $this->created_at, "updated_at" => $this->updated_at, "auth_token" => $this->auth_token];
    }
}


?>