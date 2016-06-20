<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/users';
    const EMAIL = 'tmp-user@urbanpotager.com';

    private static $token;

    // === SETUP ===
    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail(self::EMAIL);

        $userManager->deleteUser($user);
    }

    // === HELPER ===
    private function getResetToken($email)
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($email);

        return $user->getConfirmationToken();
    }

    // === POST ===
    public function testPostTokenSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password'], true);

        $this->assertTrue(!is_null($header));
    }

    public function testPostTokenUnauthorized()
    {
        $header = $this->getHeaderConnect('nope', 'nope', false);

        $this->assertTrue(is_null($header));
    }

    public function testPostCreateSuccessful()
    {
        $params = [
            'username' => 'super username',
            'email' => self::EMAIL,
            'plainPassword' => 'coucou',
        ];

        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, $params);
    }

    public function testPostCreateBadRequest()
    {
        $params = [
            'username' => 'super username',
            'email' => 'not-an-email',
            'plainPassword' => 'coucou',
        ];

        $this->isBadRequest(Request::METHOD_POST, self::PREFIX_URL, $params);
    }

    public function testPostForgetPasswordSuccessful()
    {
        $params = [
            'username' => self::EMAIL,
        ];

        $this->isSuccessful(Request::METHOD_POST, '/forget-password', $params);

        self::$token = $this->getResetToken($params['username']);
    }

    public function testPostForgetPasswordBadRequest()
    {
        $this->isBadRequest(Request::METHOD_POST, '/forget-password');
    }

    public function testPostForgetPasswordNotFound()
    {
        $params = [
            'username' => 'not-exists@urbanpotager.com',
        ];

        $this->isNotFound(Request::METHOD_POST, '/forget-password', $params);
    }

    public function testPostForgetPasswordConflict()
    {
        $params = [
            'username' => self::EMAIL,
        ];

        $this->isConflict(Request::METHOD_POST, '/forget-password', $params);
    }

    public function testPostResetPasswordBadRequest()
    {
        $url = '/reset-password/' . self::$token;

        $this->isBadRequest(Request::METHOD_POST, $url);
    }

    public function testPostResetPasswordSuccessful()
    {
        $url = '/reset-password/' . self::$token;

        $params = [
            'plainPassword' => 'newpassword',
        ];

        $this->isSuccessful(Request::METHOD_POST, $url, $params);

        $header = $this->getHeaderConnect(self::EMAIL, $params['plainPassword'], true);

        $this->assertTrue(!is_null($header));
    }

    public function testPostResetPasswordNotFound()
    {
        $url = '/reset-password/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_POST, $url);
    }

    // === GET ===
    public function testGetMeSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/me';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetMeUnauthorized()
    {
        $url = '/me';

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetMeGardenSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/me/gardens';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetMeGardenUnauthorized()
    {
        $url = '/me/gardens';

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    // === PATCH ===
    public function testPatchMeSuccessful()
    {
        $header = $this->getHeaderConnect(self::EMAIL, 'newpassword');

        $url = '/me';

        $params = [
            'plainPassword' => 'supernewpassword',
        ];

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);

        $header = $this->getHeaderConnect(self::EMAIL, $params['plainPassword'], true);

        $this->assertTrue(!is_null($header));
    }

    public function testPatchMeBadRequest()
    {
        $header = $this->getHeaderConnect(self::EMAIL, 'supernewpassword');

        $url = '/me';

        $this->isBadRequest(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchMeUnauthorized()
    {
        $url = '/me';

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }
}
