<?

use Bitrix\Main;
$eventManager = Main\EventManager::getInstance();

//Вешаем обработчик на событие создания списка пользовательских свойств OnUserTypeBuildList
$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', ['ArticleContentsIblockUserProperty', 'GetUserTypeDescription']);
