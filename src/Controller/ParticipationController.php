<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[ApiResource]
class ParticipationController extends AbstractController
{

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/participations/customer/{customerId}", name: "get_participations_by_customer")]

    public function getParticipationsByCustomer(string $customerId): JsonResponse
    {
        $participations = $this->entityManager->getRepository(Participation::class)->findBy(['customer_id' => $customerId]);

        $response = [];
        foreach ($participations as $participation) {
            $response[] = [
                'raffle_id' => $participation->getRaffleId(),
                'participation_id' => $participation->getParticipationId(),
                'participation_date' => $participation->getParticipationDate() ? $participation->getParticipationDate()->format('Y-m-d') : null,
                'prize' => $participation->getPrize(),
                'sale_id' => $participation->getSaleId(),
                'coupon_code' => $participation->getCouponCode(),
                'customer_id' => $participation->getCustomerId(),
                'scratch' => $participation->isScratch(),
                'associated_raffle' => $participation->isAssociatedRaffle(),
                'store' => $participation->getStore(),
            ];
        }

        return new JsonResponse(['participations' => $response]);
    }
}
