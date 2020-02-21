<?php

declare(strict_types=1);

namespace App\View;

use Cake\View\View;

/**
 * Csv View
 * CSVビュー
 */
class CsvView extends View
{

    /**
     * Render a CSV view.
     *
     * @return string The rendered view.
     */
    public function render(): string
    {
        $text = '';
        foreach ($this->viewVars['csv'] as $row) {
            foreach($row as &$col) {
                if (!is_numeric(($col))) {
                    $col = '"' . str_replace('"', '""', $col) . '"';
                }
            }
            unset($col);
            $text .= implode(',', $row) . "\r\n";
        }
        return $text;
    }
}
