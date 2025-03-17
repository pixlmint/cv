<?php

namespace App\Controller;

use App\Helper\TechnologyHelper;
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
            'meta'    => $page->meta,
            'content' => $content,
        ]);
    }

    public function listTechnologyEntries(RequestInterface $request, TechnologyHelper $technologyHelper): HttpResponse
    {
        $route = $request->getRoute()->getPath();
        $re = '/tech\/(.*)$/';

        preg_match($re, $route, $matches);

        if (count($matches) !== 2) {
            throw new \Exception("Bad technology");
        }

        $technology = $matches[1];

        $ret = $technologyHelper->getEntriesWithTechnology($technology);

        return $this->render('category.html.twig', [
            'pages'      => $ret,
            'technology' => $technology,
        ]);
    }
}

