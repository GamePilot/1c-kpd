<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

if(empty($arResult))
	return false;
?>

<ul class="menu">
	<?foreach($arResult as $item):?>
		<li><a href="<?=$item["LINK"]?>"><?=$item["TEXT"]?></a></li>
	<?endforeach?>
</ul>
