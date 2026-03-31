<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php"); ?>
<?php
$APPLICATION->SetTitle("Добавление в лог");
?>
<ul class="list-group">
  <li class="list-group-item">
    <a href="/local/Logs/log_custom.log">Файл лога</a>,
    в лог добавленно Текущая дата и время: <?= date('d.m.Y H:i:s'); ?>
  </li>
</ul>
<?

\App\Debug\Log::addLog(
  'Текущая дата и время: ' . date('d.m.Y H:i:s'),
  false,
  'log_custom',
  false
);

?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>