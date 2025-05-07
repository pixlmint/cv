<?php

namespace App\Controller;

use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;

class NotFoundController extends AbstractController
{
    public function notFound(): HttpResponse
    {
        return $this->render('404.html.twig');
    }
}
