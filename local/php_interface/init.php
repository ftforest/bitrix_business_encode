<?php

global $arCustomTemplateEngines;
$arCustomTemplateEngines = array(
    "smarty" => array(
        "templateExt" => array("tpl"),
        "function" => "SmartyEngine"
    )
);

function SmartyEngine($templateFile, $arResult, $arParams, $arLangMessages, $templateFolder, $parentTemplateFolder, $template)
{
    if (!defined("SMARTY_DIR"))
        define("SMARTY_DIR2", "smarty");

    require_once(SMARTY_DIR2.'/libs/Smarty.class.php');

    $smarty = new Smarty;

    $smarty->setCompileDir(SMARTY_DIR2."/templates_c/");
    $smarty->setConfigDir(SMARTY_DIR2."/configs/");
    $smarty->setTemplateDir(SMARTY_DIR2."/templates/");
    $smarty->setCacheDir(SMARTY_DIR2."/cache/");

    //$smarty->testInstall();

    $smarty->assign("arResult", $arResult);
    $smarty->assign("arParams", $arParams);
    $smarty->assign("MESS", $arLangMessages);
    $smarty->assign("templateFolder", $templateFolder);
    $smarty->assign("parentTemplateFolder", $parentTemplateFolder);

    $smarty->display($_SERVER["DOCUMENT_ROOT"].$templateFile);
}