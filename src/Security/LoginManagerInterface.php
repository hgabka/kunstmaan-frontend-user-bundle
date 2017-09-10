<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
