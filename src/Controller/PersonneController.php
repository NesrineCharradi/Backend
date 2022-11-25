<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PersonneRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\Collection;

#[AsController]
class PersonneController
{

    public function __construct(PersonneRepository $personneRepository)
    {
         $this->personneRepository = $personneRepository;
    }

    /**
     * @Route("/addpersonne", name="add_personne", methods={"POST"})
     */
    public function add(Request $request ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nom = $data['nom'];
        $prenom = $data['prenom'];
        $email = $data['email'];
        $password = $data['password'];

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }

        $this->personneRepository->savePersonne($nom, $prenom, $email, $password);

        return new JsonResponse(['status' => 'Le personne est crée!'], Response::HTTP_CREATED);
    }


    /**
 * @Route("/customers/get", name="get_all_customers", methods={"GET"})
 */
public function getAll(): JsonResponse
{
    $personnes = $this->personneRepository->findAll();
    $data = [];

    foreach ($personnes as $personne) {
        $data[] = [
            'id' => $personne->getId(),
            'nom' => $personne->getNom(),
            'prenom' => $personne->getPrenom(),
            'email' => $personne->getLogin(),
            'password' => $personne->getPassword(),
        ];
    }

    return new JsonResponse($data, Response::HTTP_OK);
}


/**
 * @Route("/delete/{id}", name="delete_customer", methods={"DELETE"})
 */
public function delete($id): JsonResponse
{
    $personne = $this->personneRepository->findOneBy(['id' => $id]);

    $this->personneRepository->removePersonne($personne);

    return new JsonResponse(['status' => 'Customer deleted'], Response::HTTP_OK);
}

/**
 * @Route("/update/{id}", name="update_customer", methods={"PUT"})
 */
public function update($id, Request $request): JsonResponse
{
    $personne = $this->personneRepository->findOneBy(['id' => $id]);
    $data = json_decode($request->getContent(), true);

    empty($data['nom']) ? true : $personne->setNom($data['nom']);
    empty($data['prenom']) ? true : $personne->setPrenom($data['prenom']);
    empty($data['email']) ? true : $personne->setLogin($data['email']);
    empty($data['password']) ? true : $personne->setPassword($data['password']);

    $updatedCostumer = $this->personneRepository->updatePersonne($personne);

    return new JsonResponse($updatedCostumer->toArray(), Response::HTTP_OK);
}



}