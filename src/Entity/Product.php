<?php
// src/Entity/Product.php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Pgvector\Vector;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
#[ORM\Index(columns: ['category'], name: 'idx_product_category')]
#[ORM\Index(columns: ['created_at'], name: 'idx_product_created_at')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?string $price = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $category = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $tags = [];

    #[ORM\Column]
    private ?bool $inStock = true;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'vector', length: 1536, nullable: true)]
    private ?Vector $embedding = null;

    // Transient property for similarity score (not persisted to database)
    private ?float $similarityScore = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): static
    {
        $this->tags = $tags;
        return $this;
    }

    public function isInStock(): ?bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $inStock): static
    {
        $this->inStock = $inStock;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEmbedding(): ?array
    {
        return $this->embedding;
    }

    public function setEmbedding(Vector $embedding): static
    {
        $this->embedding = $embedding;
        return $this;
    }

    public function getSearchableText(): string
    {
        return sprintf(
            '%s %s %s %s',
            $this->name,
            $this->description,
            $this->category,
            implode(' ', $this->tags ?? [])
        );
    }

    public function getSimilarityScore(): ?float
    {
        return $this->similarityScore;
    }

    public function setSimilarityScore(?float $similarityScore): static
    {
        $this->similarityScore = $similarityScore;
        return $this;
    }
}
