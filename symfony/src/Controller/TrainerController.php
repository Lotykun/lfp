<?php

namespace App\Controller;

use App\Object\ApiErrorResponse;
use App\Service\TrainerManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrainerType;
use App\Entity\Trainer;

/**
 * Trainer controller.
 * @Route("/api", name="api_")
 */
class TrainerController extends AbstractFOSRestController
{
    /**
     * Lists all Trainers.
     * @Rest\Get("/trainers")
     *
     * @param TrainerManager $trainerManager
     * @return Response
     */
    public function getTrainersAction(TrainerManager $trainerManager): Response
    {
        try {
            $trainer = $trainerManager->getRepository()->findAll();
            return $this->handleView($this->view($trainer));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * List Trainer.
     * @Rest\Get("/trainer/{id}")
     *
     * @param TrainerManager $trainerManager
     * @param Trainer $trainer
     * @return Response
     */
    public function getTrainerAction(TrainerManager $trainerManager, Trainer $trainer): Response
    {
        try {
            return $this->handleView($this->view($trainer));
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    /**
     * Create team.
     * @Rest\Post("/trainer")
     * @Rest\Post("/trainer/{id}", defaults={"id" = null})
     *
     * @param Request $request
     * @param TrainerManager $trainerManager
     * @param Trainer|null $trainer
     * @return Response
     */
    public function postTrainerAction(Request $request, TrainerManager $trainerManager, Trainer $trainer = null): Response
    {
        try {
            if (!$trainer){
                $trainer = $trainerManager->create();
            }
            $form = $this->createForm(TrainerType::class, $trainer);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                $trainerManager->save($trainer);
                return $this->handleView($this->view($trainer, Response::HTTP_CREATED));
            }
        } catch (\Exception $e){
            $response = new ApiErrorResponse(Response::HTTP_INTERNAL_SERVER_ERROR, 'Exception: ' . $e->getMessage());
            return $this->handleView($this->view($response, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }
}
