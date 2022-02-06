<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", name="sitemap", defaults={"_format"="xml"})
     */
    public function index(Request $request, ArticlesRepository $articlesRepository): Response
    {
        // Récupère le nom d'hôte depuis l'URL
        $hostname = $request->getSchemeAndHttpHost();

        // Initialise un tableau pour lister les URLs
        $urls = [];

        // Ajoute les URLs "statiques"
        $urls[] = ['loc' => $this->generateUrl('home')];
        $urls[] = ['loc' => $this->generateUrl('app_login')];
        $urls[] = ['loc' => $this->generateUrl('app_register')];

        // Ajoute les URLs "dynamiques"
        foreach ($articlesRepository->findAll() as $article) {
            $images = [
                'loc' => '/uploads/images/featured/' . $article->getFeaturedImage(),
                'title' => $article->getTitle()
            ];

            $urls[] = [
                'loc' => $this->generateUrl('article', [
                    'slug' => $article->getSlug()
                ]),
                'image' => $images,
                'lastmod' => $article->getUpdatedAt()->format('Y-m-d')
            ];
        }

        // Fabrique la réponse
        $response = new Response(
            $this->renderView('sitemap/index.html.twig', compact('urls', 'hostname')),
            200
        );

        // Ajout des entêtes HTTP
        $response->headers->set('Content-Type', 'text/xml');

        // Envoie la réponse
        return $response;
    }
}
