<?php
namespace tests\functional;

use FunctionalTester;

class LoginCest
{
    public function loginWithValidCredentials(FunctionalTester $I)
    {
        $I->sendPOST('/api/login', [
            'email' => 'admin@gmail.com',
            'password' => '12345',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => true]);
        $I->seeResponseMatchesJsonType([
            'token' => 'string',
            'user_id' => 'integer',
            'username' => 'string',
        ]);
    }

    public function loginWithInvalidCredentials(FunctionalTester $I)
    {
        $I->sendPOST('/api/login', [
            'email' => 'admin@gmail.com',
            'password' => 'wrongpassword',
        ]);
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => false]);
        $I->seeResponseContains('Email atau password salah');
    }
}
