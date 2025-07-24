<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
#[ORM\Table(name: 'feedback_submissions')] # Custom table name
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['feedback_list', 'feedback_detail'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Name cannot be blank.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Name must be at least {{ limit }} characters long.')]
    #[Groups(['feedback_list', 'feedback_detail', 'feedback_create'])]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Email cannot be blank.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[Groups(['feedback_list', 'feedback_detail', 'feedback_create'])]
    private ?string $email = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Message cannot be blank.')]
    #[Assert\Length(min: 10, minMessage: 'Message must be at least {{ limit }} characters long.')]
    #[Groups(['feedback_list', 'feedback_detail', 'feedback_create'])]
    private ?string $message = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['feedback_list', 'feedback_detail'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}