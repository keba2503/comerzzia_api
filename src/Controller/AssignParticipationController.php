<?php

namespace App\Controller;

use App\Entity\Participation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssignParticipationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/assign/participations", methods: ["POST"])]
    public function __invoke(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $raffleId = $requestData['raffle_id'];
        $saleId = $requestData['sale_id'];
        $numberOfParticipations = $requestData['number_of_participations'];
        $purchaseDate = $requestData['purchase_date'];
        $store = $requestData['store'];
        $customerId = $requestData['customer_id'];

        $participations = $this->entityManager->getRepository(Participation::class)
            ->findBy([
                'raffle_id' => $raffleId,
                'sale_id' => null
            ]);

        $filteredParticipations = array_slice($participations, 0, $numberOfParticipations);

        $assignedParticipations = [];
        foreach ($filteredParticipations as $participation) {
            $participation->setSaleId($saleId);
            $participation->setParticipationDate(new \DateTime($purchaseDate));
            $participation->setStore($store);
            $participation->setCustomerId($customerId);
            $participation->setScratch(false);

            $assignedParticipations[] = [
                'raffle_id' => $participation->getRaffleId(),
                'participation_id' => $participation->getParticipationId(),
                'participation_date' => $participation->getParticipationDate() ? $participation->getParticipationDate()->format('Y-m-d') : null,
                'prize' => $participation->getPrize(),
                'sale_id' => $participation->getSaleId(),
                'coupon_code' => $participation->getCouponCode(),
                'customer_id' => $participation->getCustomerId(),
                'scratch' => $participation->isScratch(),
                'scratch_date' => $participation->getScratchDate(),
                'associated_raffle' => $participation->isAssociatedRaffle(),
                'raffle_date' => $participation->getRafflehDate(),
                'store' => $participation->getStore(),
            ];

            $this->entityManager->persist($participation);
        }

        $this->entityManager->flush();

        $response = [
            'participations' => $assignedParticipations,
        ];

        return new JsonResponse($response);
    }
}
