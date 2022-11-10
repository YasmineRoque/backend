<?php

namespace App\Controller;

use App\Entity\Argonaute;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IndexController extends AbstractController
{
    #[Route('/argonaute', name: 'argonaute')]
    public function argonaute(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Argonaute::class);
        $argonautes = $repository->findAll();

        $payload = [];
        foreach ($argonautes as $argonaute) {
            array_push($payload, $argonaute->getName());
        }

        return new JsonResponse([
            'message' => 'Argonaute entity successfully retrieved.!',
            'argonautes' => $payload,
        ]);
    }

    #[Route('/argonaute/add', name: 'argonaute_add', methods: ['POST'])]
    public function addArgonaute(Request $request, ManagerRegistry $doctrine, ValidatorInterface $validator): Response
    {
        $postData = $request->toArray();

        $argonaute = new Argonaute();
        $argonaute->setName($postData['name']);

        $errors = $validator->validate($argonaute);
        if (count($errors) > 0) {

            $payload = [];
            foreach ($errors as $error) {
                array_push($payload, $error->getMessage());
            }

            return new JsonResponse([
                'message' => 'Validation Error',
                'errors' => $payload,
            ]);
        }

        $em = $doctrine->getManager();
        $em->persist($argonaute);
        $em->flush();

        return new JsonResponse([
            'message' => 'Argonaute created!',
            'argonautes' => $postData['name'],
        ]);
    }
}
