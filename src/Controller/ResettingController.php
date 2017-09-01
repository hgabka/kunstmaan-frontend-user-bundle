<?php
/**
 * Created by PhpStorm.
 * User: Gabe
 * Date: 2017.01.27.
 * Time: 9:57
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Controller;

use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Model\KunstmaanFrontendUserManagerInterface;

class ResettingController extends Controller
{
    /**
     * Request reset user password: show form.
     */
    public function frontendRequestAction()
    {
        return $this->render('HgabkaKunstmaanFrontendUserBundle:Resetting:frontend_request.html.twig');
    }

    /**
     * Request reset user password: submit form and send email.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function frontendSendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        /** @var $user KunstmaanFrontendUserInterface */
        $user = $this->get('hgabkakunstmaanfrontenduser.user_manager')->findUserByUsernameOrEmail($username);

        if (null !== $user) {
            if (null === $user->getConfirmationToken()) {
                /** @var $tokenGenerator TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
            }

            $this->get('hgabkakunstmaanfrontenduser.mailer')->sendResettingEmailMessage($user);
            $this->get('hgabkakunstmaanfrontenduser.user_manager')->updateUser($user);
        }

        return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_resetting_check_email', ['username' => $username]);
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function frontendCheckEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_resetting_request');
        }
        /** @var $user KunstmaanFrontendUserInterface */
        $user = $this->get('hgabkakunstmaanfrontenduser.user_manager')->findUserByUsernameOrEmail($username);

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Resetting:frontend_check_email.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function frontendResetAction(Request $request, $token)
    {
        /** @var $userManager KunstmaanFrontendUserManagerInterface */
        $userManager = $this->get('hgabkakunstmaanfrontenduser.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $form = $this->createForm($this->container->getParameter('hgabka_kunstmaan_frontend_resetting_form_type'));
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updateUser($user);

            return $this->redirectToRoute('hgabka_kunstmaan_frontend_user_profile_show');
        }

        return $this->render('HgabkaKunstmaanFrontendUserBundle:Resetting:frontend_reset.html.twig', [
            'token' => $token,
            'form' => $form->createView(),
        ]);
    }
}
