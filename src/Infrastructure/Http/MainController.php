<?php

namespace App\Infrastructure\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main', methods: 'GET', stateless: true)]
    public function main(): Response
    {
        return new Response(null, 404);
    }
}