<?php

namespace Test\PhpDevCommunity\Session;

use PhpDevCommunity\Session\Storage\NativeSessionStorage;
use PhpDevCommunity\UniTester\TestCase;

final class NativeSessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    protected function execute(): void
    {
        $session = new NativeSessionStorage();
        $session['username'] = 'myName';
        $this->assertTrue($session->has('username'));
        $session['role'] = 'ADMIN';
        $this->assertTrue($session->has('role'));
        $this->assertStrictEquals('myName', $session->get('username'));

        $article = [
            'title' => 'TV',
            'description' => 'lorem',
            'price' => 199.80
        ];
        $session->put('article',$article);
        $this->assertStrictEquals($article, $session->get('article'));
        $this->assertTrue(is_float($session->get('article')['price']));
        $this->assertTrue(count($session->all()) === 3);

        $this->assertStrictEquals(null, $session->get('email'));
        $this->assertStrictEquals('dev@phpdevcommunity.com', $session->get('email', 'dev@phpdevcommunity.com'));
    }
}
