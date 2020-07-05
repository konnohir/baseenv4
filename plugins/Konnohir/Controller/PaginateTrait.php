<?php

declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Konnohir\Controller;

use Cake\Http\Exception\NotFoundException;
use InvalidArgumentException;
use PDOException;

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
     * @return \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface|array Query results
     * @link https://book.cakephp.org/4/en/controllers.html#paginating-a-model
     * @throws \RuntimeException When no compatible table object can be found.
     * @var \Cake\Controller\Controller $this
     */
    public function paginate($object = null, array $settings = [])
    {
        try {
            return parent::paginate($object, $settings);
        } catch (NotFoundException $e) {
            // ページが見つからない場合、末尾のページに遷移
            $paging = $this->getRequest()->getAttribute('paging');
            $pageCount = $paging[key($paging)]['pageCount'];
            if ($pageCount <= 1) {
                $pageCount = null;
            }
            $routes = [
                'action' => $this->getRequest()->getParam('action')
            ];
            if ($this->getRequest()->getParam('pass.0')) {
                $routes[] = $this->getRequest()->getParam('pass.0');
            }
            $routes['?'] = ['page' => $pageCount] + $this->getRequest()->getQuery();
            $this->redirect($routes);
        } catch (InvalidArgumentException $e) {
            // ユーザー入力文字列不正
            // ex)数値型のカラムを文字列で検索
            $newRequest = $this->getRequest()->withAttribute('paging', [
                $object->getAlias() => [
                    'count' => 0,
                    'current' => 0,
                    'page' => 0,
                    'pageCount' => 0,
                    'start' => 0,
                    'end' => 0,
                ],
            ]);
            $this->setRequest($newRequest);
        } catch (PDOException $e) {
            // ユーザー入力文字列不正
            // ex)日付型のカラムを日付フォーマット以外で検索
            $newRequest = $this->getRequest()->withAttribute('paging', [
                $object->getAlias() => [
                    'count' => 0,
                    'current' => 0,
                    'page' => 0,
                    'pageCount' => 0,
                    'start' => 0,
                    'end' => 0,
                ],
            ]);
            $this->setRequest($newRequest);
        }
        return [];
    }
}
