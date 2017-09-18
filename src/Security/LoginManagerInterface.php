<?php

namespace Hgabka\KunstmaanFrontendUserBundle\Security;

use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Symfony\Component\HttpFoundation\Response;

interface LoginManagerInterface
{
    /**
     * @param string                         $firewallName
     * @param KunstmaanFrontendUserInterface $user
     * @param null|Response                  $response
     */
    public function logInUser($firewallName, KunstmaanFrontendUserInterface $user, Response $response = null);
}
