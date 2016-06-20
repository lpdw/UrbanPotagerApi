<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Access;

class MeasureControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/measures';

    private static $apiKey;
    private static $slug;
    private static $user = ['username' => 'dummy user2', 'password' => 'coucou'];

    // === SETUP ===
    public static function setUpBeforeClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEmail('tmp-user@urbanpotager.com')
                ->setUsername(self::$user['username'])
                ->setPlainPassword(self::$user['password'])
                ->setEnabled(true);

        $userManager->updateUser($user, true);

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
        $type1 = $typeRepository->findOneBy(['slug' => 'water-level']);
        $type2 = $typeRepository->findOneBy(['slug' => 'humidity-air']);

        $accessPublic = new Access();
        $accessPublic->setType($type1)
                     ->setIsPublic(true);

        $accessPrivate = new Access();
        $accessPrivate->setType($type2)
                      ->setIsPublic(false);

        $accessPublic->setGarden($garden);
        $accessPrivate->setGarden($garden);

        $em->persist($garden);
        $em->persist($accessPublic);
        $em->persist($accessPrivate);

        $em->flush();

        self::$apiKey = $garden->getApiKey();
        self::$slug = $garden->getSlug();
    }

    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();
        $gardenRepository = $em->getRepository('CoreBundle:Garden');

        $garden = $gardenRepository->findOneBy(['apiKey' => self::$apiKey]);

        $em->remove($garden);
        $em->flush();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail('tmp-user@urbanpotager.com');

        $userManager->deleteUser($user);
    }

    // === HELPER ===
    private function setGardenNotPublic()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();
        $gardenRepository = $em->getRepository('CoreBundle:Garden');

        /** @var Garden $garden */
        $garden = $gardenRepository->findOneBy(['apiKey' => self::$apiKey]);

        $garden->setIsPublic(false);

        $em->flush();
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $url = self::PREFIX_URL . '?api_key=' . self::$apiKey;

        $params = [
            'type' => 'water-level',
            'value' => 15.45,
        ];

        $this->isSuccessful(Request::METHOD_POST, $url, $params);

        $params['type'] = 'humidity-air';

        $this->isSuccessful(Request::METHOD_POST, $url, $params);
    }

    public function testPostBadRequest()
    {
        $url = self::PREFIX_URL . '?api_key=' . self::$apiKey;

        $params = [
            'type' => 'water-level',
        ];

        $this->isBadRequest(Request::METHOD_POST, $url, $params);
    }

    public function testPostNotFound()
    {
        $url = self::PREFIX_URL;

        $params = [
            'type' => 'water-level',
            'value' => 15.45,
        ];

        $this->isNotFound(Request::METHOD_POST, $url, $params);
    }

    // === GET ===
    public function testGetSuccessful()
    {
        $baseUrl = '/gardens/' . self::$slug . '/measures/';
        $url = $baseUrl . 'water-level';

        $this->isSuccessful(Request::METHOD_GET, $url);

        $url = $baseUrl . 'humidity-air';
        $header = $this->getHeaderConnect(self::$user['username'], self::$user['password']);

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetUnauthorized()
    {
        $baseUrl = '/gardens/' . self::$slug . '/measures/';
        $url = $baseUrl . 'humidity-air';

        $this->isUnauthorized(Request::METHOD_GET, $url);

        $this->setGardenNotPublic();

        $url = $baseUrl . 'water-level';
        $this->isUnauthorized(Request::METHOD_GET, $url);
    }

    public function testGetForbidden()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);
        $baseUrl = '/gardens/' . self::$slug . '/measures/';

        $url = $baseUrl . 'humidity-air';
        $this->isForbidden(Request::METHOD_GET, $url, [], $header);

        $this->setGardenNotPublic();

        $url = $baseUrl . 'water-level';
        $this->isForbidden(Request::METHOD_GET, $url, [], $header);
    }

    public function testGetNotFound()
    {
        $fakeGardenUrl = '/gardens/' . $this->fakeSlug() . '/measures/water-level';
        $this->isNotFound(Request::METHOD_GET, $fakeGardenUrl);

        $fakeMeasureUrl = '/gardens/' . self::$slug . '/measures/' . $this->fakeSlug();
        $this->isNotFound(Request::METHOD_GET, $fakeMeasureUrl);
    }
}
