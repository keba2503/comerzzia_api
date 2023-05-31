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

    #[Route("/api/assign/participations", methods: ["PUT"])]
    public function actualizarParticipacion(Request $request): JsonResponse
    {
        // Obtén los datos de la solicitud
        $requestData = json_decode($request->getContent(), true);

        // Aquí puedes acceder a los datos proporcionados en la solicitud
        $raffleId = $requestData['raffle_id'];
        $saleId = $requestData['sale_id'];
        $numberOfParticipations = $requestData['number_of_participations'];
        $purchaseDate = $requestData['purchase_date'];
        $store = $requestData['store'];
        $customerId = $requestData['customer_id'];

        // Busca participaciones existentes con los campos vacíos y raffle_id igual al de la solicitud
        $participations = $this->entityManager->getRepository(Participation::class)
            ->findBy([
                'raffle_id' => $raffleId,
                'sale_id' => null
            ]);


// Filtra las participaciones que cumplen las condiciones
        $filteredParticipations = [];
        $count = 0;
        foreach ($participations as $participation) {
            if ($count < $numberOfParticipations) {
                $filteredParticipations[] = $participation;
                $count++;
            }
        }

// Asigna los valores proporcionados a las participaciones filtradas
        $assignedParticipations = [];
        foreach ($filteredParticipations as $participation) {
            $participation->setSaleId($saleId);
            $participation->setParticipationDate(new \DateTime($purchaseDate));
            $participation->setStore($store);
            $participation->setCustomerId($customerId);
            $participation->setScratch(false);

            // Puedes asignar más valores según sea necesario

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

        // Prepara la respuesta con las participaciones asignadas
        $response = [
            'participations' => $assignedParticipations,
        ];

        return new JsonResponse($response);
    }
}
