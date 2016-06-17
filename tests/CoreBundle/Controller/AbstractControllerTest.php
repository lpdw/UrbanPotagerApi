<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerTest extends WebTestCase
{
    const ADMIN = ['username' => 'admin', 'password' => 'admin'];
    const USER1 = ['username' => 'user1', 'password' => 'userpass'];
    const USER2 = ['username' => 'user2', 'password' => 'userpass'];

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    private function request($method, $url, $params = [], $headers = [])
    {
        if (is_null($this->client)) {
            $this->client = static::createClient();
        }

        $this->client->request($method, $url, $params, [], $headers);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isSuccessful($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue($this->client->getResponse()->isSuccessful(),
            sprintf('Status code is %d instead of 2xx', $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isNotFound($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue(
                $this->client->getResponse()->isNotFound(),
                sprintf('Status code is %s instead of 404', $this->client->getResponse()->getStatusCode())
            );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isBadRequest($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isUnauthorized($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isForbidden($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue(
            $this->client->getResponse()->isForbidden(),
            sprintf('Status code is %s instead of 403', $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isConflict($method, $url, $params = [], $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    protected function getResponseContent($key = null)
    {
        $json = json_decode($this->client->getResponse()->getContent(), true);

        if (is_null($json)) {
            $this->assertTrue(false, 'Json is invalid');
            return null;
        }

        return (!is_null($key)) ? $json[$key] : $json;
    }

    /**
     * @param string $username
     * @param string $password
     * @return array|null
     */
    protected function getHeaderConnect($username, $password, $checkUser = true)
    {
        $this->request(Request::METHOD_POST, '/token', ['username' => $username, 'password' => $password]);
        $response = $this->getResponseContent();

        if (Response::HTTP_OK != $this->client->getResponse()->getStatusCode()) {
            if ($checkUser) {
                $this->assertTrue(false, 'Bad credential');
            }

            return null;
        }

        return ['HTTP_AUTHORIZATION' => 'Bearer ' . $response['token']];
    }

    /**
     * @return string
     */
    protected function fakeSlug()
    {
        return 'this-slug-does-not-exist';
    }
}
