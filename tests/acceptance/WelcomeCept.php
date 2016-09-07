<?php

use Facebook\WebDriver\WebDriverElement;

$I = new AcceptanceTester($scenario);

#####################################Login page#####################################
$I->wantTo('ensure that login page works');
$I->amOnPage('/login');
$I->see('Hello');
$I->see('Please enter your username to log in');
$I->see('Login');
$I->seeElement('input', ['name' => 'email']);
$I->seeElement('input', ['name' => 'password']);

$I->wantTo('Test incorrect login');
$rand = time() . '-' . rand(1, 100000);
$I->fillField('//form[2]/input[1]', $rand.'user');
$I->fillField('//form[2]/input[2]', '1234');
$I->click('login');
$I->see('Incorrect login!');
$I->seeInCurrentUrl('signin');

$I->wantTo('Test correct login');
$I->fillField('//form[2]/input[1]', 'user');
$I->fillField('//form[2]/input[2]', 'user');
$I->click('login');
$I->seeInCurrentUrl('home');
$I->click('Logout');

#####################################Signup page#####################################
$I->wantTo('Ensure that signup page works');
$I->see('Create an account');
$I->click('Create an account');
$I->seeElement('input', ['name' => 'email']);
$I->seeElement('input', ['name' => 'password']);
$I->seeElement('input', ['name' => 'name']);

$I->wantTo('Test correct signup');
$rand = time() . '-' . rand(1, 100000);
$I->fillField('//form[1]/input[1]', $rand.'name');
$I->fillField('//form[1]/input[2]', '1234');
$I->fillField('//form[1]/input[3]', $rand.'email');
$I->click('create');
$I->seeInCurrentUrl('home');
$I->see('Hello '.$rand.'name');
$I->click('Logout');

#####################################Home page#####################################
$I->wantTo('Ensure that home page works');
$I->fillField('//form[2]/input[1]', 'user');
$I->fillField('//form[2]/input[2]', 'user');
$I->click('login');
$I->seeInCurrentUrl('home');
$I->see('Hello user');
$I->see('Home');
$I->see('My Music');
$I->see('Logout');
$I->seeElement('audio');
$I->seeElement('#like');
$I->seeElement('#dislike');
$I->seeElement('.cover');
$I->seeElement('#play_pause');
$I->seeElement('.play');
$I->click('#play_pause');
$I->seeElement('#img_link');
$I->seeElement('#song_name');
$I->seeElement('#song_artist');
$songId = $I->grabAttributeFrom('#song_link','src');
$I->click('#like');


#####################################Music page#####################################
$I->wantTo('Ensure that music page works');
$I->click('My Music');
$I->seeInCurrentUrl('music');
$I->seeElement('#music');
$I->see('Song');
$I->see('Artist');
$I->see('Album');
$I->seeInSource('<tr id="'.$songId);