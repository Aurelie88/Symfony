<?php

namespace AppBundle\Repository;

/**
 * EmployeeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EmployeeRepository extends \Doctrine\ORM\EntityRepository
{
    public function nombreEmployee(){
        $query = $this->_em->createQuery('SELECT count(u) as nombre FROM AppBundle:Employee u');
        $results = $query->getResult();
        return $results;
    }
}
