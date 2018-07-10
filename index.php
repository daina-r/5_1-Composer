<?php
require __DIR__ . '/vendor/autoload.php';
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Домашнее задание к лекции "Менеджер зависимостей Composer"</title>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

    <style>
        html, body {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 10px 4px;
        }
        #map {
            width: 70%;
            height: 70%;
            margin: 20px 4px 0;
        }
    </style>
</head>
<body>
<h2>Поиск координат по адресу объекта</h2>
<?php if (empty($_GET)) :?>
<form method="post">
    <input name="search_address" type="text" placeholder="Введите адрес">
    <input type="submit" value="Найти">
</form>
<?php
endif;

if (!empty($_POST['search_address'])) :
$searchAddress = $_POST['search_address'];
$api = new \Yandex\Geo\Api();
$api->setQuery($searchAddress);
try {
    $api
        ->setLang(\Yandex\Geo\Api::LANG_RU)
        ->load();
} catch  (Exception $err) {
    echo $err->getMessage();
}


$response = $api->getResponse();
$search = $response->getQuery();
$collection = $response->getList();
?>
<table>
    <tr>
        <th>Адрес объекта</th>
        <th>Широта</th>
        <th>Долгота</th>
        <th></th>
    </tr>
    <?php foreach ($collection as $item) :?>
    <tr>
        <td><?=$item->getAddress();?></td>
        <td><?=$item->getLatitude();?></td>
        <td><?=$item->getLongitude();?></td>
        <td><a href="index.php?latitude=<?=$item->getLatitude();?>&longitude=<?=$item->getLongitude();?>">показать на карте</a></td>
    </tr>
    <?php endforeach;
endif; ?>
</table>
<?php
if (!empty($_GET['latitude']) && !empty($_GET['longitude'])) :
    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];
?>
    <script type="application/javascript">
        ymaps.ready(init);

        function init () {
            var myMap = new ymaps.Map("map", {
                    center: [<?=$latitude?>, <?=$longitude?>],
                    zoom: 12,
                    controls: []
                }, {
                    searchControlProvider: 'yandex#search'
                }),
                myPlacemark = new ymaps.Placemark([<?=$latitude?>, <?=$longitude?>], {
                    // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
                    balloonContentHeader: "Балун метки",
                    balloonContentBody: "Содержимое <em>балуна</em> метки",
                    balloonContentFooter: "Подвал",
                    hintContent: "Хинт метки"
                });

            myMap.geoObjects.add(myPlacemark);
        }
    </script>
    <a href="index.php">Вернуться к поиску</a>

    <div id="map"></div>
<?php endif;?>
</body>
</html>
