<?php

namespace App\Entity;

use App\Repository\SearchQueryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Pgvector\Vector;

#[ORM\Entity(repositoryClass: SearchQueryRepository::class)]
#[ORM\Table(name: 'search_queries')]
#[ORM\Index(columns: ['created_at'], name: 'idx_search_queries_created_at')]
#[ORM\Index(columns: ['user_id'], name: 'idx_search_queries_user_id')]
class SearchQuery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $keyword = null;

    #[ORM\Column(type: 'vector', length: 1536, nullable: true)]
    private ?Vector $vector = null;

    #[ORM\Column(nullable: true)]
    private ?int $userId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private int $searchCount = 1;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getVector(): ?Vector
    {
        return $this->vector;
    }

    /**
     * Set the vector. Accepts Vector or array for easier binding.
     */
    public function setVector(Vector|array|null $vector): static
    {
        if (is_array($vector)) {
            $vector = new Vector($vector);
        }
        $this->vector = $vector;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static
    {
        $this->userId = $userId;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSearchCount(): int
    {
        return $this->searchCount;
    }

    public function setSearchCount(int $searchCount): static
    {
        $this->searchCount = $searchCount;

        return $this;
    }

    public function incrementSearchCount(): static
    {
        $this->searchCount++;

        return $this;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
