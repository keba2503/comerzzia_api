<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RaffleRegisterController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ParticipationRepository $participationRepository;

    public function __construct(EntityManagerInterface $entityManager, ParticipationRepository $participationRepository)
    {
        $this->entityManager = $entityManager;
        $this->participationRepository = $participationRepository;
    }

    #[Route("/api/participations/register/{participation_id}/{associated_raffle}", methods: ["PUT"])]
    public function actualizarParticipacion(int $participation_id, bool $associated_raffle): JsonResponse
    {
        // Busca la participación existente por su ID
        $participaciones = $this->participationRepository->findBy(['participation_id' => $participation_id]);

        if (empty($participaciones)) {
            return new JsonResponse(['message' => 'Participación no encontrada'], 404);
        }

        foreach ($participaciones as $participacion) {

            if ($participacion->isAssociatedRaffle() !== null) {
                return new JsonResponse(['message' => 'La participación ya está registrada en el sorteo'], 400);
            }

            $participacion->setAssociatedRaffle($associated_raffle);
            $participacion->setRaffleDate(new \DateTime());

            $this->entityManager->persist($participacion);
        }

        $this->entityManager->flush();

        // Prepara la respuesta con los datos actualizados de la participación
        $respuesta = [];
        foreach ($participaciones as $participacion) {
            $respuesta[] = [
                'raffle_id' => $participacion->getRaffleId(),
                'participation_id' => $participacion->getParticipationId(),
                'participation_date' => $participacion->getParticipationDate()?->format('Y-m-d H:i:s'),
                'prize' => $participacion->getPrize(),
                'sale_id' => $participacion->getSaleId(),
                'coupon_code' => $participacion->getCouponCode(),
                'customer_id' => $participacion->getCustomerId(),
                'scratch' => $participacion->isScratch(),
                'scratch_date' => $participacion->getScratchDate(),
                'associated_raffle' => $participacion->isAssociatedRaffle(),
                'raffle_date' => $participacion->getRaffleDate()->format('Y-m-d H:i:s'),
                'store' => $participacion->getStore(),
            ];
        }

        return new JsonResponse($respuesta);
    }
}
