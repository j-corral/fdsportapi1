<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/02/18
 * Time: 11:10
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ClickRepository extends EntityRepository {

    /**
     * @param $user
     * @param string $column
     * @return array
     */
    public function findByColumn($user, $column = '') {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('c');

        $qb->select('c')
        ->from('AppBundle:Click', 'c')
            ->where($qb->expr()->in('c.user', $user->getUserId()));


        if(!empty($column)) {
            $qb->andWhere($qb->expr()->isNotNull('c.'.$column));
        }


        $qb->orderBy('c.click_id', 'DESC');


        $query = $qb->getQuery();

        return $query->getResult();
    }

}