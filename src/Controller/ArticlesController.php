<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticlesController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Articles::class)->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{slug}", name="article")
     */
    public function article($slug): Response
    {
        $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException("L'article recherché n'existe pas");
        }

        return $this->render('articles/article.html.twig', compact('article'));
    }
}
