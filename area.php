<?php

$area_json = file_get_contents('area.json');
$data_array = json_decode($area_json, true);

$province_total = 0;
$city_total = 0;
$district_total = 0;

$levelData = array();
$now_province_code = 0;
$now_city_code = 0;

foreach ($data_array as $k => $v) {
    if (substr($v['code'], -4) == '0000') {
        $province_total++;
        // echo $v['code'], '--', iconv('utf-8', 'gbk//IGNORE', $v['cName']), "\n\r";
        $levelData[$v['code']] = $v;
        $now_province_code = $v['code'];
        if (in_array($v['code'], ['110000', '120000', '310000', '500000'])) {
            $v['code'] = (string)($v['code'] + 1);
            $levelData[substr($v['code'], 0, 2) . '0000']['city'][$v['code']] = $v;
            $now_city_code = $v['code'];
        }
    } elseif (substr($v['code'], -2) == '00') {
        $city_total++;
        $levelData[substr($v['code'], 0, 2) . '0000']['city'][$v['code']] = $v;
        $now_city_code = $v['code'];
    } else {
        $district_total++;
        $levelData[$now_province_code]['city'][$now_city_code]['district'][$v['code']] = $v;
    }
}

$result = [
    'province_total' => $province_total,
    'city_total' => $city_total,
    'district_total' => $district_total,
    'levelData' => $levelData,
];
echo json_encode($result, JSON_UNESCAPED_UNICODE);

