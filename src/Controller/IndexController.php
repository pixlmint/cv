<?php

namespace App\Controller;

use App\Helper\PageAttributeHelper;
use Dom\Element;
use Dom\HTMLDocument;
use Nacho\Contracts\PageManagerInterface;
use Nacho\Controllers\AbstractController;
use Nacho\Models\HttpResponse;

use const Dom\HTML_NO_DEFAULT_NS;

class IndexController extends AbstractController
{
    private PageManagerInterface $pageManager;
    private PageAttributeHelper $pageAttributeHelper;

    public function __construct(PageManagerInterface $pageManager, PageAttributeHelper $pageAttributeHelper)
    {
        $this->pageManager = $pageManager;
        $this->pageAttributeHelper = $pageAttributeHelper;
    }

    public function index(): HttpResponse
    {
        $page = $this->pageManager->getPage('/');
        $content = $this->pageManager->renderPage($page);
        $doc = HTMLDocument::createFromString($content, HTML_NO_DEFAULT_NS | LIBXML_NOERROR);
        $tables = $doc->getElementsByTagName('table');
        $educationAndJobs = $this->tableToArray($tables[0]);

        $lead = $doc->getElementById('lead')->innerHTML;

        $lists = $doc->getElementsByTagName('ul');

        $allSkills = $this->pageAttributeHelper->getAvailableAttributeValues('technologies');
        $skills = [];
        foreach ($lists[0]->getElementsByTagName('img') as $skill) {
            /** @var Element $skill */
            $tech = $skill->getAttribute('alt');

            $skillArr = [
                'img' => $skill->getAttribute('src'),
                'alt' => $tech,
                'url' => null,
            ];

            if (in_array($tech, $allSkills)) {
                $skillArr['url'] = '/tech/' . $tech;
            }
            $skills[] = $skillArr;
        }

        $featured = array_map(function($id) {
            return $this->pageManager->getPage($id);
        }, $this->listToArray($lists[1]));


        return $this->render('home.html.twig', ['skills' => $skills, 'educationAndJobs' => $educationAndJobs, 'featured' => $featured, 'lead' => $lead]);
    }

    public function listToArray(Element $listElement): array
    {
        $ret = [];
        foreach ($listElement->getElementsByTagName('li') as $listItemElement) {
            $ret[] = $listItemElement->innerHTML;
        }

        return $ret;
    }

    /**
     * Converts an HTML table element to an array of associative arrays
     * 
     * @param \DOM\Element $tableElement The table element to process
     * @return array An array of associative arrays where keys are table headings
     */
    public function tableToArray(Element $tableElement): array
    {
        $result = [];
        $headers = [];

        // Find the thead element
        $theadElements = $tableElement->getElementsByTagName('thead');
        if ($theadElements->count() > 0) {
            $thead = $theadElements->item(0);
            $headerRows = $thead->getElementsByTagName('tr');

            // Get headers from the last row in thead (in case of multi-row headers)
            if ($headerRows->count() > 0) {
                $headerRow = $headerRows->item($headerRows->count() - 1);
                $headerCells = $headerRow->getElementsByTagName('th');

                // If no th elements found, try to use td elements
                if ($headerCells->count() === 0) {
                    $headerCells = $headerRow->getElementsByTagName('td');
                }

                // Extract header text
                foreach ($headerCells as $cell) {
                    $headers[] = trim($cell->textContent);
                }
            }
        } else {
            // No thead, try to get headers from the first row
            $rows = $tableElement->getElementsByTagName('tr');
            if ($rows->count() > 0) {
                $headerRow = $rows->item(0);
                $headerCells = $headerRow->getElementsByTagName('th');

                // If no th elements found, try to use td elements
                if ($headerCells->count() === 0) {
                    $headerCells = $headerRow->getElementsByTagName('td');
                }

                // Extract header text
                foreach ($headerCells as $cell) {
                    $headers[] = trim($cell->textContent);
                }
            }
        }

        // Find the tbody element
        $tbodyElements = $tableElement->getElementsByTagName('tbody');
        if ($tbodyElements->count() > 0) {
            $tbody = $tbodyElements->item(0);
            $dataRows = $tbody->getElementsByTagName('tr');
        } else {
            // No tbody, use all rows except the first one (assumed to be headers)
            $dataRows = $tableElement->getElementsByTagName('tr');

            // Skip the first row if we used it for headers and there's no thead
            if (count($headers) > 0 && $theadElements->count() === 0) {
                // Need to create a new list without the first element
                $filteredRows = [];
                for ($i = 1; $i < $dataRows->count(); $i++) {
                    $filteredRows[] = $dataRows->item($i);
                }

                // Reassign dataRows to our filtered collection
                $tempRows = $dataRows;
                $dataRows = $filteredRows;
            }
        }

        // Process data rows
        foreach ($dataRows as $row) {
            $cells = $row->getElementsByTagName('td');

            // Skip rows that don't have any cells
            if ($cells->count() === 0) {
                continue;
            }

            $rowData = [];
            for ($j = 0; $j < count($headers); $j++) {
                // Only process if we have both a header and a cell
                if (isset($headers[$j]) && $j < $cells->count()) {
                    $rowData[$headers[$j]] = trim($cells->item($j)->textContent);
                }
            }

            // Only add non-empty rows
            if (!empty($rowData)) {
                $result[] = $rowData;
            }
        }

        return $result;
    }
}
