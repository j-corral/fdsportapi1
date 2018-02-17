<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/02/18
 * Time: 11:10
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class TicketRepository extends EntityRepository {

    public function findAvailable($limit = 0) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('t');

        $qb->select('t')
        ->from('AppBundle:Ticket', 't')
        ->where(
            $qb->expr()->gt('t.date', 'CURRENT_DATE()')
        )
        ->orderBy('t.date', 'ASC');


        if($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

}