<?php

namespace App\Controller;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Contracts\RequestInterface;
use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;

class EntryController extends AbstractController
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function showEntry(RequestInterface $request): HttpResponse
    {
        $route = $request->getRoute()->getPath();
        $page = $this->pageManager->getPage('/' . $route);
        $content = $this->pageManager->renderPage($page);
        return $this->render('post.html.twig', [
            'title' => $page->meta->title,
            'content' => $content,
        ]);
    }
}

