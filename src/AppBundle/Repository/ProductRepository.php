<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 17/02/18
 * Time: 11:10
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository {

    public function findNewest($limit = 0) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder('p');

        $qb->select('p')
        ->from('AppBundle:Product', 'p')
        ->orderBy('p.product_id', 'DESC');


        if($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

}