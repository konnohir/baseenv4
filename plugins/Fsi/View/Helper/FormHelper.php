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

namespace Fsi\View\Helper;

use Cake\View\Helper\FormHelper as Helper;
use Cake\View\View;

/**
 * Form helper library.
 *
 * Automatic generation of HTML FORMs from given data.
 *
 * @method string text(string $fieldName, array $options = [])
 * @method string number(string $fieldName, array $options = [])
 * @method string email(string $fieldName, array $options = [])
 * @method string password(string $fieldName, array $options = [])
 * @method string search(string $fieldName, array $options = [])
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @link https://book.cakephp.org/3.0/en/views/helpers/form.html
 */
class FormHelper extends Helper
{

    public function __construct(View $view, array $config = [])
    {
        $this->_defaultConfig['autoSetCustomValidity'] = false;
        $this->_defaultConfig['templates']['formStart'] = '<form{{attrs}} novalidate>';
        parent::__construct($view, $config);
    }

    public function customButton(string $fieldName, array $options = []): string
    {

        $options += [
            'type' => 'button',
        ];
        $options = $this->addClass($options, 'btn btn-sm');

        return parent::button($fieldName, $options);
    }

    public function customControl(string $fieldName, array $options = []): string
    {
        $options['templates'] = [];
        $options['templateVars'] = [];

        if (isset($options['customButton'])) {
            $label = $options['customButton']['label'] ?? '';
            unset($options['customButton']['label']);
            $options['templates'] += [
                // 'formGroup' => '{{label}}{{input}}<div class="input-group-append">{{button}}</div>'
                'input' => '<div class="input-group"><input type="{{type}}" name="{{name}}"{{attrs}}/><div class="input-group-append">{{button}}</div></div>',
            ];
            $options['templateVars'] += [
                'button' => $this->customButton($label, $options['customButton']),
            ];
            unset($options['customButton']);
        }

        // 'inputContainer' => '<div class="input {{type}}{{required}}">{{content}}</div>',
        // 'formGroup' => '{{label}}{{input}}',
        // 'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
        // 'label' => '<label{{attrs}}>{{text}}</label>',


        // $options['templates'] += [
        //     // 'inputContainer' => '<div style="background:#eee" class=" border-bottom input-group {{type}}{{required}}">{{content}}</div>',
        //     'inputContainer' => '<div style="background:#eee" class=" border-bottom form-row {{type}}{{required}}">{{content}}</div>',
        //     'formGroup' => '<div class="col-auto" style="width:12em">{{label}}</div><div class="col">{{input}}</div>',
        // ];
        $multiple = $options['multiple'] ?? null;
        $type = $options['type'] ?? null;
        if ($multiple !== 'checkbox' && $type !== 'checkbox' && $type !== 'radio'/* && !isset($options['options'])*/) {
            $options = $this->addClass($options, 'form-control form-control-sm');
        } else {
            $options = $this->addClass($options, 'ml-1');
            $options['templates'] += [
                'checkboxWrapper' => '<div class="checkbox form-check">{{label}}</div>',
            ];
        }

        return parent::control($fieldName, $options);
    }
}
