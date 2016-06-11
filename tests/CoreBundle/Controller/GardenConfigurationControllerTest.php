<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Configuration;

class GardenConfigurationControllerTest extends AbstractControllerTest
{
    private static $prefixUrl;
    private static $slugGarden1;
    private static $slugGarden2;
    private static $slugConfiguration;

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

        $garden2 = clone $garden;

        $start = new \DateTime('now');
        $end = new \DateTime('now -2 hours');

        $configuration = new Configuration();
        $configuration->setName('configuration name')
                      ->setDescription('description')
                      ->setOwner($user)
                      ->setIsWateringActive(true)
                      ->setLightingStart($start)
                      ->setLightingEnd($end)
                      ->setLightTreshold(54.22)
                      ->setWateringStart($start)
                      ->setWateringEnd($end);

        $em->persist($garden);
        $em->persist($garden2);
        $em->persist($configuration);

        $em->flush();

        self::$slugGarden1 = $garden->getSlug();
        self::$slugGarden2 = $garden2->getSlug();
        self::$slugConfiguration = $configuration->getSlug();
        self::$prefixUrl = '/gardens/' . self::$slugGarden1 . '/configurations';
    }

    public static function tearDownAfterClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \Doctrine\Common\Persistence\ObjectManager $em */
        $em = $container->get('doctrine')->getManager();
        $gardenRepository = $em->getRepository('CoreBundle:Garden');
        $configurationRepository = $em->getRepository('CoreBundle:Configuration');

        $garden1 = $gardenRepository->findOneBy(['slug' => self::$slugGarden1]);
        $garden2 = $gardenRepository->findOneBy(['slug' => self::$slugGarden2]);
        $configuration = $configurationRepository->findOneBy(['slug' => self::$slugConfiguration]);

        $em->remove($garden1);
        $em->remove($garden2);
        $em->remove($configuration);
        $em->flush();
    }

    // === POST ===
    public function testPostSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . self::$slugConfiguration;

        $this->isSuccessful(Request::METHOD_POST, $url, [], $header);
        $this->isSuccessful(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testPostUnauthorized()
    {
        $url = self::$prefixUrl . '/' . self::$slugConfiguration;

        $this->isUnauthorized(Request::METHOD_POST, $url);
    }

    public function testPostForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . self::$slugConfiguration;

        $this->isForbidden(Request::METHOD_POST, $url, [], $header);
    }

    public function testPostNotFound()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . $this->fakeSlug();

        $this->isNotFound(Request::METHOD_POST, $url, [], $header);

        $url = '/gardens/' . $this->fakeSlug() . '/configurations/' . self::$slugConfiguration;

        $this->isNotFound(Request::METHOD_POST, $url, [], $header);
    }

    public function testPostConflict()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = self::$prefixUrl . '/' . self::$slugConfiguration;

        $this->isConflict(Request::METHOD_POST, $url, [], $header);
    }

    // === GET ===
    public function testGetSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isSuccessful(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testGetUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::$prefixUrl);
    }

    public function testGetForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isForbidden(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testGetNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/gardens/' . $this->fakeSlug() . '/configurations';

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);

        $url = '/gardens/' . self::$slugGarden2 . '/configurations';

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_DELETE, self::$prefixUrl);
    }

    public function testDeleteForbidden()
    {
        $user = self::USER2;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isForbidden(Request::METHOD_DELETE, self::$prefixUrl, [], $header);
    }

    public function testDeleteSuccessful()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $this->isSuccessful(Request::METHOD_DELETE, self::$prefixUrl, [], $header);

        $url = '/configurations/' . self::$slugConfiguration;

        $this->isSuccessful(Request::METHOD_GET, $url, [], $header);
        $this->isNotFound(Request::METHOD_GET, self::$prefixUrl, [], $header);
    }

    public function testDeleteNotFound()
    {
        $user = self::USER1;
        $header = $this->getHeaderConnect($user['username'], $user['password']);

        $url = '/gardens/' . $this->fakeSlug() . '/configurations';

        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }
}
