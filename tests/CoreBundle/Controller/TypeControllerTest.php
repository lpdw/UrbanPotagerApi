<?php

namespace Tests\CoreBundle\Controller;

use CoreBundle\Entity\Type;
use Symfony\Component\HttpFoundation\Request;

class TypeControllerTest extends AbstractControllerTest
{
    // TODO setup with few types ?

    const PREFIX_URL = '/types';

    // === GET ===
    public function testCGetSuccessful()
    {
        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL);
    }

    public function testGetSuccessful()
    {
        $slug = "water-level";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isSuccessful(Request::METHOD_GET, $url);
    }

    public function testGetNotFound()
    {
        $url  = self::PREFIX_URL . '/' . $this->fakeSlug();
        $this->isNotFound(Request::METHOD_GET, $url);
    }

    public function testGetLocale()
    {
        $slug = "water-level";
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_GET, $url . '?_locale=en');
        $entityEN = $this->getResponseContent('type');

        $this->isSuccessful(Request::METHOD_GET, $url . '?_locale=fr');
        $entityFR = $this->getResponseContent('type');

        // test locale
        $this->assertTrue(
            $entityEN['name'] != $entityFR['name'] &&
            $entityEN['slug'] == $entityFR['slug']
        );
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $params = [
            'name' => 'My new type',
            'description' => 'My description',
            'min' => 0,
            'max' => 100,
            'type' => Type::SENSOR,
        ];

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
        $newType = $this->getResponseContent('type');

        $url = self::PREFIX_URL . '/' . $newType['slug'];
        $this->isSuccessful(Request::METHOD_GET, $url);
        $newType = $this->getResponseContent('type');

        $this->assertEquals(
                $newType['name'], $params['name'],
                sprintf('inserted %s != new %s', $newType['name'], $params['name'])
            );
    }

    public function testPostBadRequest()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $params = [
            'name' => 'Missing type & min',
            'description' => 'test bad request & min',
            'max' => 100,
        ];

        $this->isBadRequest(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
    }

    public function testPostUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_POST, self::PREFIX_URL);
    }

    public function testPostForbidden()
    {
        $user = self::USER;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isForbidden(Request::METHOD_POST, self::PREFIX_URL, [], $header);
    }

    // === PATCH ===
    public function testPatchSuccessful()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $params = [
            'name' => 'new name',
        ];

        $slug = "my-new-type";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);
        $editType = $this->getResponseContent('type');

        $this->assertEquals(
            $editType['name'], $params['name'],
            sprintf('inserted %s != new %s', $editType['name'], $params['name'])
        );

        $this->assertEquals(
            $editType['slug'], $slug,
            sprintf('slug %s != old %s', $editType['name'], $slug)
        );
    }

    public function testPatchBadRequest()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $params = [
            'max' => 1,
            'min' => 100,
        ];

        $slug = "my-new-type";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isBadRequest(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchUnauthorized()
    {
        $slug = "my-new-type";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }

    public function testPatchForbidden()
    {
        $user = self::USER;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = "my-new-type";
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
    public function testDeleteSuccfessful()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $slug = "my-new-type";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);

        $this->isNotFound(Request::METHOD_GET, $url);
    }

    public function testDeleteUnauthorized()
    {
        $slug = "water-level";
        $url = self::PREFIX_URL . '/' . $slug;
        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = "water-level";
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isForbidden(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteNotFound()
    {
        $admin = self::ADMIN;
        $header = $this->getHeaderConnect($admin['username'], $admin['password']);

        $url = self::PREFIX_URL . '/' . $this->fakeSlug();
        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }
}
