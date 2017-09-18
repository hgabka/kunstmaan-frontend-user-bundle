<?php

namespace Hgabka\KunstmaanFrontendUserBundle\Model;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Hgabka\KunstmaanFrontendUserBundle\Entity\KunstmaanFrontendUserInterface;
use Hgabka\KunstmaanFrontendUserBundle\Util\PasswordUpdaterInterface;

/**
 * Abstract User Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class KunstmaanFrontendUserManager implements KunstmaanFrontendUserManagerInterface
{
    /** @var Registry */
    private $doctrine;

    /** @var PasswordUpdaterInterface $passwordUpdater */
    private $passwordUpdater;

    private $class;

    public function __construct(Registry $doctrine, PasswordUpdaterInterface $passwordUpdater, $class)
    {
        $this->doctrine = $doctrine;
        $this->passwordUpdater = $passwordUpdater;

        $metadata = $this->doctrine->getManager()->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->findUserBy(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            return $this->findUserByEmail($usernameOrEmail);
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(KunstmaanFrontendUserInterface $user)
    {
        $this->passwordUpdater->hashPassword($user);
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function reloadUser(KunstmaanFrontendUserInterface $user)
    {
        $this->doctrine->getManager()->refresh($user);
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(KunstmaanFrontendUserInterface $user, $andFlush = true)
    {
        $em = $this->doctrine->getManager();
        $this->updatePassword($user);

        $em->persist($user);
        if ($andFlush) {
            $em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteUser(KunstmaanFrontendUserInterface $user)
    {
        $em = $this->doctrine->getManager();

        $em->remove($user);
        $em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findUsers()
    {
        return $this->doctrine->getRepository($this->getClass())->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->doctrine->getRepository($this->getClass())->findOneBy($criteria);
    }

    /**
     * @return PasswordUpdaterInterface
     */
    protected function getPasswordUpdater()
    {
        return $this->passwordUpdater;
    }
}
