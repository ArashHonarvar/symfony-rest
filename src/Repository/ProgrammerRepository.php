<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use App\Entity\Programmer;

class ProgrammerRepository extends EntityRepository
{
    /**
     * @param User $user
     * @return Programmer[]
     */
    public function findAllForUser(User $user)
    {
        return $this->findBy(array('user' => $user));
    }

    /**
     * @param $nickname
     * @return Programmer
     */
    public function findOneByNickname($nickname)
    {
        return $this->findOneBy(array('nickname' => $nickname));
    }
}
