<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hgabka\KunstmaanFrontendUserBundle\Util;

use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
interface PasswordUpdaterInterface
{
    /**
     * Updates the hashed password in the user when there is a new password.
     *
     * The implement should be a no-op in case there is no new password (it should not erase the
     * existing hash with a wrong one).
     *
     * @param KunstmaanFrontendUserInterface $user
     */
    public function hashPassword(KunstmaanFrontendUserInterface $user);
}
