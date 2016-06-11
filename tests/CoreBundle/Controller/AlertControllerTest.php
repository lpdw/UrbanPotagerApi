<?php

namespace Tests\CoreBundle\Controller;

use CoreBundle\Entity\Alert;
use Symfony\Component\HttpFoundation\Request;

class AlertControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/alerts';

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'threshold' => 15.54,
            'comparison' => Alert::$OPERATOR['equal'],
            'name' => 'alert name',
            'description' => 'description',
            'message' => 'message',
            'type' => 'water-level',
        ];

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params, $header);
    }

    public function testPostBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'name' => 'alert name',
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

        $slug = 'alert-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetUnauthorized()
    {
        $slug = 'alert-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'alert-name';
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

        $slug = 'alert-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $params = [
            'name' => 'updated name',
        ];

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $params = [
            'comparison' => 999,
        ];

        $this->isBadRequest(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchUnauthorized()
    {
        $slug = 'updated-name';
        $url = self::PREFIX_URL . '/' . $slug;

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
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
    }
}
