<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
\App\Debug\Log::cleanLog(
  'exceptions',
);

LocalRedirect('/local/homeworks/homework2/');
