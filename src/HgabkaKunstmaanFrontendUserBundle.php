<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
