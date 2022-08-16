<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\IdentifiableInterface;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customers')]
#[UniqueEntity(fields: ['nickName'])]
#[UniqueEntity(fields: ['email'])]
class Customer implements IdentifiableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Serializer\Type('integer')]
    #[Serializer\Groups(['getters', 'createContact'])]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 100)]
    #[Serializer\Type('string')]
    private string $nickName = '';

    #[ORM\Column(unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Serializer\Type('string')]
    private string $email = '';

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Contact::class)]
    #[Serializer\Exclude]
    private iterable $contacts;

    public function __construct()
    {
        $this->nickName = '';
        $this->contacts = new ArrayCollection([]);
        $this->email = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickName(): string
    {
        return $this->nickName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getContacts(): iterable
    {
        return $this->contacts;
    }

    public function setNickName(string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setContacts(iterable $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }
}
