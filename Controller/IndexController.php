<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', []);
    }
}
