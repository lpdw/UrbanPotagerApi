<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class ConfigurationControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/configurations';

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'super configuration',
            'description' => 'description',
            'lightTreshold' => 50.21,
            'lightingStart' => ['hour' => 4, 'minute' => 34],
            'lightingEnd' => ['hour' => 5, 'minute' => 34],
            'isWateringActive' => true,
            'wateringStart' => ['hour' => 4, 'minute' => 34],
            'wateringEnd' => ['hour' => 4, 'minute' => 36],
        ];

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
        $newConfiguration = $this->getResponseContent('configuration');

        $url = self::PREFIX_URL . '/' . $newConfiguration['slug'];
        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
        $newConfiguration = $this->getResponseContent('configuration');

        $this->assertEquals(
            $newConfiguration['name'], $params['name'],
            sprintf('inserted %s != new %s', $newConfiguration['name'], $params['name'])
        );
    }

    public function testPostBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'super configuration',
            'description' => 'description',
            'lightTreshold' => 50.21,
            'lightingStart' => ['hour' => 4, 'minute' => 34],
            'isWateringActive' => true,
            'wateringStart' => ['hour' => 4, 'minute' => 34],
            'wateringEnd' => ['hour' => 4, 'minute' => 36],
        ];

        $this->isBadRequest(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
    }

    public function testPostUnauthorized()
    {
        $params = [
            'name' => 'super configuration',
            'description' => 'description',
            'lightTreshold' => 50.21,
            'lightingStart' => ['hour' => 4, 'minute' => 34],
            'isWateringActive' => true,
            'wateringStart' => ['hour' => 4, 'minute' => 34],
            'wateringEnd' => ['hour' => 4, 'minute' => 36],
        ];

        $this->isUnauthorized(Request::METHOD_POST, self::PREFIX_URL, $params);
    }

    // === GET ===
    public function testCGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL, [], $header);
    }

    public function testCGetUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::PREFIX_URL);
    }

    public function testGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'super-configuration';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetUnauthorized()
    {
        $slug = 'super-configuration';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'super-configuration';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isForbidden(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::PREFIX_URL . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    // === PATCH ===
    public function testPatchSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'updated name',
        ];

        $slug = 'super-configuration';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);

        $url = self::PREFIX_URL . '/updated-name';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);

        $newConfiguration = $this->getResponseContent('configuration');

        $this->assertTrue(
            $newConfiguration['name'] == $params['name'],
            sprintf('[inserted] %s != [new] %s', $newConfiguration['name'], $params['name'])
        );
    }

    public function testPatchBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => '',
        ];

        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isBadRequest(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchUnauthorized()
    {
        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_PATCH, $url, [], []);
    }

    public function testPatchForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isForbidden(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::PREFIX_URL . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_PATCH, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteUnauthorized()
    {
        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isForbidden(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::PREFIX_URL . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);
        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }
}
