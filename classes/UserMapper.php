<?php
class UserMapper extends Mapper
{
    public function getUsers() {
        $sql = "SELECT name, email
            FROM user";
        $stmt = $this->db->query($sql);
        $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ComponentEntity($row);
        }
        return $results;
    }
    
    public function loginUser($user_email, $user_pass) {
        $email = $user_email;
        $pass = $user_pass;
        $sql = "SELECT name, email, password
            FROM user WHERE email = :user_email AND password = PASSWORD(:user_pass)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["user_email" => $email,"user_pass" => $pass,]);
        return new UserEntity($stmt->fetch());
    }
    
    public function createUser($user_name, $user_email, $user_pass) {
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