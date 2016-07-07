<?php

namespace Tests\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tests\CoreBundle\Controller\AbstractControllerTest;

class MeControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/me';
    private static $user = ['username' => 'dummy user2', 'password' => 'coucou', 'email' => 'tmp-user@urbanpotager.com'];

    // === SETUP ===
    public static function setUpBeforeClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setEmail(self::$user['email'])
            ->setUsername(self::$user['username'])
            ->setPlainPassword(self::$user['password'])
            ->setEnabled(true);

        $userManager->updateUser($user, true);
    }

    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');

        $user = $userManager->findUserByEmail(self::$user['email']);

        $userManager->deleteUser($user);
    }

    // === GET ===
    public function testGetMeSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL, [], $header);
    }

    public function testGetMeUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::PREFIX_URL);
    }

    public function testGetMeGardenSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::PREFIX_URL . '/gardens';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetMeGardenUnauthorized()
    {
        $url = self::PREFIX_URL . '/gardens';

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    // === PATCH ===
    public function testPatchMeSuccessful()
    {
        $header = $this->getHeaderConnect(self::$user['email'], self::$user['password']);

        $params = [
            'plainPassword' => 'supernewpassword',
        ];

        $this->isSuccessful(Request::METHOD_PATCH, self::PREFIX_URL, $params, $header);

        $header = $this->getHeaderConnect(self::$user['email'], $params['plainPassword'], true);

        $this->assertTrue(!is_null($header));
    }

    public function testPatchMeBadRequest()
    {
        $header = $this->getHeaderConnect(self::$user['email'], 'supernewpassword');

        $this->isBadRequest(Request::METHOD_PATCH, self::PREFIX_URL, [], $header);
    }

    public function testPatchMeUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_PATCH, self::PREFIX_URL);
    }
}
