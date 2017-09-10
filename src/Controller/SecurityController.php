<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends BaseController
{
    /**
     * Overridden to check if the route matches our member login entry point.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderLogin(array $data)
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Pass the route to the template
        // Because we're extending FOSUserBundle by extending KunstmaanAdminBundle
        // The template inheritance for ::layout.html.twig will always point
        // at this bundles' ::layout.html.twig file, and end in a endless loop
        $route = $request->attributes->get('_route');

        if ($route === 'hgabka_kunstmaan_frontend_user_login') {
            $template = 'HgabkaKunstmaanFrontendUserBundle:Security:frontend_login.html.twig';
        } elseif ($route === 'fos_user_security_login') {
            $template = 'KunstmaanAdminBundle:Security:login.html.twig';
        }

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
