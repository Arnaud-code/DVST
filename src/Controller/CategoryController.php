<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryController extends AbstractController
{
    /**
     * @Route("/{slug}", name="category_show", priority=-1)
     */
    public function show($slug, CategoryRepository $categoryRepository, ProductRepository $productRepository, SubscriptionRepository $subscriptionRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            // throw new NotFoundHttpException("La catégorie demandée n'existe pas");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        $subs = [];
        if ($this->getUser() instanceof UserInterface) {
            $subscriptions = $subscriptionRepository->findBy([
                'user' => $this->getUser(),
                'product' => $productRepository->findBy(['category' => $category])
            ]);
            foreach ($subscriptions as $subscription) {
                $subs[] = $subscription->getProduct();
            }
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'subscriptions' => $subs,
        ]);
    }
}
