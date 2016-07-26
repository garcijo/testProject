<?php
class UserMapper extends Mapper
{   
    public function loginUser(string $user_email,string $user_pass):UserEntity {
        $email = $user_email;
        $pass = $user_pass;
        $sql = "SELECT name, email, password
            FROM user WHERE email = :user_email AND password = PASSWORD(:user_pass)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user_email" => $email,"user_pass" => $pass,]);
        return new UserEntity($stmt->fetch());
    }
    
    public function searchUser(string $user_email):UserEntity {
        $email = $user_email;
        $sql = "SELECT * FROM user WHERE email = :user_email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user_email" => $email]);
        return new UserEntity($stmt->fetch());
    }
    
    public function createUser(string $user_name, string $user_email, string $user_pass) {
        $sql = "INSERT INTO user
            (name, email, password) VALUES
            (:name, :email, PASSWORD(:password))";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "name" => $user_name,
            "email" => $user_email,
            "password" => $user_pass,
        ]);
        if(!$result) {
            throw new Exception("Could not register user!");
        }
    }
}