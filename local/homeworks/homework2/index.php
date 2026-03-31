<?

use Bitrix\Main\Page\Asset;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("ДЗ #2: Отладка и логирование");

Asset::getInstance()->addCss('//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');


?>
<h1 class="mb-3"><? $APPLICATION->ShowTitle() ?></h1>

<h4 class="mb-3">Выполнение ДЗ 2:Создать файлы для ДЗ согласно репозиторию https://github.com/OtusTeam/bitrix24.
  В нем написать код, который, при обращении к нему по HTTP, будет записывать в файл текущие дату и время.
  Написать и подключить собственный класс системного логгера, который будет переопределять форматирование строк лога - добавлять слово OTUS в каждую строку.</h4>
<p>Репозиторий :<a href="https://github.com/aplatov/bitrix24-project/tree/main"> https://github.com/aplatov/bitrix24-project/tree/main</a></p>
Битрикс24 : <a href="https://cg460987.tw1.ru/bitrix/">https://cg460987.tw1.ru/bitrix/</a> логин: admin пароль: Yw78mP!#
<div>
  Реализоано добавление в лог даты и времени, добавлена кастомизация вывода сообщения в системный лог исключений.
</div>
<br>
<br>
<hr>

<h4 class=" mb-3">Часть 1 - записываем в файл текущие дату и время</h4>
<ul class="list-group">
  <li class="list-group-item">
    <a href="/local/Logs/log_custom.log">Файл лога из п1 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="writelog.php">Добавление в лог из п1 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="clearlog.php">Очистить лог из п1 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2FApp%2FDebug%2FLog.php&full_src=Y">Файл с классом кастомного логгера</a>
  </li>
</ul>


<h4 class="mb-3 mt-5">Часть 2 - кастомизация вывода в Exception</h4>
<ul class="list-group">
  <li class="list-group-item">
    <a href="/local/Logs/exceptions.log">Файл лога из п2 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="writeexception.php">Добавление в лог из п2 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="clearexception.php">Очистить лог из п2 ДЗ</a>
  </li>
  <li class="list-group-item">
    <a href="/bitrix/admin/fileman_file_edit.php?path=%2Flocal%2FApp%2FDebug%2FLog.php&full_src=Y">Файл с классом </a>
  </li>
</ul>



<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>