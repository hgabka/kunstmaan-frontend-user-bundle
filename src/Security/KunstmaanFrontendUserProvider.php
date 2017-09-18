<?php

namespace Hgabka\KunstmaanFrontendUserBundle\Security;

use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Model\KunstmaanFrontendUserManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class KunstmaanFrontendUserProvider implements UserProviderInterface
{
    /**
     * @var KunstmaanFrontendUserManagerInterface
     */
    protected $userManager;

    /**
     * Constructor.
     *
     * @param KunstmaanFrontendUserManagerInterface $userManager
     */
    public function __construct(KunstmaanFrontendUserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUser($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        if (!$user instanceof KunstmaanFrontendUserInterface) {
            throw new UnsupportedUserException(sprintf('Expected an instance of Hgabka\KunstmaanUserBundle\KunstmaanFrontendUserInterface, but got "%s".', get_class($user)));
        }

        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), get_class($user)));
        }

        if (null === $reloadedUser = $this->userManager->findUserBy(['id' => $user->getId()])) {
            throw new UsernameNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        $userClass = $this->userManager->getClass();

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    /**
     * Finds a user by username.
     *
     * This method is meant to be an extension point for child classes.
     *
     * @param string $username
     *
     * @return null|KunstmaanFrontendUserInterface
     */
    protected function findUser($username)
    {
        return $this->userManager->findUserByUsernameOrEmail($username);
    }
}
