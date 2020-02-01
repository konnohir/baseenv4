<?php
declare(strict_types=1);

namespace Fsi\Controller;

use Cake\Http\Exception\NotFoundException;

/**
 * PaginateTrait
 */
trait PaginateTrait
{

    /**
     * Handles pagination of records in Table objects.
     *
     * Will load the referenced Table object, and have the PaginatorComponent
     * paginate the query using the request date and settings defined in `$this->paginate`.
     *
     * This method will also make the PaginatorHelper available in the view.
     *
     * @param \Cake\ORM\Table|string|\Cake\ORM\Query|null $object Table to paginate
     * (e.g: Table instance, 'TableName' or a Query object)
     * @param array $settings The settings/configuration used for pagination.
     * @return \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface Query results
     * @link https://book.cakephp.org/4/en/controllers.html#paginating-a-model
     * @throws \RuntimeException When no compatible table object can be found.
     */
    public function paginate($object = null, array $settings = [])
    {
        try {
            return parent::paginate($object, $settings);
        } catch (NotFoundException $e) {
            $obj = $this->getRequest()->getAttribute('paging');
            $page = $obj[key($obj)]['pageCount'];
            if ($page <= 1) {
                $page = null;
            }
            return $this->redirect([
                'action' => $this->getRequest()->getParam('action'),
                '?' => ['page' => $page] + $this->getRequest()->getQuery(),
            ]);
        }
    }
}