<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\IdentifiableInterface;
use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'contacts')]
class Contact implements IdentifiableInterface
{
    #[Groups("contact")]
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Serializer\Groups(['getters', 'get_image'])]
    private ?int $id = null;

    #[Groups("contact")]
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Serializer\Groups(['Default', 'createContact'])]
    public string $firstName = '';

    #[Groups("contact")]
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Serializer\Groups(['Default', 'createContact'])]
    private string $lastName = '';

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Serializer\Groups(['Default', 'createContact'])]
    public string $phone = '';

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Serializer\Groups(['Default', 'createContact'])]
    public string $address = '';

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Serializer\Groups(['Default', 'createContact'])]
    public string $email = '';

    #[ORM\Column(type: 'date')]
    #[Serializer\Type("DateTimeInterface<'Y-m-d'>")]
    #[Serializer\Groups(['Default', 'createContact'])]
    public \DateTimeInterface $birthday;

    #[ORM\Column(nullable: true)]
    #[Serializer\Groups(['Default', 'createContact'])]
    private ?string $imgUrl = null;

    #[ORM\OneToOne(targetEntity: Image::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[ORM\JoinColumn(name: "file_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Serializer\Groups(['get_contact'])]
    private ?Image $image = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: "customer_id", referencedColumnName: "id", nullable: true)]
    #[Serializer\Groups(['Default', 'createContact'])]
    public ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id", nullable: false)]
    #[Serializer\Groups(['Default', 'createContact'])]
    public Customer $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getBirthday(): \DateTimeInterface
    {
        return $this->birthday;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getOwner(): Customer
    {
        return $this->owner;
    }

    public function setImgUrl(?string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
}
