<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Добавление в лог");
?>
<ul class="list-group">
  <li class="list-group-item">
    <a href="/local/logs/log_custom.log">Файл лога</a>,
    в лог добавленно 'Открыта страница writelog.php'
  </li>
</ul>
<?

\App\Debug\Log::addLog('Текущая дата и время', 'log_custom', true);

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>