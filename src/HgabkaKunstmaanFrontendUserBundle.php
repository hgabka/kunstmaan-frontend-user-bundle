<?php

namespace Hgabka\KunstmaanFrontendUserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HgabkaKunstmaanFrontendUserBundle extends Bundle
{
    /**
     * @return string The Bundle parent name it overrides or null if no parent
     */
    public function getParent()
    {
        return 'KunstmaanAdminBundle';
    }
}
