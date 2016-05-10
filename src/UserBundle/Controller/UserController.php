<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use CoreBundle\Controller\CoreController;
use UserBundle\Entity\User;
use UserBundle\Form\Type\UserType;

class UserController extends CoreController
{
    /**
     * @View(serializerGroups={"Default", "details-user", "me"}, statusCode=201)
     */
    public function postUserAction(Request $request)
    {
        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');
        /** @var \UserBundle\Entity\User $user */
        $user = $userManager->createUser();

        return $this->formUser($user, $request, 'post');
    }

    private function formUser(User $user, Request $request, $method = 'post')
    {
        $form = $this->createForm(UserType::class, $user, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return [
                'user' => $this->persistUser($user),
            ];
        }

        return new JsonResponse($this->getAllErrors($form), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * @param User $userModel
     *
     * @return \UserBundle\Entity\User
     */
    private function persistUser(User $user)
    {
        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $this->get('fos_user.user_manager');

        $user->setEnabled(true);
        $userManager->updateUser($user, true);

        return $user;
    }

    /**
     * @return string
     */
    protected function getRepositoryName()
    {
        return 'CoreBundle:User';
    }
}
