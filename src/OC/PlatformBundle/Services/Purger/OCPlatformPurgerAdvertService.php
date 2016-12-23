<?php

namespace OC\PlatformBundle\Services\Purger;

use DateTime;
use Doctrine\ORM\EntityManager;

class OCPlatformPurgerAdvertService
{

    /**
     *
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function purge($days)
    {
        $advertRepository = $this->em->getRepository('OCPlatformBundle:Advert');
        $alias = $advertRepository->getDefaultAlias();

        /* See https://github.com/doctrine/dbal/issues/2586
         * Can be used when fix branch will be merged
        $result = $advertRepository
            ->getQueryBuilder()
            ->delete()
            ->where(sprintf('%s.applications IS EMPTY', $alias))
            ->andWhere(sprintf("%s.date < DATE_SUB(CURRENT_DATE(), :days, 'DAY')", $alias))
            ->setParameter('days', $days)
            ->getQuery()
            ->execute()
        ;
        */


        $purgeDate = new DateTime();
        $purgeDate
            ->modify(sprintf('- %s days', $days))
            ->setTime(0, 0, 0)
        ;

        $result = $advertRepository
            ->getQueryBuilder()
            ->delete()
            ->where(sprintf('%s.applications IS EMPTY', $alias))
            ->andWhere(sprintf('%s.date < :purgeDate', $alias))
            ->setParameter('purgeDate', $purgeDate->format('Y-m-d'))
            ->getQuery()
            ->execute()
        ;

        return $result;
    }
}