<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerTest extends WebTestCase
{
    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function getClient($method, $url, $params = [], $headers = [])
    {
        $client = static::createClient();

        $client->request($method, $url, $params, [], $headers);

        return $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function isSuccessful($method, $url, $params = [], $headers = [])
    {
        $client = $this->getClient($method, $url, $params, $headers);

        $this->assertTrue($client->getResponse()->isSuccessful());

        return $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function notFound($method, $url, $params = [], $headers = [])
    {
        $client = $this->getClient($method, $url, $params, $headers);

        $this->assertTrue($client->getResponse()->isNotFound());

        return $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function badRequest($method, $url, $params = [], $headers = [])
    {
        $client = $this->getClient($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

        return $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function unauthorized($method, $url, $params = [], $headers = [])
    {
        $client = $this->getClient($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());

        return $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function forbidden($method, $url, $params = [], $headers = [])
    {
        $client = $this->getClient($method, $url, $params, $headers);

        $this->assertTrue($client->getResponse()->isForbidden());

        return $client;
    }
}
