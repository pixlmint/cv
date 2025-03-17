<?php

namespace App\Hook;

use App\Controller\EntryController;
use Nacho\Contracts\Hooks\PreFindRoute;
use Nacho\Contracts\PageManagerInterface;
use Nacho\Models\PicoPage;

class ContentRouteHook implements PreFindRoute
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function call(array $routes, string $path): array
    {
        /** @var PicoPage[] $pages */
        $pages = $this->pageManager->getPages();

        $technologies = [];
        foreach ($pages as $page) {
            if ($page->id !== '/') {
                $routes[] = ['route' => $page->id, 'controller' => EntryController::class, 'function' => 'showEntry'];
                if ($page->meta->getAdditionalValues()->has('technologies')) {
                    foreach ($page->meta->getAdditionalValues()->get('technologies') as $technology) {
                        if (!in_array($technology, $technologies)) {
                            $technologies[] = $technology;
                        }
                    }
                }
            }
        }

        foreach ($technologies as $technology) {
            $routes[] = ['route' => '/tech/' . $technology, 'controller' => EntryController::class, 'function' => 'listTechnologyEntries'];
        }

        return $routes;
    }
}
