<?php

namespace OC\PlatformBundle\Repository;

use TO\ORMBundle\Component\TOORMEntityRepository;

/**
 * AdvertSkillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertSkillRepository extends TOORMEntityRepository
{
    protected $defaultAlias = 'ask';

    public function loadAdvertSkillWithSkill()
    {
        $this->loadRelatedFromEntity('skill', 's');

        return $this;
    }
}