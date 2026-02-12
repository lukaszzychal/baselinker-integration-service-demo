<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ORM\UniqueConstraint(columns: ['external_id', 'marketplace'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) ?int $id = null;
    
    #[ORM\Column(length: 255)]
    public private(set) string $externalId;
    
    #[ORM\Column(length: 50)]
    public private(set) string $marketplace;
    
    #[ORM\Column(length: 255)]
    public private(set) string $customerName;
    
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    public private(set) string $totalAmount;
    
    #[ORM\Column(type: 'datetime_immutable')]
    public private(set) \DateTimeImmutable $createdAt;
    
    public function __construct(
        string $externalId,
        string $marketplace,
        string $customerName,
        string $totalAmount,
        ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->externalId = $externalId;
        $this->marketplace = $marketplace;
        $this->customerName = $customerName;
        $this->totalAmount = $totalAmount;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }
}
