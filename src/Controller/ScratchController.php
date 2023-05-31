<?php
namespace App\Controller;

use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route("/api/participations/scratch/{id}/{scratch}", methods: ["PUT"])]
    public function actualizarParticipacion(int $id, bool $scratch): JsonResponse
    {
        // Busca la participación existente por su ID
        $participacion = $this->participationRepository->find($id);

        if (!$participacion) {
            return new JsonResponse(['message' => 'Participación no encontrada'], 404);
        }

        // Actualiza los datos de la participación
        $participacion->setScratch($scratch);
        $participacion->setScratchDate(new \DateTime());

        // Puedes actualizar más campos según sea necesario

        $this->entityManager->flush();

        // Prepara la respuesta con los datos actualizados de la participación
        $respuesta = [
            'raffle_id' => $participacion->getRaffleId(),
            'participation_id' => $participacion->getParticipationId(),
            'participation_date' => $participacion->getParticipationDate()?->format('Y-m-d H:i:s'),
            'prize' => $participacion->getPrize(),
            'sale_id' => $participacion->getSaleId(),
            'coupon_code' => $participacion->getCouponCode(),
            'customer_id' => $participacion->getCustomerId(),
            'scratch' => $participacion->isScratch(),
            'scratch_date' => $participacion->getScratchDate()?->format('Y-m-d H:i:s'),
            'associated_raffle' => $participacion->isAssociatedRaffle(),
            'raffle_date' => $participacion->getRafflehDate()?->format('Y-m-d H:i:s'),
            'store' => $participacion->getStore(),
        ];

        return new JsonResponse($respuesta);
    }
}
