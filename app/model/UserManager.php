<?php

namespace App\Model;

use App\Model\Entities\User;
use Doctrine\ORM\EntityManager;
use Nette;
use Nette\Security\Passwords;


/*
 * User management
 */

class UserManager implements Nette\Security\IAuthenticator
{
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Performs an authentication.
     * @return Nette\Security\Identity
     * @throws Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->em->find('\App\Model\Entities\User', $username);
        if (!$row) {
            throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);

        } elseif (!Passwords::verify($password, $row->getPassword())) {
            throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

        } elseif (Passwords::needsRehash($row->getPassword())) {
            $row->update([
                'password' => Passwords::hash($password),
            ]);
        }

        return new Nette\Security\Identity($row->getLogin(), ['admin'], ['username' => $row->getLogin()]);
    }
}
