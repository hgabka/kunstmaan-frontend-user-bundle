<?php

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
