<?php

namespace App\Entity;

use App\Repository\ConseilRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ConseilRepository::class)]
class Conseil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['list_conseil', 'admin_conseil'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['list_conseil', 'admin_conseil'])]
    #[Assert\NotBlank(message: 'Le mois doit être ajouté')]
    #[Assert\Range(min: 1, max: 12, notInRangeMessage: "Il faut mettre un mois valide")]
    private ?int $month = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_conseil', 'admin_conseil'])]
    #[Assert\NotBlank(message: 'La ville doit être ajoutée')]
    #[Assert\Length(min: 1, max: 60, minMessage: "Il faut une ville avec au moins {{ limit }} caractères", maxMessage: "Il faut une ville avec au maximum {{ limit }} caractères")]
    private ?string $city = null;

    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(min: 10, minMessage: "La description doit faire au moins {{ limit }} caractères")]
    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['list_conseil', 'admin_conseil'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'conseils')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['list_conseil', 'admin_conseil'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): static
    {
        $this->month = $month;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
