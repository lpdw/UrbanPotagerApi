<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class GardenControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/gardens';

    // === HELPER ===
    private function paramGarden($name, $isPublic = true)
    {
        return [
            'name' => $name,
            'description' => 'description garden',
            'isPublic' => $isPublic,
            'latitude' => 48.869679,
            'longitude' => 2.337256,
            'showLocation' => true,
            'country' => 'France',
            'city' => 'Paris',
            'zipCode' => '75002',
            'address1' => '14 Rue du 4 septembre',
        ];
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = $this->paramGarden('super garden public');

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
        $garden = $this->getResponseContent('garden');

        $url = self::PREFIX_URL . '/' . $garden['slug'];

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
        $garden = $this->getResponseContent('garden');

        $this->assertEquals(
            $garden['name'], $params['name'],
            sprintf('[garden] %s != [params] %s', $garden['name'], $params['name'])
        );
    }

    public function testPostBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'super garden',
        ];

        $this->isBadRequest(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
    }

    public function testPostUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_POST, self::PREFIX_URL);
    }

    // === GET ===
    public function testCGetSuccessful()
    {
        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL);
    }

    public function testGetOwnerSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'super-garden-public';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetPublicSuccessful()
    {
        $slug = 'super-garden-public';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_GET, $url);
    }

    public function testGetUnauthorized()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = $this->paramGarden('super garden', false);

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
        $garden = $this->getResponseContent('garden');

        $url = self::PREFIX_URL . '/' . $garden['slug'];

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::PREFIX_URL . '/' . 'super-garden';

        $this->isForbidden(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetNotFound()
    {
        $url = self::PREFIX_URL . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_GET, $url);
    }

    // === PATCH ===
    public function testPatchSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'updated name',
        ];

        $slug = 'super-garden-public';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);

        $url = self::PREFIX_URL . '/updated-name';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);

        $newGarden = $this->getResponseContent('garden');

        $this->assertTrue(
            $newGarden['name'] == $params['name'],
            sprintf('[inserted] %s != [new] %s', $newGarden['name'], $params['name'])
        );
    }

    public function testPatchBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => '',
        ];

        $slug = 'super-garden';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isBadRequest(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchUnauthorized()
    {
        $slug = 'super-garden';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }

    public function testPatchForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'super-garden';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isForbidden(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchNotFound()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $url = self::PREFIX_URL . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_PATCH, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteUnauthorized()
    {
        $slug = 'super-garden';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'super-garden';
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

        $slugs = ['super-garden', 'updated-name'];

        foreach ($slugs as $slug) {
            $url = self::PREFIX_URL . '/' . $slug;

            $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);
            $this->isNotFound(Request::METHOD_GET, $url, [], $header);
        }
    }
}
