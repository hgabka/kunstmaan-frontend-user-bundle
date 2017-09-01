<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Model\KunstmaanFrontendUserManagerInterface;

class LastLoginListener implements EventSubscriberInterface
{
    protected $userManager;

    /**
     * LastLoginListener constructor.
     *
     * @param KunstmaanFrontendUserManagerInterface $userManager
     */
    public function __construct(KunstmaanFrontendUserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof KunstmaanFrontendUserInterface) {
            $user->setLastLogin(new \DateTime());
            $this->userManager->updateUser($user);
        }
    }
}
