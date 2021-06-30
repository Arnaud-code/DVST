<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnectionController extends AbstractController
{
    /**
     * @Route("/connection", name="connection")
     */
    public function connection(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('identification');
        }

        return $this->render('connection/info.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/identification", name="identification")
     */
    public function identification(): Response
    {
        # code...
        return $this->render('connection/identification.html.twig', [
            'controller_name' => 'ConnectionController',
        ]);
    }
}
