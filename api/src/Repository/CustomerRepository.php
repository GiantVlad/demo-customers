<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Customer::class);
    }

    public function create(Customer $customer): Customer
    {
        $this->_em->persist($customer);
        $this->_em->flush();

        return $customer;
    }

    public function update(Customer $customer): Customer
    {
        $this->_em->flush();

        return $customer;
    }
}
