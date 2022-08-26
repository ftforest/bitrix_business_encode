<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\IO;
use Bitrix\Main\Application;

use \Bitrix\Iblock\Elements\ElementEdConditionsTable as EdConditions;
use \Bitrix\Iblock\Elements\ElementEdProductsTable as EdProducts;
use \Bitrix\Iblock\Elements\ElementEdRegionsTable as EdRegions;


\Bitrix\Main\Loader::includeModule('iblock');


$module_id = 'ftden45.edadealexpjson'; //обязательно, иначе права доступа не работают!

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
Loc::loadMessages(__FILE__);


\Bitrix\Main\Loader::includeModule($module_id);


$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

#Описание опций

$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('FTDEN45_EDADEALEXPJSON_TAB_SETTINGS'),
        'OPTIONS' => array(
            array('field_name_file_output', Loc::getMessage('FTDEN45_EDADEALEXPJSON_FIELD_NAME_FILE_EXPORT'),
                '',
                array('text', 50)),
            array('field_product_url', Loc::getMessage('FTDEN45_EDADEALEXPJSON_FIELD_PRODUCT_URL'),
                '/tovary.php?ELEMENT_ID=',
                array('text', 50)),
            /*array('field_text', Loc::getMessage('FTDEN45_EDADEALEXPJSON_FIELD_TEXT_TITLE'),
                '',
                array('textarea', 10, 50)),
            array('field_list', Loc::getMessage('FTDEN45_EDADEALEXPJSON_FIELD_LIST_TITLE'),
                '',
                array('multiselectbox',array('var1'=>'var1','var2'=>'var2','var3'=>'var3','var4'=>'var4'))),*/
        )
    ),
    array(
        "DIV" => "edit2",
        "TAB" => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    ),
);
#Сохранение

$name_file_output = 'file.json';
$edadeal_settings = [];
if ($request->isPost() && $request['Export'] && check_bitrix_sessid())
{

    foreach ($aTabs as $aTab)
    {
        //Или можно использовать __AdmSettingsSaveOptions($MODULE_ID, $arOptions);
        foreach ($aTab['OPTIONS'] as $arOption)
        {
            if (!is_array($arOption)) //Строка с подсветкой. Используется для разделения настроек в одной вкладке
                continue;

            if ($arOption['note']) //Уведомление с подсветкой
                continue;

            //Или __AdmSettingsSaveOption($MODULE_ID, $arOption);
            $optionName = $arOption[0];

            if ($optionName == 'field_name_file_output') $edadeal_settings['field_name_file_output'] = $optionValue = $request->getPost($optionName);
            else if ($optionName == 'field_product_url') $edadeal_settings['field_product_url'] = $optionValue = $request->getPost($optionName);
            else $optionValue = $request->getPost($optionName);

            Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue):$optionValue);
        }
    }
    $iblockId = 6;
    //var_dump(\Bitrix\Iblock\Iblock::wakeUp($iblockId)->getEntityDataClass());

    //var_dump($edadeal_settings);
    $server = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getServer();
    // var
    $file_edadeal = []; // final array
    $ed_regions = [];
    $ed_catalogs = [];
    $ed_catalog_item = [];
    $ed_offers = [];
    $ed_offers_id = [];
    $ed_conditions_elements = EdConditions::getList([
    //$elements = \Bitrix\Iblock\Elements\ElementEdproductsTable::getList([
        'select' => ['ID', 'NAME','OFFERS', 'CONDITIONS','IMAGE.FILE','TARGET_REGIONS',
                'DATA_END', 'DATA_START','IS_MAIN', 'DETAIL_PICTURE'
            ],
        'filter' => ['=ACTIVE' => 'Y'],
    ])->fetchCollection();
    echo "<pre>";
    foreach ($ed_conditions_elements as $ed_condition) {
        //var_dump($ed_condition);
        //var_dump($ed_condition->getDataEnd()->getValue());
        //var_dump($ed_condition->get('ID'));
        //var_dump($ed_condition->get('NAME'));
        //var_dump($ed_condition->get('CONDITIONS'));
        //var_dump($ed_condition->get('DATA_END'));
        //var_dump($ed_condition->get('DATA_START'));
        //echo "<br><hr><br>";
        $ed_catalog_offers_id = [];
        foreach ($ed_condition->getOffers()->getAll() as $value) {
            $ed_offers_item = [];
            $item_offer_id = (string)$value->getValue();
            //var_dump($item_offer_id);
            $ed_catalog_offers_id[] = (string)$item_offer_id;
            if (!in_array($item_offer_id,$ed_offers_id)) {
                $ed_offers_id[] = $item_offer_id;
                $ed_product_item = EdProducts::getByPrimary($value->getValue(), [
                    'select' => [
                            'ID',
                        'NAME',
                        'BARCODE_' => 'BARCODE',
                        'DATA_END_' => 'DATA_END',
                        'DATA_START_' => 'DATA_START',
                        'DESCRIPTION_' => 'DESCRIPTION',
                        'IMAGE_' => 'IMAGE',
                        'DETAIL_PICTURE',
                        'PRICE_IS_FROM_' => 'PRICE_IS_FROM',
                        'PRICE_NEW_' => 'PRICE_NEW',
                        'PRICE_OLD_' => 'PRICE_OLD',
                        'URL_' => 'URL',
                        'DISCOUNT_LABEL_' => 'DISCOUNT_LABEL'
                    ],
                ])->fetch();
                //var_dump($ed_product_item);
                $img_path = 'http://'.$server['SERVER_NAME'].CFile::GetPath($ed_product_item['DETAIL_PICTURE']);
                $ed_offers_item['barcode'] = $ed_product_item['BARCODE_VALUE'];
                if (isset($ed_product_item['DATA_END_VALUE'])) {
                    $date_end = DateTime::createFromFormat('Y-m-d H:i:s', $ed_product_item['DATA_END_VALUE']);
                    $ed_offers_item['date_end'] = date(DATE_RFC3339, $date_end->getTimestamp());
                }
                //echo $ed_product_item['DATA_START_VALUE'];
                if (isset($ed_product_item['DATA_START_VALUE'])) {
                    $date_start = DateTime::createFromFormat('Y-m-d H:i:s', $ed_product_item['DATA_START_VALUE']);
                    $ed_offers_item['date_start'] = date(DATE_RFC3339, $date_start->getTimestamp());
                }
                $ed_offers_item['description'] = $ed_product_item['NAME'];
                if (isset($ed_product_item['DISCOUNT_LABEL_VALUE'])) $ed_offers_item['discount_label'] = $ed_product_item['DISCOUNT_LABEL_VALUE'];
                $ed_offers_item['id'] = $ed_product_item['ID'];
                $ed_offers_item['image'] = $img_path;
                if (isset($ed_product_item['PRICE_IS_FROM_VALUE'])) $ed_offers_item['price_is_from'] = $ed_product_item['PRICE_IS_FROM_VALUE'];
                $ed_offers_item['price_new'] = $ed_product_item['PRICE_NEW_VALUE'];
                if (isset($ed_product_item['PRICE_OLD_VALUE'])) $ed_offers_item['price_old'] = $ed_product_item['PRICE_OLD_VALUE'];
                //if (isset($ed_product_item['URL_VALUE'])) $ed_offers_item['url'] = $ed_product_item['URL_VALUE'];
                $ed_offers_item['url'] = 'http://'.$server['SERVER_NAME'].trim($edadeal_settings['field_product_url']).$ed_product_item['ID'];
                $ed_offers[] = $ed_offers_item;
                /*$ed_offers[] = [
                    //"conditions" => $ed_condition->get('CONDITIONS')->getValue(),
                    "barcode" => $ed_product_item['BARCODE_VALUE'],
                    "date_end" => isset($ed_product_item['DATA_END_VALUE']) ? $ed_product_item['DATA_END_VALUE'] : '',
                    "date_start" => isset($ed_product_item['DATA_START_VALUE']) ? $ed_product_item['DATA_END_VALUE'] : '',
                    "description" => $ed_product_item['NAME'],
                    "discount_label" => isset($ed_product_item['DISCOUNT_LABEL_VALUE'],
                    "id" => $ed_product_item['ID'],
                    "image" => $img_path,
                    "price_is_from" => isset($ed_product_item['PRICE_IS_FROM_VALUE'],
                    "price_new" => $ed_product_item['PRICE_NEW_VALUE'],
                    "price_old" => isset($ed_product_item['PRICE_OLD_VALUE'],
                    "url" => isset($ed_product_item['URL_VALUE'],

                ];*/
            }
        }
        $ed_region_items = [];
        foreach ($ed_condition->getTargetRegions()->getAll() as $value) {
            //var_dump($value->getValue());

            $ed_region_item = EdRegions::getByPrimary($value->getValue(), [
                'select' => [
                    'NAME'
                ],
            ])->fetch();
            //var_dump($ed_product_item);
            $ed_region_items[] = $ed_region_item['NAME'];
        }
        //var_dump('/upload/' . $ed_condition->get('IMAGE')->getFile()->getSubdir().'/'.$ed_condition->get('IMAGE')->getFile()->getFileName());
        //echo $ed_condition->get('DETAIL_PICTURE');
        $img_path = CFile::GetPath($ed_condition->get('DETAIL_PICTURE'));

        $date_end = DateTime::createFromFormat('Y-m-d H:i:s', $ed_condition->get('DATA_END')->getValue());
        $date_end_condition = date(DATE_RFC3339, $date_end->getTimestamp());

        $date_start = DateTime::createFromFormat('Y-m-d H:i:s', $ed_condition->get('DATA_START')->getValue());
        $date_start_condition = date(DATE_RFC3339, $date_start->getTimestamp());

        $ed_catalogs[] = [
            //"conditions" => $ed_condition->get('CONDITIONS')->getValue(),
            "conditions" => $ed_condition->get('NAME'),
            "date_end" => $date_end_condition,
            "date_start" => $date_start_condition,
            "id" => $ed_condition->get('ID'),
            //"image" => $ed_condition->get('IMAGE')->getValue(),
            "image" => 'http://'.$server['SERVER_NAME'].$img_path,
            "is_main" => $ed_condition->get('IS_MAIN')->getValue() == 1 ? true : false,
            "offers" => $ed_catalog_offers_id,
            "delivery_regions" => $ed_region_items
        ];
    }
    echo "</pre>";

    $file_edadeal = [
        "catalogs" =>  $ed_catalogs,
        "offers" =>  $ed_offers,
        "version" =>  2
    ];

    if(isset($edadeal_settings['field_name_file_output']) && (string)$edadeal_settings['field_name_file_output'])
        $name_file_output = trim($edadeal_settings['field_name_file_output']);

    $file = new IO\File(Application::getDocumentRoot() . "/upload/edadeal_json/".$name_file_output);
    $file->putContents(json_encode($file_edadeal, JSON_UNESCAPED_UNICODE));

}

#Визуальный вывод

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
<form method='post' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request['mid'])?>&amp;lang=<?=$request['lang']?>' name='ftden45_edadealexpjson_settings'>

    <? foreach ($aTabs as $aTab):
            if($aTab['OPTIONS']):?>
        <? $tabControl->BeginNextTab(); ?>
        <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

    <?      endif;
        endforeach; ?>

    <?
    $tabControl->BeginNextTab();



    $tabControl->Buttons(); ?>

    <input type="submit" name="Export" value="<?echo Loc::getMessage('FTDEN45_EDADEALEXPJSON_EXPORT_EDADEAL')?>">
    <input type="reset" name="reset" value="<?echo GetMessage('MAIN_RESET')?>">
    <?=bitrix_sessid_post();?>
</form>
<? $tabControl->End(); ?>
