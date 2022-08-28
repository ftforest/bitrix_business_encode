#Edadeal.ru
мой модуль``/local/modules/ftden45.edadealexpjson``

в файле настроект модуля весь код экспорта из трех инфоблоков
``/local/modules/ftden45.edadealexpjson/options.php``

в папке ``/upload/`` есть экспортированные инфоблоки Edadeal в xml для импорта в битрикс:

1. ``/upload/edadeal_conditions.xml``
``/upload/edadeal_conditions_files/``
2. ``/upload/edadeal_product.xml``
``/upload/edadeal_product_files/``
3. ``/upload/edadeal_regions.xml``
``/upload/edadeal_regions_files/``

в настройках можно указать название файла с расширением *.json
и url к товарам, на которые будут ссылаться из файла json.

Файл в формате json Edadeal.ru будет в папке ``/upload/edadeal_json/file.json``

Для моего модуля нужен `Символьный код API` для Инфоблоков:
1. ``EdRegions`` для Регионов поставки
2. ``EdProducts`` для Товаров
3. ``EdConditions`` для Условий скидок