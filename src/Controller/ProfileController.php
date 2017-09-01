<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Model\KunstmaanFrontendUserManagerInterface;

/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends Controller
{
    /**
     * Show the user.
     */
    public function frontendShowAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof KunstmaanFrontendUserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Profile:frontend_show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function frontendEditAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof KunstmaanFrontendUserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->createForm($this->container->getParameter('hgabka_kunstmaan_frontend_profile_form_type'));
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var $userManager KunstmaanFrontendUserManagerInterface */
            $userManager = $this->get('hgabkakunstmaanfrontenduser.user_manager');
            $userManager->updateUser($user);

            return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_profile_show');
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Profile:frontend_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
