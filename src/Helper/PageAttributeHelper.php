<?php

namespace App\Helper;

use Nacho\Contracts\PageManagerInterface;
use Nacho\Models\PicoPage;

class PageAttributeHelper
{
    private PageManagerInterface $pageManager;

    public function __construct(PageManagerInterface $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    public function getEntriesWithAttribute(string $attribute, string $value): array
    {
        $entries = [];
        $pages = $this->pageManager->getPages();
        foreach ($pages as $page) {
            $attributes = $page->meta->getAdditionalValues()->getOrNull($attribute);
            if (!is_null($attributes) && in_array($value, $attributes)) {
                $entries[] = $page;
            }
        }

        return $entries;
    }

    public function getAvailableAttributeValues(string $attribute): array
    {
        $values = [];
        $arr = array_map(function(PicoPage $page) use ($attribute) {
            return $page->meta->getAdditionalValues()->getOrNull($attribute) ?? []; 
        }, $this->pageManager->getPages());
        array_walk_recursive($arr, function($a) use (&$values) { $values[] = $a; });

        return array_unique($values);
    }
}
