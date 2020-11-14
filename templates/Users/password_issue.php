<?php
/**
 * @var \App\View\CsvView $this
 * @var \App\Model\Entity\User[] $users
 */

 // 出力ファイル名
$this->setFileName('password.csv');

// ヘッダー
$this->setHeader('ID');
$this->setHeader('Email');
$this->setHeader('Password');
$this->nextRow();

// 内容
foreach($users as $user)
{
    $this->write($user->id);
    $this->write($user->email);
    $this->write($user->plain_password);
    $this->nextRow();
}
