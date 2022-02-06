<?php

use Bitrix\Main\Loader;

//Автозагрузка наших классов
Loader::registerAutoLoadClasses(null, [
    'ArticleContentsIblockUserProperty' => APP_CLASS_FOLDER . 'ArticleContentsIblockUserProperty.php',
]);

