<?php

namespace Web\Domain;

class UserMapper extends Mapper
{
    /**
     * Read a username and password and verify that it's a valid user.
     *
     * @param string $userEmail The current user's username
     * @param string $userPass  The current user's password
     *
     * @return UserEntity
     */
    public function loginUser(string $userEmail, string $userPass):UserEntity
    {
        $email = $userEmail;
        $pass = $userPass;

        $sql = 'SELECT name, email, password
            FROM user WHERE email = :userEmail';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userEmail' => $email]);
        if ($rs = $stmt->fetch()) {
            if (password_verify($pass, $rs['password'])) {
                return new UserEntity($rs);
            }
            else {
                return new UserEntity(['email' => '', 'name' => '', 'password' => '']);
            }
        } else {
            return new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        }
    }

    /**
     * Accept a username and look it up in the database to verify if it exists.
     *
     * @param string $userEmail The current user's username
     *
     * @return UserEntity
     */
    public function searchUser(string $userEmail):UserEntity
    {
        $sql = 'SELECT * FROM user WHERE email = :userEmail';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userEmail' => $userEmail]);
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
     * @param string $userName  The new user's name
     * @param string $userEmail The new user's username
     * @param string $userPass  The new user's password
     */
    public function createUser(string $userName, string $userEmail, string $userPass)
    {
        $sql = 'INSERT INTO user
            (name, email, password) VALUES
            (:name, :email, :password)';
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPass,
        ]);
        if (!$result) {
            throw new \Exception('Could not register user!');
        }
    }

    /**
     * Accept a username and look it up in the database to verify if it exists.
     *
     * @param string $userEmail The current user's username
     */
    public function removeUser(string $userEmail)
    {
        $sql = 'DELETE FROM user WHERE email = :userEmail';
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['userEmail' => $userEmail]);
        if (!$result) {
            throw new \Exception('Could not delete user!');
        }
    }
}
