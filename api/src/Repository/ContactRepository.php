<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ContactRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, Contact::class);
    }

    public function create(Contact $contact): Contact
    {
        $this->_em->persist($contact);
        $this->_em->flush();

        return $contact;
    }

    public function save(): void
    {
        $this->_em->flush();
    }
}
