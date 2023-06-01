<?php

namespace App\Controller;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[ApiResource]
class ParticipationEspecificController extends AbstractController
{

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/participation/{raffle_id}/{participation_id}", name: "get_participations_specific")]

    public function getParticipationsSpecific(string $participation_id, $raffle_id): JsonResponse
    {
        $participations = $this->entityManager->getRepository(Participation::class)->findBy([
            'participation_id' => $participation_id,
            'raffle_id' => $raffle_id
        ]);

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
