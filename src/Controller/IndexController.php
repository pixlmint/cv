<?php

namespace App\Controller;

use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;

class IndexController extends AbstractController
{
    public function index(): HttpResponse
    {
        return $this->render('home.html.twig');
    }
}
