<?php
$content = file_get_contents('data.json');
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
                        'code' => $k,
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
$rs = json_encode($data, JSON_UNESCAPED_UNICODE);
// file_put_contents('data_level.json', $rs);
echo $rs;
