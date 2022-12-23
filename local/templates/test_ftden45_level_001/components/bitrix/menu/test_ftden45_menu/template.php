<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul xmlns="http://www.w3.org/1999/html">

    <li><a href=""   class="menu-img-fon"  style="background-image: url(images/nv_home.png);" ><span></span></a></li>
    <?
    $previousLevel = 1;
    foreach($arResult as $arItem):?>

        <?php
    if (false) {
        echo "<pre>";
        print_r($arItem);
        echo "</pre>";
    }
        ?>

        <?if ($arItem["DEPTH_LEVEL"] == $previousLevel && $arItem["IS_PARENT"] == 1 && $arItem["DEPTH_LEVEL"] == 1):?>
            <li><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a><ul>
        <?elseif ($arItem["DEPTH_LEVEL"] == $previousLevel && $arItem["DEPTH_LEVEL"] == 1 && empty($arItem["IS_PARENT"])):?>
            <li><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a></li>
        <?elseif ($arItem["DEPTH_LEVEL"] == $previousLevel && $arItem["DEPTH_LEVEL"] > 1 && empty($arItem["IS_PARENT"])):?>
            <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
        <?elseif ($previousLevel < $arItem["DEPTH_LEVEL"]):?>
            <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
        <?elseif ($previousLevel > $arItem["DEPTH_LEVEL"] && $arItem["DEPTH_LEVEL"] == 1):?>
            </ul></li>
                <li><a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a></li>
        <?elseif ($previousLevel > $arItem["DEPTH_LEVEL"]):?>
            </ul></li>
                <li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
        <?endif?>

        <?$previousLevel = $arItem["DEPTH_LEVEL"];?>

    <?endforeach?>

    <?if ($previousLevel > 1)://close last item tags?>
        <?=str_repeat("</ul></li>", ($previousLevel-1) );?>
    <?endif?>

    <div class="clearboth"></div>
</ul>
<?endif?>
