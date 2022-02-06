<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Type\Date,
	Bitrix\Iblock;

Loc::loadMessages(__FILE__);

class ArticleContentsIblockUserProperty
{
	const USER_TYPE = 'ARTICLE_CONTENTS';

	/**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */

	public static function GetUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => Iblock\PropertyTable::TYPE_STRING,
			"USER_TYPE" => self::USER_TYPE,
			"DESCRIPTION" => "Содержимое статьи в виде блоков",
			//optional handlers
			"GetPropertyFieldHtmlMulty" => array(__CLASS__, "GetPropertyFieldHtmlMulty"),
			"GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
			"CheckFields" => array(__CLASS__, "CheckFields"),
			"ConvertToDB" => array(__CLASS__, "ConvertToDB"),
			"ConvertFromDB" => array(__CLASS__, "ConvertFromDB")
		);
	}

	

	/**
	 * Метод для вывода множественного свойства
	 * 
	 * Вызывается ядром Битрикс когда нужно получить верстку множественного свойства
	 * 
	 * @param array $arProperty - массив с информацией о свойстве (название, символьный код и прочее)
	 * @param array $value - массив содержащий значение свойства
	 * @param array $strHTMLControlName - массив содержащий информацию о имени свойства на форме редактирования элемента инфоблока.
	 * 
	 * @return String - html код свойства, который будет вставляться на форму редактирования элемента инфоблока
	 */

	public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
	{
		$arSortedValues = $value;
		if(!empty($value))
		{
			uasort(
				$arSortedValues,
				function($arVal1, $arVal2)
				{
					if($arVal1["VALUE"]["SORT"] < $arVal2["VALUE"]["SORT"])
					{
						return -1;
					}
					else if($arVal1["VALUE"]["SORT"] > $arVal2["VALUE"]["SORT"])
					{
						return 1;
					}
					else
					{
						return 0;
					}
				}
			);

			
			$arSortedValues["n0"] = [
				"VALUE" => [
					"TITLE" => "",
					"SORT" => "",
					"DESCRIPTION" => "",
					"IMAGE" => ""
				]
			];

		}
		else
		{
			$arSortedValues = [
				"n0" => [
					"VALUE" => [
						"TITLE" => "",
						"SORT" => "",
						"DESCRIPTION" => "",
						"IMAGE" => ""
					]
				]
			];
		}

		$sFieldHtml = "";

		$bFirstField = true;

		foreach($arSortedValues as $key => $arVals)
		{

			if($bFirstField == false)
			{
				$sFieldHtml .= "</td></tr><tr><td>";
				$bFirstField = false;
			}
			$bFirstField = false;
		

			$sFieldBlockWrapId = 'row_' . $key;

			$sFieldHtml .= '
			<div id="'. $sFieldBlockWrapId .'">
				<p><b>Заголовок блока:</b></p>
				<input type="text" size="70" name="' . $strHTMLControlName["VALUE"] . "[" . $key . '][TITLE]" value="'.htmlspecialcharsbx($arVals["VALUE"]["TITLE"]).'">
				<p><b>Сортировка:</b></p>
				<input type="text" size="5" name="' . $strHTMLControlName["VALUE"] . "[" . $key . '][SORT]" value="'.htmlspecialcharsbx($arVals["VALUE"]["SORT"]).'"><br>
				<p>Описание:</p>
				<textarea rows="5" cols="60" name="' . $strHTMLControlName["VALUE"] . "[" . $key . '][DESCRIPTION]">'.htmlspecialcharsbx($arVals["VALUE"]["DESCRIPTION"]).'</textarea>
				<p>Путь от кроня сайта до картинки</p>
				<input type="text" size="70" name="' . $strHTMLControlName["VALUE"] . "[" . $key . '][IMAGE]" value="'.htmlspecialcharsbx($arVals["VALUE"]["IMAGE"]).'"><br>
			';

			
			$sFieldHtml .= '<input type="button" style="height: auto;" value="Удалить блок" title="Удалить" onclick="document.getElementById(\''. $sFieldBlockWrapId .'\').parentNode.parentNode.remove()" />';

			$sFieldHtml .= "</div><hr>";

			

		}



		$sFieldHtml .= '
		
		</td></tr>
		<tr><td>
		<input type="button" value="Добавить" onclick="addNewBlock(event)">
		<script>
			function addNewBlock(event)
			{
				var propertyTableWrapID = event.target.parentNode.parentNode.parentNode.parentNode.id;
				BX.IBlock.Tools.addNewRow(propertyTableWrapID);

			}
		</script>
		</td></tr>
		
		';



		



		return $sFieldHtml;
	}

	/**
	 * Метод для вывода единичного свойства (т. е. свойства, не являющегося множественным)
	 * 
	 * Вызывается ядром Битрикс когда нужно получить верстку единичного свойства (т. е. свойства, не являющегося множественным)
	 * 
	 * @param array $arProperty - массив с информацией о свойстве (название, символьный код и прочее)
	 * @param array $value - массив содержащий значение свойства
	 * @param array $strHTMLControlName - массив содержащий информацию о имени свойства на форме редактирования элемента инфоблока.
	 * 
	 * @return String - html код свойства, который будет вставляться на форму редактирования элемента инфоблока
	 */
	public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{

		$sFieldHtml = "";
		

		$sFieldBlockWrapId = 'row_' . uniqid();

		$sFieldHtml .= '
		<div id="'. $sFieldBlockWrapId .'">
			<p><b>Заголовок блока:</b></p>
			<input type="text" size="70" name="' . $strHTMLControlName["VALUE"] . '[TITLE]" value="'.htmlspecialcharsbx($value["VALUE"]["TITLE"]).'">
			<p><b>Сортировка:</b></p>
			<input type="text" size="5" name="' . $strHTMLControlName["VALUE"] . '[SORT]" value="'.htmlspecialcharsbx($value["VALUE"]["SORT"]).'"><br>
			<p>Описание:</p>
			<textarea rows="5" cols="60" name="' . $strHTMLControlName["VALUE"] . '[DESCRIPTION]">'.htmlspecialcharsbx($value["VALUE"]["DESCRIPTION"]).'</textarea>
		';

		$arHTMLControlNameFileInput = $strHTMLControlName;
		$arHTMLControlNameFileInput["VALUE"] = $strHTMLControlName["VALUE"] . "[IMAGE]";

		$arValueFileInput = $value;
		$arValueFileInput["VALUE"] = $value["VALUE"]["IMAGE"];

		$sFieldHtml .= CIBlockPropertyFileMan::GetPropertyFieldHtml($arProperty, $arValueFileInput, $arHTMLControlNameFileInput);

		$sFieldHtml .= "</div>";

        return $sFieldHtml;
	}

	

	/**
	 * Проверка корректности заполнения поля.
	 * 
	 * Вызывается Битриксом перед сохранением значения свойства в БД.
	 * 
	 * @param array - $arProperty - массив с информацией о свойстве (название, символьный код и прочее)
	 * @param array - $value - массив содержащий значение свойства
	 * 
	 * @return array - Массив строк. Каждая строка является сообщением об ошибках
	 */
	public static function CheckFields($arProperty, $value)
	{
		$arErrors = [];

		// Если поле пытались заполнять, то проверяем корректно ли оно заполнено
		if(
			!empty($value["VALUE"]["TITLE"])
			|| !empty($value["VALUE"]["SORT"])
			|| !empty($value["VALUE"]["DESCRIPTION"])
			|| !empty($value["VALUE"]["IMAGE"])
		)
		{
			if(empty($value["VALUE"]["TITLE"]))
			{
				$arErrors[] = 'Поле "Заголовок" обязательно для заполнения';
			}
			if(
				empty($value["VALUE"]["SORT"])
				&& ($value["VALUE"]["SORT"] !== 0)
				&& ($value["VALUE"]["SORT"] !== "0")
				)
			{
				$arErrors[] = 'Поле "Сортировка" обязательно для заполнения';
			}
		}
		
        return $arErrors;
	}

	/**
	 * Конвертация данных перед сохранением в БД
	 * 
	 * @param array - $arProperty - массив с информацией о свойстве (название, символьный код и прочее)
	 * @param array - $value - массив содержащий значение свойства
	 * 
	 * @return String - Строка, которая будет записана в БД в качестве значения свойства
	 */
	public static function ConvertToDB($arProperty, $value)
	{
        // Если поле пытались заполнять, то сохраняем значение
        if(
			!empty($value["VALUE"]["TITLE"])
			|| !empty($value["VALUE"]["SORT"])
			|| !empty($value["VALUE"]["DESCRIPTION"])
			|| !empty($value["VALUE"]["IMAGE"])
		)
        {
            try {
                $value['VALUE'] = base64_encode(serialize($value["VALUE"]));
            } catch(Bitrix\Main\ObjectException $exception) {
            }
        } else {
            $value['VALUE'] = '';
        }
        return $value;
	}

	/**
	 * Конвертируем данные при извлечении из БД
	 * 
	 * @param array - $arProperty - массив с информацией о свойстве (название, символьный код и прочее)
	 * @param array - $value - массив содержащий значение свойства
	 * @param String - $format - ???  (мне так и не удалось узнать, что это за параметр и для чего он нужен)
	 * 
	 * @return mixed - Значение, которое будет подставлено в инпуты данного свойства на форме редактирования элемента инфоблока.
	 */
	public static function ConvertFromDB($arProperty, $value, $format = '')
	{
		if (!empty($value['VALUE']))
        {
            try
            {
                $value['VALUE'] = unserialize(base64_decode($value['VALUE']));
            } catch(Bitrix\Main\ObjectException $exception)
            {
            }
        }
        return $value;
	}

	
}