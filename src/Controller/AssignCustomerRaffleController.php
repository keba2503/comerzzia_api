<?php

namespace App\Controller;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssignCustomerRaffleController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ParticipationRepository $participationRepository;

    public function __construct(EntityManagerInterface $entityManager, ParticipationRepository $participationRepository)
    {
        $this->entityManager = $entityManager;
        $this->participationRepository = $participationRepository;
    }

    #[Route("/api/participations/{participation_id}/customer/{customer_id}", methods: ["PUT"])]
    public function actualizarParticipacion(string $participation_id, string $customer_id): JsonResponse
    {
        // Busca la participación existente por participation_id
        $participacion = $this->participationRepository->findOneBy(['participation_id' => $participation_id]);

        if (!$participacion) {
            return new JsonResponse(['message' => 'Participación no encontrada'], 404);
        }

        // Asigna el customer_id a la participación
        $participacion->setCustomerId($customer_id);

        // Puedes actualizar más campos según sea necesario

        $this->entityManager->flush();

        // Prepara la respuesta con los datos actualizados de la participación
        $respuesta = [
            'raffle_id' => $participacion->getRaffleId(),
            'participation_id' => $participacion->getParticipationId(),
            'participation_date' => $participacion->getParticipationDate()?->format('Y-m-d'),
            'prize' => $participacion->getPrize(),
            'sale_id' => $participacion->getSaleId(),
            'coupon_code' => $participacion->getCouponCode(),
            'customer_id' => $participacion->getCustomerId(),
            'scratch' => $participacion->isScratch(),
            'associated_raffle' => $participacion->isAssociatedRaffle(),
            'store' => $participacion->getStore(),
        ];

        return new JsonResponse($respuesta);
    }
}
