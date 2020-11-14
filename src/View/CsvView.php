<?php

declare(strict_types=1);

namespace App\View;

use Cake\View\View;
use Exception;

/**
 * Csv View
 * CSVビュー
 */
class CsvView extends View
{

    protected $row = [];

    protected $columnCount = 0;

    protected $columnMaxCount = 0;

    public function setFileName($csvFileName) {
        $this->autoLayout = false;
        $this->setResponse($this->getResponse()->withDownload($csvFileName)->withType('text/csv'));
    }

    public function setHeader($value) {
        $this->write($value);
        $this->columnMaxCount++;
    }

    public function write($col) {
        if (!is_numeric(($col))) {
            if ($col === false) {
                $col = '0';
            }else {
                $col = '"' . str_replace('"', '""', $col) . '"';
            }
        }
        $this->row[] = $col;

        $this->columnCount++;
    }

    public function nextRow() {
        if ($this->columnCount !== $this->columnMaxCount) {
            throw new Exception('Too few columns');
        }
        echo implode(',', $this->row) . "\r\n";
        $this->row = [];
        $this->columnCount = 0;
    }
}
