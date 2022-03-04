<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Notifications\CreationCompteNotification;
use App\Notifications\ActivationCompteNotification;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var CreationCompteNotification
     */
    private $notifyCreation;

    /**
     * @var ActivationCompteNotification
     */
    private $notifyActivation;

    public function __construct(CreationCompteNotification $notifyCreation, ActivationCompteNotification $notifyActivation)
    {
        $this->notifyCreation = $notifyCreation;
        $this->notifyActivation = $notifyActivation;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $userPasswordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        UsersAuthenticator $authenticator
        // \Swift_Mailer $mailer
    ): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->notifyCreation->notify();
            
            // $message = (new \Swift_Message('Activation de compte'))
            //     ->setFrom('blog@noreply')
            //     ->setTo($user->getEmail())
            //     ->setBody(
            //             $this->renderView(
            //                 'emails/activation.html.twig', ['token' => $user->getActivationToken()]
            //             ),
            //             'text/html'
            //         )
            //     ;
            // $mailer->send($message);

            // remplacé par :
            $this->notifyActivation->notify($user);

                    
            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UsersRepository $usersRepository)
    {
        $user = $usersRepository->findOneBy(['activation_token' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        $user->setActivationToken(null);

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        $this->addFlash('message', 'Vous avez bien activé votre compte');
        return $this->redirectToRoute('home');
    }
}
