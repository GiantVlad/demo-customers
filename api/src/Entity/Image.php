<?php

namespace App\Entity;

use App\Entity\Interfaces\IdentifiableInterface;
use App\Entity\Interfaces\UploadableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Table(name: "images")]
#[ORM\Entity]
#[Vich\Uploadable]
class Image implements UploadableInterface, IdentifiableInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Serializer\Type('integer')]
    #[Serializer\Groups(['get_image', 'createContact'])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Serializer\Type('string')]
    #[Serializer\Groups(['get_image'])]
    private ?string $fileName = null;

    #[ORM\OneToOne(targetEntity: Contact::class, cascade: ["persist", "remove"], orphanRemoval: true)]
    #[ORM\JoinColumn(name: "contact_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    #[Serializer\Groups(['get_image'])]
    private ?Contact $contact;

    #[Vich\UploadableField(mapping: "image", fileNameProperty: "fileName")]
    #[Serializer\Exclude]
    private File $file;

    #[ORM\Column(type: 'datetime')]
    #[Serializer\Exclude]
    private \DateTimeInterface $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
