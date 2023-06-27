<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\AssignCustomerRaffleController;
use App\Controller\AssignParticipationController;
use App\Controller\ParticipationController;
use App\Controller\ParticipationEspecificController;
use App\Controller\RaffleRegisterController;
use App\Controller\ScratchController;
use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[Get]
#[GetCollection]
#[Post]
#[Put]
#[Delete]
#[ApiResource(operations: [
    new Get(
        uriTemplate: '/participations/customer/{customer_id}',
        requirements: ['customer_id' => '\d+'],
        controller: ParticipationController::class,
        name: 'GetForCustomer'
    ),
    new Put(
        uriTemplate: '/participations/{participation_id}/customer/{customer_id}/{raffle_id}',
        requirements: [
            'sale_id' => '\d+',
            'customer_id' => '\d+',
            'raffle_id' => '\d+'
        ],
        controller: AssignCustomerRaffleController::class,
        name: 'AssignForCustomer'
    ),
    new Put(
        uriTemplate: '/participations/scratch/{id}',
        controller: ScratchController::class,
        name: 'Scratch'
    ),
    new Post(
        uriTemplate: '/assign/participations',
        controller: AssignParticipationController::class,
        name: 'AssigParticipation'
    ),
    new Put(
        uriTemplate: '/participations/register/{id}',
        controller: RaffleRegisterController::class,
        name: 'RaffleRegister'
    ),

])]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $raffle_id = null;

    #[ORM\Column]
    private ?int $participation_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $participation_date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prize = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sale_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coupon_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customer_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $scratch = null;

    #[ORM\Column(nullable: true)]
    private ?bool $associated_raffle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $store = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $scratch_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $raffle_date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaffleId(): ?int
    {
        return $this->raffle_id;
    }

    public function setRaffleId(int $raffle_id): self
    {
        $this->raffle_id = $raffle_id;

        return $this;
    }

    public function getParticipationId(): ?int
    {
        return $this->participation_id;
    }

    public function setParticipationId(int $participation_id): self
    {
        $this->participation_id = $participation_id;

        return $this;
    }

    public function getParticipationDate(): ?\DateTimeInterface
    {
        return $this->participation_date;
    }

    public function setParticipationDate(?\DateTimeInterface $participation_date): self
    {
        $this->participation_date = $participation_date;

        return $this;
    }

    public function getPrize(): ?string
    {
        return $this->prize;
    }

    public function setPrize(?string $prize): self
    {
        $this->prize = $prize;

        return $this;
    }

    public function getSaleId(): ?string
    {
        return $this->sale_id;
    }

    public function setSaleId(?string $sale_id): self
    {
        $this->sale_id = $sale_id;

        return $this;
    }

    public function getCouponCode(): ?string
    {
        return $this->coupon_code;
    }

    public function setCouponCode(?string $coupon_code): self
    {
        $this->coupon_code = $coupon_code;

        return $this;
    }

    public function getCustomerId(): ?string
    {
        return $this->customer_id;
    }

    public function setCustomerId(?string $customer_id): self
    {
        $this->customer_id = $customer_id;

        return $this;
    }

    public function isScratch(): ?bool
    {
        return $this->scratch;
    }

    public function setScratch(?bool $scratch): self
    {
        $this->scratch = $scratch;

        return $this;
    }

    public function isAssociatedRaffle(): ?bool
    {
        return $this->associated_raffle;
    }

    public function setAssociatedRaffle(?bool $associated_raffle): self
    {
        $this->associated_raffle = $associated_raffle;

        return $this;
    }

    public function getStore(): ?string
    {
        return $this->store;
    }

    public function setStore(?string $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getScratchDate(): ?\DateTimeInterface
    {
        return $this->scratch_date;
    }

    public function setScratchDate(?\DateTimeInterface $scratch_date): self
    {
        $this->scratch_date = $scratch_date;

        return $this;
    }

    public function getRafflehDate(): ?\DateTimeInterface
    {
        return $this->raffle_date;
    }

    public function setRafflehDate(?\DateTimeInterface $raffle_date): self
    {
        $this->raffle_date = $raffle_date;

        return $this;
    }

    public function getRaffleDate(): ?\DateTimeInterface
    {
        return $this->raffle_date;
    }

    public function setRaffleDate(?\DateTimeInterface $raffle_date): self
    {
        $this->raffle_date = $raffle_date;

        return $this;
    }
}
