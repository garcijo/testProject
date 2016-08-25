<?php

namespace Web\Domain;

class UserMapper extends Mapper
{
    /**
     * Read a username and password and verify that it's a valid user.
     *
     * @param string $user_email The current user's username
     * @param string $user_pass  The current user's password
     */
    public function loginUser(string $user_email, string $user_pass):UserEntity
    {
        $email = $user_email;
        $pass = $user_pass;
        $sql = 'SELECT name, email, password
            FROM user WHERE email = :user_email AND password = PASSWORD(:user_pass)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_email' => $email, 'user_pass' => $pass]);
        if ($rs = $stmt->fetch()) {
            return new UserEntity($rs);
        } else {
            return new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        }
    }

    /**
     * Accept a username and look it up in the database to verify if it exists.
     *
     * @param string $user_email The current user's username
     */
    public function searchUser(string $user_email):UserEntity
    {
        $email = $user_email;
        $sql = 'SELECT * FROM user WHERE email = :user_email';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_email' => $email]);
        if ($rs = $stmt->fetch()) {
            return new UserEntity($rs);
        } else {
            return new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        }
    }

    /**
     * Accept a username, name, and password
     * and create a new user with the given fields.
     *
     * @param string $user_name  The new user's name
     * @param string $user_email The new user's username
     * @param string $user_pass  The new user's password
     */
    public function createUser(string $user_name, string $user_email, string $user_pass)
    {
        $sql = 'INSERT INTO user
            (name, email, password) VALUES
            (:name, :email, PASSWORD(:password))';
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'name' => $user_name,
            'email' => $user_email,
            'password' => $user_pass,
        ]);
        if (!$result) {
            throw new Exception('Could not register user!');
        }
    }
}
