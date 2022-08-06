<?php

header('Content-Type: application/json');

global $qb;
global $user;

if (!isset($_GET['hash']) && $_GET['hash'] != 'JSON_UNESCAPED_UNICODE')
    return;

if (isset($_GET['method'])) {
    if ($_GET['method'] == 'check') {
        if (isset($_GET['user'])) {
            $json['success'] = !empty($user->getUserInfo($_GET['user']));
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
        }
    }
    else if ($_GET['method'] == 'hlist') {
        $idx = 0;
        $list = $qb->createQueryBuilder('users')->selectSql()->where('helper_level > 0')->andWhere('admin_level = 0')->orderBy('is_online DESC, helper_level DESC, count_hask DESC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $idx++;

            $uItem['idx'] = $idx;
            $uItem['name'] = $item['name'];
            $uItem['is_online'] = $item['is_online'];
            $uItem['helper_level'] = $item['helper_level'];
            $uItem['count_hask'] = $item['count_hask'];
            $uItem['online_day'] = round($item['online_cont'] * 8.5 / 60, 2);
            $uItem['login_timestamp'] = $item['login_date'] + 3600 * 3;
            $uItem['login_date'] = gmdate('H:i d-m-Y', $item['login_date'] + 3600 * 3);

            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'alist') {
        $idx = 0;
        $list = $qb->createQueryBuilder('users')->selectSql()->where('admin_level > 0')->andWhere('status_rp = 0')->orderBy('is_online DESC, admin_level DESC, count_aask DESC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $idx++;
            $countAll = $qb->createQueryBuilder('report_user_answer')->selectSql('COUNT(*) as count')->where('social_from = \'' . $item['social'] . '\'')->executeQuery()->getSingleResult();

            $uItem['idx'] = $idx;
            $uItem['name'] = $item['name'];
            $uItem['is_online'] = $item['is_online'];
            $uItem['admin_level'] = $item['admin_level'];
            $uItem['count_aask'] = $item['count_aask'];
            $uItem['online_day'] = round($item['online_cont'] * 8.5 / 60, 2);
            $uItem['login_timestamp'] = $item['login_date'] + 3600 * 3;
            $uItem['login_date'] = gmdate('H:i d-m-Y', $item['login_date'] + 3600 * 3);
            $uItem['count_report'] = $countAll;

            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else {
        echo 'GGWP';
    }
}
else {
    echo 'GGWP';
}