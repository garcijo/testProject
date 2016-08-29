<?php

namespace UnitTest\Web\Domain;

use Web\Domain\UserMapper;
use Web\Domain\UserEntity;

class UserMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testLoginUserSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $testPassword]);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testLog = $userMapper->loginUser($testUserName, $testPassword);

        $this->assertEquals($expectedUser, $testLog);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $testLog;
    }

    public function testLoginUserFail()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testPassword = md5(uniqid('fakePass'));
        $testUserName = uniqid('fakeEmail');
        $expectedUser = new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        $testLog = $userMapper->loginUser($testUserName, $testPassword);

        $this->assertEquals($expectedUser, $testLog);

        return $testLog;
    }

    public function testSearchUserSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $testPassword]);
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $testSearch = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $testSearch);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $testSearch;
    }

    public function testSearchUserFail()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testUserName = uniqid('email');
        $expectedUser = new UserEntity(['email' => '', 'name' => '', 'password' => '']);
        $testSearch = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $testSearch);

        return $testSearch;
    }

    public function testCreateUserSuccess()
    {
        $db = new \PDO('mysql:dbname=test', 'vagrant', 'vagrant');
        $userMapper = new UserMapper($db);

        $testName = uniqid('name');
        $testPassword = md5(uniqid('pass'));
        $testUserName = uniqid('email');
        $userMapper->createUser($testName, $testUserName, $testPassword);
        $expectedUser = new UserEntity(['email' => $testUserName, 'name' => $testName, 'password' => $testPassword]);
        $searchUser = $userMapper->searchUser($testUserName);

        $this->assertEquals($expectedUser, $searchUser);

        //remove dummy data
        $userMapper->removeUser($testUserName);

        return $searchUser;
    }
}
