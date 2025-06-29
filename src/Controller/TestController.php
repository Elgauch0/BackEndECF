<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    #[Route('/', name: 'ping')]
    public function ping(): Response
    {
        return new Response(' El kaouri ,the server is is upp');
    }
}
