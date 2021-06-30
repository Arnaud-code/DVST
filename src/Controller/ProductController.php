<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if ($product->getAccess() !== "free") {
            $this->denyAccessUnlessGranted('CAN_USE', $product);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
