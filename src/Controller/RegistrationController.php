<?php

namespace Hgabka\KunstmaanFrontendUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Model\KunstmaanFrontendUserManagerInterface;

class RegistrationController extends Controller
{
    public function frontendRegisterAction(Request $request)
    {
        /** @var $userManager KunstmaanFrontendUserManagerInterface */
        $userManager = $this->get('hgabkakunstmaanfrontenduser.user_manager');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $form = $this->createForm($this->container->getParameter('hgabka_kunstmaan_frontend_registration_form_type'));
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($this->container->getParameter('hgabka_kunstmaan_frontend_registration_confirmation_enabled')) {
                    $user->setEnabled(false);
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $mailer = $this->get('hgabkakunstmaanfrontenduser.mailer');
                    if (null === $user->getConfirmationToken()) {
                        $user->setConfirmationToken($tokenGenerator->generateToken());
                    }
                    $mailer->sendConfirmationEmailMessage($user);
                    $this->get('session')->set('hgabka_kunstmaan_frontend_user_send_confirmation_email/email', $user->getEmail());
                    $userManager->updateUser($user);

                    return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_registration_check_email');
                } else {
                    $userManager->updateUser($user);

                    return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_registration_confirmed');
                }
            }
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Registration:frontend_register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Tell the user to check his email provider.
     */
    public function frontendCheckEmailAction()
    {
        $email = $this->get('session')->get('hgabka_kunstmaan_frontend_user_send_confirmation_email/email');

        if (empty($email)) {
            return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_registration');
        }

        $this->get('session')->remove('hgabka_kunstmaan_frontend_user_send_confirmation_email/email');
        $user = $this->get('hgabkakunstmaanfrontenduser.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Registration:frontend_check_email.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function frontendConfirmAction(Request $request, $token)
    {
        /** @var $userManager KunstmaanFrontendUserManagerInterface */
        $userManager = $this->get('hgabkakunstmaanfrontenduser.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $userManager->updateUser($user);
        $this->authenticate($user);

        return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_registration_confirmed');
    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function frontendConfirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof KunstmaanFrontendUserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Registration:frontend_confirmed.html.twig', [
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
        ]);
    }

    /**
     * @return mixed
     */
    protected function getTargetUrlFromSession()
    {
        $key = sprintf('_security.%s.target_path', $this->get('security.token_storage')->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }

    /**
     * @param KunstmaanFrontendUserInterface $user
     */
    protected function authenticate(KunstmaanFrontendUserInterface $user)
    {
        /** @var $userManager KunstmaanFrontendUserManagerInterface */
        $userManager = $this->get('hgabkakunstmaanfrontenduser.user_manager');

        try {
            $this->get('hgabkakunstmaanfrontenduser.security.login_manager')->logInUser($this->container->getParameter('hgabka_kunstmaan_frontend_firewall_name'), $user);
            $user->setLastLogin(new \DateTime());

            $userManager->updateUser($user);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
