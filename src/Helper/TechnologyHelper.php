<?php

namespace App\Helper;

use Nacho\Contracts\PageManagerInterface;

class TechnologyHelper
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function getEntriesWithTechnology(string $technology): array
    {
        $entries = [];
        $pages = $this->pageManager->getPages();
        foreach ($pages as $page) {
            $technologies = $page->meta->getAdditionalValues()->getOrNull('technologies');
            if (!is_null($technologies) && in_array($technology, $technologies)) {
                $entries[] = $page;
            }
        }

        return $entries;
    }
}
