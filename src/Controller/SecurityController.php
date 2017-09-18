<?php

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

        if ('hgabka_kunstmaan_frontend_user_login' === $route) {
            $template = 'HgabkaKunstmaanFrontendUserBundle:Security:frontend_login.html.twig';
        } elseif ('fos_user_security_login' === $route) {
            $template = 'KunstmaanAdminBundle:Security:login.html.twig';
        }

        return $this->container->get('templating')->renderResponse($template, $data);
    }
}
