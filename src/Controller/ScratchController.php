<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScratchController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ParticipationRepository $participationRepository;

    public function __construct(EntityManagerInterface $entityManager, ParticipationRepository $participationRepository)
    {
        $this->entityManager = $entityManager;
        $this->participationRepository = $participationRepository;
    }

    #[Route("/api/participations/scratch/{participation_id}/{scratch}", methods: ["PUT"])]
    public function actualizarParticipacion(int $participation_id, bool $scratch): JsonResponse
    {
// Busca la participación existente por su ID
        $participaciones = $this->participationRepository->findBy(['participation_id' => $participation_id]);

        if (empty($participaciones)) {
            return new JsonResponse(['message' => 'Participación no encontrada'], 404);
        }

        foreach ($participaciones as $participacion) {
            if ($participacion->isScratch() !== null) {
                return new JsonResponse(['message' => 'La participación ya está rascada'], 400);
            }

            $participacion->setScratch($scratch);
            $participacion->setScratchDate(new \DateTime());

            $this->entityManager->persist($participacion);
        }

        $this->entityManager->flush();

// Prepara la respuesta con los datos actualizados de la participación
        $respuesta = [];
        foreach ($participaciones as $participacion) {
            $participationDate = $participacion->getParticipationDate();
            $scratchDate = $participacion->getScratchDate();
            $raffleDate = $participacion->getRaffleDate();

            $respuesta[] = [
                'raffle_id' => $participacion->getRaffleId(),
                'participation_id' => $participacion->getParticipationId(),
                'participation_date' => $participationDate !== null ? $participationDate->format('Y-m-d H:i:s') : null,
                'prize' => $participacion->getPrize(),
                'sale_id' => $participacion->getSaleId(),
                'coupon_code' => $participacion->getCouponCode(),
                'customer_id' => $participacion->getCustomerId(),
                'scratch' => $participacion->isScratch(),
                'scratch_date' => $scratchDate !== null ? $scratchDate->format('Y-m-d H:i:s') : null,
                'associated_raffle' => $participacion->isAssociatedRaffle(),
                'raffle_date' => $raffleDate !== null ? $raffleDate->format('Y-m-d H:i:s') : null,
                'store' => $participacion->getStore(),
            ];
        }

        return new JsonResponse($respuesta);
    }
}
