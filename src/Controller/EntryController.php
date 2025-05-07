<?php

namespace App\Controller;

use App\Helper\PageAttributeHelper;
use Nacho\Contracts\PageManagerInterface;
use Nacho\Contracts\RequestInterface;
use Nacho\Controllers\AbstractController;
use Nacho\Helpers\PageManager;
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

    public function showFolder(RequestInterface $request): HttpResponse
    {
        PageManager::$INCLUDE_PAGE_TREE = true;
        $this->pageManager->getPageTree();
        $route = $request->getRoute()->getPath();
        $page = $this->pageManager->getPage('/' . $route);
        $content = $this->pageManager->renderPage($page);
        $childPages = $page->children;
        PageManager::$INCLUDE_PAGE_TREE = false;



        return $this->render('category.html.twig', [
            'category_name' => $page->meta->title,
            'category_text' => $content,
            'pages' => $childPages,
        ]);
    }

    public function listTechnologyEntries(RequestInterface $request, PageAttributeHelper $attributeHelper): HttpResponse
    {
        $route = $request->getRoute()->getPath();
        $re = '/tech\/(.*)$/';

        preg_match($re, $route, $matches);

        if (count($matches) !== 2) {
            throw new \Exception("Bad technology");
        }

        $technology = $matches[1];

        $ret = $attributeHelper->getEntriesWithAttribute('technologies', $technology);

        $technologyEntry = $this->pageManager->getPage('/tech/' . $technology);

        return $this->render('category.html.twig', [
            'pages'         => $ret,
            'category_text' => $technologyEntry ? $this->pageManager->renderPage($technologyEntry) : null,
            'category_name' => $technology,
        ]);
    }

    public function listTagEntries(RequestInterface $request, PageAttributeHelper $attributeHelper): HttpResponse
    {
        $route = $request->getRoute()->getPath();
        $re = '/tag\/(.*)$/';

        preg_match($re, $route, $matches);

        if (count($matches) !== 2) {
            throw new \Exception("Bad Tag");
        }

        $tag = $matches[1];

        $ret = $attributeHelper->getEntriesWithAttribute('tags', $tag);

        return $this->render('category.html.twig', [
            'pages'         => $ret,
            'category_name' => $tag,
        ]);

    }
}

