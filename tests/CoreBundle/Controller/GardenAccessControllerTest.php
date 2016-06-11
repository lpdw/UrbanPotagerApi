<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Access;

class GardenAccessControllerTest extends AbstractControllerTest
{
    private static $prefixUrl;
    private static $slug;

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
            ->setIsPublic(true)
            ->setOwner($user)
            ->setLongitude(2.4)
            ->setLatitude(42.8)
            ->setShowLocation(true)
            ->setCountry('France')
            ->setCity('Paris')
            ->setAddress1('4 Rue de la tour Eiffel');

        $typeRepository = $em->getRepository('CoreBundle:Type');
        $type = $typeRepository->findOneBy(['slug' => 'water-level']);

        $access = new Access();
        $access->setType($type)
            ->setIsPublic(true);

        $access->setGarden($garden);

        $em->persist($garden);
        $em->persist($access);

        $em->flush();

        self::$slug = $garden->getSlug();
        self::$prefixUrl = '/gardens/' . self::$slug . '/access';
    }

    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();
        $gardenRepository = $em->getRepository('CoreBundle:Garden');

        $garden = $gardenRepository->findOneBy(['slug' => self::$slug]);

        $em->remove($garden);
        $em->flush();
    }

    // === HELPER ===
    private function setIsPublicGarden($isPublic)
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();

        $gardenRepository = $em->getRepository('CoreBundle:Garden');

        /** @var Garden $garden */
        $garden = $gardenRepository->findOneBy(['slug' => self::$slug]);

        $garden->setIsPublic($isPublic);

        $em->flush();
    }

    // === GET ===
    public function testCGetSuccessful()
    {
        $this->isSuccessful(Request::METHOD_GET, self::$prefixUrl);

        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->setIsPublicGarden(false);

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
        $url = '/gardens/' . $this->fakeSlug() . '/access';

        $this->isNotFound(Request::METHOD_GET, $url);
    }

    public function testGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);

        $this->setIsPublicGarden(true);

        $this->isSuccessful(Request::METHOD_GET, $url);

        $url = self::$prefixUrl . '/humidity-air';

        $this->isSuccessful(Request::METHOD_GET, $url);
    }

    public function testGetUnauthorized()
    {
        $this->setIsPublicGarden(false);

        $url = self::$prefixUrl . '/water-level';

        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isForbidden(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $params = [
            'type' => 'humidity-air',
        ];

        $this->isSuccessful(Request::METHOD_POST, self::$prefixUrl, $params, $header);
        $access = $this->getResponseContent('access');

        $this->assertTrue(!$access['is_public']);

        $params = [
            'type' => 'water-temperature',
            'isPublic' => true,
        ];

        $this->isSuccessful(Request::METHOD_POST, self::$prefixUrl, $params, $header);
        $access = $this->getResponseContent('access');

        $this->assertTrue($access['is_public']);
    }

    public function testPostBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isBadRequest(Request::METHOD_POST, self::$prefixUrl, [], $header);
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

        $url = '/gardens/' . $this->fakeSlug() . '/access';

        $this->isNotFound(Request::METHOD_POST, $url, [], $header);
    }

    // === PATCH ===
    public function testPatchSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isSuccessful(Request::METHOD_PATCH, $url, ['submit' => true], $header);
        $access = $this->getResponseContent('access');

        $this->assertTrue(!$access['is_public']);

        $this->isSuccessful(Request::METHOD_PATCH, $url, ['isPublic' => true], $header);
        $access = $this->getResponseContent('access');

        $this->assertTrue($access['is_public']);
    }

    public function testPatchBadRequest()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isBadRequest(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchUnauthorized()
    {
        $url = self::$prefixUrl . '/water-level';

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }

    public function testPatchForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isForbidden(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_PATCH, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';

        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteUnauthorized()
    {
        $url = self::$prefixUrl . '/humidity-air';

        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/humidity-air';

        $this->isForbidden(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/water-level';
        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);

        $url = '/gardens/' . $this->fakeSlug() . '/access/humidity-air';
        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);

        $url = self::$prefixUrl . '/' . $this->fakeSlug();
        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }
}
