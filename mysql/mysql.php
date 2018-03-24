<?php
$dsn = 'mysql:host=127.0.0.1;dbname=test';
$username = 'root';
$password = '123456';

$content = file_get_contents('../data.json');
$json = json_decode($content, true);
$province_count = 0;
$city_count = 0;
$county_count = 0;
$data = array();
foreach ($json as $k => $v) {
    if (substr($k, -4) == '0000') {
        if (in_array(mb_substr($v, -1, 1, 'utf-8'), ['市', '区'])) {
            $data['province'][] = [
                'code' => $k,
                'name' => $v,
                'city' => [
                    [
                        'code' => '',
                        'name' => $v
                    ]
                ]
            ];
            $city_count = 1;
            $county_count = 1;
        } else {
            $data['province'][] = [
                'code' => $k,
                'name' => $v
            ];
            $city_count = 0;
            $county_count = 0;
        }
        $province_count++;

    } else if (substr($k, -2) == '00') {
        $data['province'][$province_count - 1]['city'][$city_count] = [
            'code' => $k,
            'name' => $v
        ];
        $city_count++;
        $county_count = 0;
    } else {
        $data['province'][$province_count - 1]['city'][$city_count - 1]['county'][] = [
            'code' => $k,
            'name' => $v
        ];
        $county_count++;
    }
}

$sql = file_get_contents('area.sql');
$arr = explode(';', $sql);

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->exec("SET CHARACTER SET utf8");
    foreach ($arr as $v) {
        $pdo->query($v . ';');
    }
    foreach ($data['province'] as $k => $v) {
        $sql = 'INSERT INTO `areas`(`parentId`, `code`, `areaName`, `areaType`) VALUES (:parentId,:code,:areaName,:areaType)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':parentId' => 0, ':code' => $v['code'], ':areaName' => $v['name'], ':areaType' => 1));
        $province_id = $pdo->lastInsertId();
        foreach ($v['city'] as $k1 => $v1) {
            $stmt->execute(array(':parentId' => $province_id, ':code' => $v1['code'], ':areaName' => $v1['name'], ':areaType' => 2));
            $city_id = $pdo->lastInsertId();
            if (isset($v1['county']) && !empty($v1['county'])) {
                foreach ($v1['county'] as $k2 => $v2) {
                    $stmt->execute(array(':parentId' => $city_id, ':code' => $v2['code'], ':areaName' => $v2['name'], ':areaType' => 3));
                }
            }
        }
        /*if ($k == 0) {
            die();
        }*/

    }
} catch (PDOException $e) {
    echo $e->getMessage();
}


