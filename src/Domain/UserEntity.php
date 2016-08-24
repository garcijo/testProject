<?php


class UserEntity
{
    protected $email;
    protected $name;
    protected $password;
    
    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data)
    {
        $this->email = $data['email'];
        $this->name = $data['name'];
        $this->password = $data['password'];
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getPass()
    {
        return $this->password;
    }
}
