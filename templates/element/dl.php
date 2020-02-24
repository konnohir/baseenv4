<?php
/**
 * @var \App\View\AppView $this
 * @var string|array $label
 * @var string $value
 * @var string $class
 * @var bool $escape
 */

if (is_array($label)) {
    $labelClass = $label['class'] ?? null;
    $labelEscape = $label['escape'] ?? true;
    $label = $label['text'];
}

if ($labelEscape ?? true) {
    $label = h($label);
}

if ($escape ?? true) {
    $value = h($value);
}

 ?>
<dl class="row">
    <dt class="col-md<?= isset($labelClass) ? ' ' . $labelClass : '' ?>"><?= $label ?></dt>
    <dd class="col-md<?= isset($class) ? ' ' . $class : '' ?>"><?= $value ?></dt>
</dl>