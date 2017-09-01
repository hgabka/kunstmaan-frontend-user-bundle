<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;

interface LoginManagerInterface
{
    /**
     * @param string                         $firewallName
     * @param KunstmaanFrontendUserInterface $user
     * @param Response|null                  $response
     */
    public function logInUser($firewallName, KunstmaanFrontendUserInterface $user, Response $response = null);
}
