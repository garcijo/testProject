<?php

namespace UnitTest\Web\Domain;

use Web\Domain\UserMapper;
use Web\Domain\UserEntity;
use Slim\PDO\Database;

class UserMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testLoginUserSuccess()
    {
        $db = new Database('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = uniqid('pass');
        $hash = password_hash($testPassword, PASSWORD_DEFAULT);
        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $hash]);
        $userMapper->createUser($testName, $testUserName, $hash);
        $testLog = $userMapper->loginUser($testUserName, $testPassword);

        $this->assertEquals($expectedUser, $testLog);

        //remove dummy data
        $userMapper->removeUser($testUserName);
    }

    public function testLoginUserFail()
    {
        $db = new Database('mysql:dbname=test', 'vagrant', 'vagrant');
        //$db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testPassword = uniqid('fakePass');
        $testUserName = uniqid('fakeEmail');
        $expectedUser = new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        $testLog = $userMapper->loginUser($testUserName, $testPassword);

        $this->assertEquals($expectedUser, $testLog);
    }

    public function testSearchUserSuccess()
    {
        $db = new Database('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = password_hash(uniqid('pass'), PASSWORD_DEFAULT);
        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $testPassword]);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testSearch = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $testSearch);

        //remove dummy data
        $userMapper->removeUser($testUserName);
    }

    public function testSearchUserFail()
    {
        $db = new Database('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        $testSearch = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $testSearch);
    }

    public function testCreateUserSuccess()
    {
        $db = new Database('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = password_hash(uniqid('pass'), PASSWORD_DEFAULT);
        $testUserName = uniqid('email');
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $testPassword]);
        $searchUser = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $searchUser);

        //remove dummy data
        $userMapper->removeUser($testUserName);
    }
}
