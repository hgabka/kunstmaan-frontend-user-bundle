<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Mailer;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class TwigSwiftMailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $fromEmail;

    protected $confirmationTemplate;
    protected $resettingTemplate;

    /**
     * TwigSwiftMailer constructor.
     *
     * @param \Swift_Mailer         $mailer
     * @param UrlGeneratorInterface $router
     * @param \Twig_Environment     $twig
     * @param string                $confirmationTemplate
     * @param string                $resettingTemplate
     */
    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $router, \Twig_Environment $twig, $confirmationTemplate, $resettingTemplate)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->twig = $twig;
        $this->confirmationTemplate = $confirmationTemplate;
        $this->resettingTemplate = $resettingTemplate;
    }

    public function setFromEmail($email)
    {
        $this->fromEmail = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function sendConfirmationEmailMessage(KunstmaanFrontendUserInterface $user)
    {
        $template = $this->confirmationTemplate;
        $url = $this->router->generate('hgabka_kunstmaan_frontend_user_registration_confirm', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, $this->fromEmail, (string) $user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function sendResettingEmailMessage(KunstmaanFrontendUserInterface $user)
    {
        $template = $this->resettingTemplate;
        $url = $this->router->generate('hgabka_kunstmaan_frontend_user_resetting_reset', ['token' => $user->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $context = [
            'user' => $user,
            'confirmationUrl' => $url,
        ];

        $this->sendMessage($template, $context, $this->fromEmail, (string) $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param array  $fromEmail
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $fromEmail, $toEmail)
    {
        $context = $this->twig->mergeGlobals($context);
        $template = $this->twig->loadTemplate($templateName);
        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
