<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * 
 * @Route("/api", name="api_")
 */
class APIController extends AbstractController
{
    /**
     * @Route("/articles/list", name="list", methods={"GET"})
     */
    public function listArticle(ArticlesRepository $articlesRepository): Response
    {
        // Récupère la liste des articles
        $articles = $articlesRepository->apiFindAll();

        // Spécifie l'utilisation d'un encodeur JSON
        $encoders = [new JsonEncoder()];
        
        // Instancie un "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // Conversion :
        // Instancie le converstiseur
        $serializer = new Serializer($normalizers, $encoders);

        // Utilisation du convertisseur
        $jsonContent = $serializer->serialize($articles, 'json', [
            'circular_reference_handler' => function($object){
                return $object->getId();
            }
        ]);
        
        // Instancie la réponse
        $response = new Response($jsonContent);

        // Ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // Envoie la réponse
        return $response;
    }

    /**
     * @Route("/articles/read/{id}", name="read", methods={"GET"})
     */
    public function getArticle(Articles $article): Response
    {
        // Spécifie l'utilisation d'un encodeur JSON
        $encoders = [new JsonEncoder()];
        
        // Instancie un "normaliseur" pour convertir la collection en tableau
        $normalizers = [new ObjectNormalizer()];

        // Conversion :
        // Instancie le converstiseur
        $serializer = new Serializer($normalizers, $encoders);

        // Utilisation du convertisseur
        $jsonContent = $serializer->serialize($article, 'json', [
            'circular_reference_handler' => function($object){
                return $object->getId();
            }
        ]);
        
        // Instancie la réponse
        $response = new Response($jsonContent);

        // Ajoute l'entête HTTP
        $response->headers->set('Content-Type', 'application/json');

        // Envoie la réponse
        return $response;
    }


    /**
     * Ajout d'un article
     * 
     * @Route("/articles/add", name="add", methods={"POST"})
     */
    public function addArticle(Request $request)
    {
        // Vérifie si requête XMLHttpRequest
        // (commenté pour tester avec « REST client »)
        // if ($request->isXmlHttpRequest()){
            // Décode les données pour les vérifier
            $data = json_decode($request->getContent());
            // TODO : vérifier les données

            // Instancie un nouvel article
            $article = new Articles();

            // Hydrate l'article
            $article->setTitle($data->title);
            $article->setContent($data->content);
            $article->setFeaturedImage($data->image);
            // TODO : récupérer un utilisateur
            $user = $this->getDoctrine()->getRepository(Users::class)->find(1);
            $article->setUsers($user);

            // Sauvegarde en base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // Retourne la confirmation
            return new Response("L'article à bien été ajouté !", 201);
        // }
        // return new Response('Erreur', 404);
    }

    /**
     * Modifie un article
     * 
     * @Route("/articles/edit/{id}", name="edit", methods={"PUT"})
     */
    public function editArticle(?Articles $article, Request $request)
    {
        // Vérifie si requête XMLHttpRequest
        // (commenté pour tester avec « REST client »)
        // if ($request->isXmlHttpRequest()){
            // Décode les données pour les vérifier
            $data = json_decode($request->getContent());
            // TODO : vérifier les données

            $code = 200;
            // Vérifie si on a pas d'article
            if (!$article) {
                // Instancie un nouvel article
                $article = new Articles();

                // Passe le code à 201
                $code = 201;
            }

            // On hydrate notre article
            $article->setTitle($data->title);
            $article->setContent($data->content);
            $article->setFeaturedImage($data->image);
            // TODO : récupérer un utilisateur
            $user = $this->getDoctrine()->getRepository(Users::class)->find(1);
            $article->setUsers($user);

            // On sauvegarde en base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            // On retourne la confirmation
            return new Response("L'article à bien été modifié !", $code);
        // }
        // return new Response('Erreur', 404);
    }

    /**
     * Supprime un article
     * 
     * @Route("/articles/delete/{id}", name="delete", methods={"DELETE"})
     */
    public function deleteArticle(Articles $article)
    {
        // TODO Vérification de sécurité
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        // NOTE : par défaut, le code réponse est 200
        return new Response("L'article à bien été effacé !");

    }
}
