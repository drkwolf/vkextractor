<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VkStandaloneTest extends TestCase
{
    protected function callRoute($method, $route, $data = [], $headers = [])
    {
        if ($this->token && !isset($headers['Authorization'])) {
            $headers['HTTP_Authorization'] = "Bearer: $this->token";
        }

        return $this->call(
            $method,
            "/api$route",
            [],
            [],
            [],
            $headers,
            json_encode($data)
        );
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testAuthentication() {

        $server = [
            'PHP_AUTH_USER' => 'drkwolf@gmail.com',
            'PHP_AUTH_PW' => 'malika123'
        ];
        $params  = [
            'email' => 'drkwolf@gmail.com',
            'password' => 'malika123'
        ];

        $request = $this->json('POST', '/api/authenticate', $params)
            ->dump()
//            ->seeJson([
//                'created' => true,
//            ])
        ;
    }
}
