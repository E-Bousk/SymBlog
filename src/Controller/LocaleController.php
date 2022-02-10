<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    /**
     * @Route("/change-locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request): Response
    {
        // Stock la langue demandée dans la session
        $request->getSession()->set('_locale', $locale);

        // Retourne sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
