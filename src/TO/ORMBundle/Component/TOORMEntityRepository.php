<?php

namespace TO\ORMBundle\Component;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class TOORMEntityRepository extends EntityRepository
{
    /**
     * Query Builder
     *
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * Default alias for entity repository
     *
     * @var string
     */
    protected $defaultAlias;

    /**
     * Hydratation mode
     *
     * @var int
     */
    protected $hydratationMode = Query::HYDRATE_OBJECT;

    public function getQueryBuilder()
    {
        if (!isset($this->queryBuilder)) {
            $this->queryBuilder = $this->createQueryBuilder($this->defaultAlias);
        }

        return $this->queryBuilder;
    }

    public function setQueryBuilder($qb)
    {
        $this->queryBuilder = $qb;

        return $this;
    }

    public function getDefaultAlias()
    {
        return $this->defaultAlias;
    }

    protected function loadRelatedFromEntity($field, $tableAlias, $typeJoin = 'innerJoin')
    {
        // Check if alias isn't already used
        if (in_array($tableAlias, $this->getQueryBuilder()->getAllAliases())) {
            throw new TOORMException(sprintf(
                'The alias "%s" already exist in query builder for entity %s',
                $tableAlias,
                $this->getEntityName()
            ));
        }

        $qb = $this
            ->getQueryBuilder()
            ->$typeJoin(
                sprintf('%s.%s', $this->defaultAlias, $field),
                $tableAlias
            )
            ->addSelect($tableAlias)
        ;

        $this->setQueryBuilder($qb);

        return $this;
    }

    public function findIdFromQueryBuilder($id)
    {
        $result = $this
            ->getQueryBuilder()
            ->where(sprintf('%s.id = :id', $this->defaultAlias))
//             ->andWhere(
//                 sprintf('%s.id = :id', $this->defaultAlias)
//             )
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult($this->hydratationMode)
        ;

        return $result;
    }

    public function findByFromQueryBuilder(
        array $criterias,
        array $parameters,
        array $orderBy = null,
        $limit = null,
        $offset = null
    )
    {
        $qb = $this->getQueryBuilder();

        // Where clause
        foreach ($criterias as $criteria => $clause) {
            // orWhere OR andWhere
            $method = sprintf(
                '%sWhere',
                strtolower($clause)
            );
            $qb->$method($criteria);
        }
        $qb->setParameters($parameters);

        if (!is_null($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy($sort, $order);
            }
        }

        return $qb->getQuery()->getResult($this->hydratationMode);
    }

}
