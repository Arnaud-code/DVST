<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(CategoryRepository $categoryRepository): Response
    {
        $showcases = $categoryRepository->findBy(['showcase' => 1], ['sort' => 'ASC']);
        $tiles = $categoryRepository->findBy(['showcase' => 0], ['sort' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'showcases' => $showcases,
            'tiles' => $tiles
        ]);
    }
}
