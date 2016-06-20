<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\UserShare;
use CoreBundle\Entity\Garden;

class GardenUserShareControllerTest extends AbstractControllerTest
{
    private static $prefixUrl;
    private static $slugGarden;
    private static $idShare;
    private static $idShare2;

    // === SETUP ===
    public static function setUpBeforeClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = self::USER1;
        $user = $userManager->findUserByUsername($user['username']);

        $garden = new Garden();
        $garden->setName('tmp garden')
            ->setDescription('description')
            ->setIsPublic(false)
            ->setOwner($user)
            ->setLongitude(2.4)
            ->setLatitude(42.8)
            ->setShowLocation(true)
            ->setCountry('France')
            ->setCity('Paris')
            ->setAddress1('4 Rue de la tour Eiffel');


        $em->persist($garden);

        $share = new UserShare();
        $share->setOwner($user)
              ->setGarden($garden)
              ->setMessage('coucou');

        $em->persist($share);
        $em->flush();

        self::$slugGarden = $garden->getSlug();
        self::$idShare = $share->getId();
        self::$prefixUrl = '/gardens/' . self::$slugGarden . '/shares';
    }

    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();
        $gardenRepository = $em->getRepository('CoreBundle:Garden');

        $garden = $gardenRepository->findOneBy(['slug' => self::$slugGarden]);

        $em->remove($garden);
        $em->flush();
    }

    // === GET ===
    public function testCGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isSuccessful(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testCGetUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::$prefixUrl);
    }

    public function testCGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isForbidden(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testCGetNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/gardens/' . $this->fakeSlug() . '/shares';

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/' . self::$idShare;

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetUnauthorized()
    {
        $url = '/shares/' . self::$idShare;

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/' . self::$idShare;

        $this->isForbidden(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/-1';

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'message' => 'coucou',
        ];

        $this->isSuccessful(Request::METHOD_POST, self::$prefixUrl, $params, $header);
        $share = $this->getResponseContent('share');

        self::$idShare2 = $share['id'];

        $this->assertEquals(
            $params['message'],
            $share['message']
        );
    }

    public function testPostUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_POST, self::$prefixUrl);
    }

    public function testPostForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isForbidden(Request::METHOD_POST, self::$prefixUrl, [], $header);
    }

    public function testPostNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/gardens/' . $this->fakeSlug() . '/shares';

        $this->isNotFound(Request::METHOD_POST, $url, [], $header);
    }

    // === PATCH ===
    public function testPatchSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'message' => 'coucou2',
        ];

        $url = '/shares/' . self::$idShare2;

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);
        $share = $this->getResponseContent('share');

        $this->assertEquals(
            $params['message'],
            $share['message']
        );
    }

    public function testPatchUnauthorized()
    {
        $url = '/shares/' . self::$idShare2;

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }

    public function testPatchForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/' . self::$idShare2;

        $this->isForbidden(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/-1';

        $this->isNotFound(Request::METHOD_PATCH, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteUnauthorized()
    {
        $url = '/shares/' . self::$idShare2;

        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/' . self::$idShare2;

        $this->isForbidden(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/-1';

        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/shares/' . self::$idShare;
        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);
        $this->isNotFound(Request::METHOD_GET, $url, [], $header);

        $url = '/shares/' . self::$idShare2;
        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);
        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }
}
