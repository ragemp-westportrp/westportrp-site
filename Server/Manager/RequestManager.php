<?php

namespace Server\Manager;

use Server\Core\QueryBuilder;
use Server\UnitPay;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Request
 */
class RequestManager
{
    protected $qb;

    /**
     * @param QueryBuilder $qb
     */
    public function checkRequests(QueryBuilder $qb) {
        $this->qb = $qb;

        global $server;
        global $methods;
        global $modal;
        global $userInfo;
        global $user;

        if (isset($_GET['paymentId'])) {

            $success = true;
            $modal['show'] = true;
            $modal['title'] = 'Поздравляем!';
            $modal['text'] = 'Спасибо за пожертвование, ваш кошелёк StateCoin был пополнен.';
            
        }

        if(!empty($_POST)) {
            if (isset($_POST['act-login'])) {
                $password = hash('sha256', $_POST['pass']);
                $userInfoTemp = $user->getAccountInfo(strtolower($_POST['login']));

                setcookie('timeout', true, time() + 3, '/');

                if (isset($_SESSION['time']) && $_SESSION['time'] > time() || isset($_COOKIE['timeout'])) {
                    $modal['show'] = true;
                    $modal['text'] = 'Таймаут на авторизацию 3 секунды';
                }
                else if (empty($userInfoTemp)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Не верно введен логин';
                    $_SESSION['time'] = time() + 3;

                }
                else {

                    if ($userInfoTemp['password'] == $password) {

                        $token = hash('sha256', $userInfoTemp['login'] . $userInfoTemp['id']) . $server->generateToken();

                        $qb
                            ->createQueryBuilder('accounts')
                            ->updateSql(['token'], [$token])
                            ->where('id = \'' . $userInfoTemp['id'] . '\'')
                            ->executeQuery()
                            ->getResult()
                        ;

                        $server->setCookie('user', $token);
                        header('Location: /profile');
                        return;

                    }
                    else {
                        $_SESSION['time'] = time() + 3;
                        $modal['show'] = true;
                        $modal['text'] = 'Не верно введен пароль';
                    }
                }
            }
            if (isset($_POST['act-get-login'])) {

                global $user;

                $userInfoTemp = $user->getAccountInfo(strtolower($_POST['login']));

                setcookie('timeout', true, time() + 3, '/');

                if (isset($_SESSION['time']) && $_SESSION['time'] > time() || isset($_COOKIE['timeout'])) {
                    $modal['show'] = true;
                    $modal['text'] = 'Таймаут на авторизацию 3 секунды';
                }
                else if (empty($userInfoTemp)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Игрок с таким SocialClub не существует, вы можете пойти и зарегестрировать аккаунт';
                    $_SESSION['time'] = time() + 3;

                }
                else {

                    if ($userInfoTemp['email'] == $_POST['email']) {
                        header('Location: /heremypass?hash=' . hash('sha256', $userInfoTemp['login'] . '_' . $userInfoTemp['social'] . '_' . $userInfoTemp['id']) . '&login=' . $userInfoTemp['login']);
                        return;
                    }
                    else {
                        $_SESSION['time'] = time() + 3;
                        $modal['show'] = true;
                        $modal['text'] = 'Вы не правильно ввели Email. Если вы его забыли, обратитесь в дискорд к администрации для восстановления';
                    }
                }
            }
            if (isset($_POST['act-change-pass'])) {

                global $user;

                $userInfoTemp = $user->getAccountInfo($_POST['hash']);

                setcookie('timeout', true, time() + 3, '/');

                if (isset($_SESSION['time']) && $_SESSION['time'] > time() || isset($_COOKIE['timeout'])) {
                    $modal['show'] = true;
                    $modal['text'] = 'Таймаут на авторизацию 3 секунды';
                }
                else if (empty($userInfoTemp)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Ошибка доступа';
                    $_SESSION['time'] = time() + 3;

                }
                else {
                    if ($_POST['password1'] != $_POST['password2']) {
                        $modal['show'] = true;
                        $modal['text'] = 'Пароли не совпадают';
                        return;
                    }

                    $qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['password'], [hash('sha256', $_POST['password1'])])
                        ->where('id = ' . $userInfoTemp['id'])
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили пароль';

                    header('Location: /profile');
                    return;
                }
            }
            if (isset($_POST['edit-acc-column'])) {

                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $key = $server->charsString($_POST['key']);
                    $val = $server->charsString($_POST['val']);
                    $valo = $server->charsString($_POST['oldv']);

                    if ($key == 'money_donate') return false;

                    $qb
                        ->createQueryBuilder('log_admin_acc_edit')
                        ->insertSql(
                            ['name', 'user_id', 'col', 'val_old', 'val_new'],
                            [$userInfo['login'] . ' (' . $userInfo['id'] . ')', intval($_POST['id']), $key, $valo, $val]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    if ($key == 'admin_level' && $val > 10) {

                        $modal['show'] = true;
                        $modal['text'] = 'В доступе отказано';
                        return;
                    }

                    $qb
                        ->createQueryBuilder('accounts')
                        ->updateSql([$key], [$val])
                        ->where('id = \'' . intval($_POST['id']) . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Колонке ' . $key . ' было присвоено значение ' . $val;
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['edit-table-column'])) {

                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $key = $server->charsString($_POST['key']);
                    $val = $server->charsString($_POST['val']);
                    $valo = $server->charsString($_POST['oldv']);
                    $table = $server->charsString($_POST['table']);

                    if ($table == 'accounts' && $key == 'money_donate') return false;

                    $qb
                        ->createQueryBuilder('log_admin_table_edit')
                        ->insertSql(
                            ['tbl', 'name', 'row_id', 'col', 'val_old', 'val_new'],
                            [$table, $userInfo['login'] . ' (' . $userInfo['id'] . ')', intval($_POST['id']), $key, $valo, $val]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $qb
                        ->createQueryBuilder($table)
                        ->updateSql([$key], [$val])
                        ->where('id = \'' . intval($_POST['id']) . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Колонке ' . $key . ' было присвоено значение ' . $val;
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['delete-table-row'])) {

                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $table = $server->charsString($_POST['table']);

                    $qb
                        ->createQueryBuilder('log_admin_table_edit')
                        ->insertSql(
                            ['tbl', 'name', 'row_id', 'col', 'val_old', 'val_new'],
                            [$table, $userInfo['login'] . ' (' . $userInfo['id'] . ')', intval($_POST['id']), 'DEL_ROW', 'DEL_ROW', 'DEL_ROW']
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $qb
                        ->createQueryBuilder($table)
                        ->deleteSql()
                        ->where('id = \'' . intval($_POST['id']) . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы удалили строку под номером ' . $_POST['id'] . ' из таблицы ' . $table;
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['insert-table-row'])) {

                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $table = $server->charsString($_POST['table']);

                    $keys = [];
                    $vals = [];

                    $result = $qb
                        ->createQueryBuilder($table)
                        ->selectSql()
                        ->limit(1)
                        ->orderBy('id DESC')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    foreach ($result as $key => $value) {
                        if ($key != 'id') {
                            if (isset($_POST['val-' . $key])) {
                                $val = $server->charsString($_POST['val-' . $key]);
                                if ($val != '') {
                                    array_push($vals, $val);
                                    array_push($keys, $key);
                                }
                            }
                        }
                    }

                    if (count($vals) > 0) {
                        $qb
                            ->createQueryBuilder($table)
                            ->insertSql($keys, $vals)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $result = $qb
                            ->createQueryBuilder($table)
                            ->selectSql('id')
                            ->limit(1)
                            ->orderBy('id DESC')
                            ->executeQuery()
                            ->getSingleResult()
                        ;

                        $qb
                            ->createQueryBuilder('log_admin_table_edit')
                            ->insertSql(
                                ['tbl', 'name', 'row_id', 'col', 'val_old', 'val_new'],
                                [$table, $userInfo['login'] . ' (' . $userInfo['id'] . ')', $result['id'], 'NEW_ROW', 'NEW_ROW', 'NEW_ROW']
                            )
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'В таблицу ' . $table . ' была добавлена новая строка';
                    }
                    else {
                        $modal['show'] = true;
                        $modal['text'] = 'Произошла неизвестная ошибка';
                    }
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['add-promo-spec'])) {
                if ($user->isLogin() && $userInfo['admin_level'] >= 1) {

                    $result = $qb
                        ->createQueryBuilder('promocode_top_list')
                        ->selectSql()
                        ->where('promocode = \'' . strtoupper($_POST['promo']) . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    if (empty($result)) {
                        $qb
                            ->createQueryBuilder('promocode_top_list')
                            ->insertSql(['promocode'], [strtoupper($_POST['promo'])])
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Промокод был добавлен: ' . strtoupper($_POST['promo']);
                    }
                    else {
                        $modal['show'] = true;
                        $modal['text'] = 'Промокод уже существует';
                    }
                }
            }
            if (isset($_POST['save-rules'])) {
                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $result = $qb
                        ->createQueryBuilder('rules')
                        ->updateSql(['text'], [$_POST['text']])
                        ->where('id = ' . intval($_POST['id']))
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Правила проекта были отредактированы';
                }
            }
            if (isset($_POST['edit-user-column'])) {

                if ($user->isLogin() && $userInfo['allow_acc']) {

                    $key = $server->charsString($_POST['key']);
                    $val = $server->charsString($_POST['val']);
                    $valo = $server->charsString($_POST['oldv']);

                    $qb
                        ->createQueryBuilder('log_admin_user_edit')
                        ->insertSql(
                            ['name', 'user_id', 'col', 'val_old', 'val_new'],
                            [$userInfo['login'] . ' (' . $userInfo['id'] . ')', intval($_POST['id']), $key, $valo, $val]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $qb
                        ->createQueryBuilder('users')
                        ->updateSql([$key], [$val])
                        ->where('id = \'' . intval($_POST['id']) . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Колонке ' . $key . ' было присвоено значение ' . $val;
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['send-faq'])) {

                if ($user->isLogin() && $userInfo['admin_level'] > 0) {

                    $title = $server->charsString($_POST['title']);
                    $img = $server->charsString($_POST['img']);
                    $text = $server->charsString($_POST['text']);

                    $qb
                        ->createQueryBuilder('faq_list')
                        ->insertSql(
                            ['title', 'img', 'text'],
                            [$title, $img, $text]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Статья была добавлена';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }
            if (isset($_POST['edit-faq'])) {

                if ($user->isLogin() && $userInfo['admin_level'] > 0) {

                    $id = intval($_POST['id']);
                    $title = $server->charsString($_POST['title']);
                    $img = $server->charsString($_POST['img']);
                    $text = $server->charsString($_POST['text']);

                    $qb
                        ->createQueryBuilder('faq_list')
                        ->updateSql(
                            ['title', 'img', 'text'],
                            [$title, $img, $text]
                        )
                        ->where('id = ' . $id)
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Статья была отредактирована';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'Аккаунт не авторизован или не достаточно прав';
                }
            }

            if (isset($_POST['buy-dc'])) {
                $_POST['sum'] = intval($_POST['sum']);

                if($userInfo['money_donate'] >= $_POST['sum'] && $userInfo['money_donate'] > 0 && $_POST['sum'] >= 1) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $_POST['sum'];
                    $idx = 1;
                    $moneyServer = 1;

                    $moneyConvert =  $uInf['money'] + ($_POST['sum'] * round(300 * $moneyServer * $idx));

                    $do = 'Convert ' . $_POST['sum'] . 'dc. to  $' . ($_POST['sum'] * round(300 * $moneyServer * $idx)) . '. Balance: ' . $moneyDonateConvert . '. $Balance: ' . $moneyConvert;


                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $_POST['sum']]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$moneyConvert])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $userInfo = $user->getAccountInfo($userInfo['id']);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы успешно перевели STATECOIN в игровую валюту.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-nc'])) {

                $uInf = $user->getUserInfo(intval($_POST['uid']));
                $_POST['sum'] = intval($_POST['sum']);

                if($uInf['money_donate'] >= $_POST['sum'] && $uInf['money_donate'] > 0 && $_POST['sum'] >= 1) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $uInf['money_donate'] - $_POST['sum'];
                    $idx = 1;
                    $moneyServer = 1;

                    $moneyConvert =  $uInf['money'] + ($_POST['sum'] * round(100 * $moneyServer * $idx));


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$moneyConvert])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы успешно перевели NETCOIN в игровую валюту.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно NETCOIN.';
                }
            }
            if (isset($_POST['buy-nat'])) {

                global $nationals;

                $idx = intval($_POST['national']);
                $sum = 49;
                $national = $nationals[$idx];

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to NATIONAL. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['national'], [$national])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили национальность вашего персонажа.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-custom'])) {

                global $nationals;

                $sum = 99;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to CUSTOM. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['is_custom'], [0])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили внешность вашего персонажа, теперь зайдите в игру.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-skill'])) {

                global $nationals;

                $sum = 99;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;

                    $do = 'Convert ' . $sum . 'dc. to SKILLS. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['stats_strength', 'stats_shooting', 'stats_endurance', 'stats_lung_capacity', 'stats_flying', 'stats_driving'], [99, 99, 99, 99, 99, 99])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы прокачали все навыки вашего персонажа.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-status'])) {

                global $nationals;

                $sum = 199;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;

                    $do = 'Convert ' . $sum . 'sc. to STATUS. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['reg_status'], [2])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили гражданство.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-warn'])) {

                global $nationals;

                $sum = 499;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['warns'] < 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет предупреждений.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;

                    $do = 'Convert ' . $sum . 'dc. to WARN. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['warns'], [$uInf['warns'] - 1])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сняли предупреждение с аккаунта.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-dating'])) {

                global $nationals;

                $sum = 99;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to DELETE ALL DATING. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('user_dating')
                        ->deleteSql()
                        ->where('user_owner = \'' . $uInf['id'] . '\'')
                        ->orWhere('user_id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы все ваши знакомства были удалены.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['remove-dating'])) {

                global $nationals;

                $sum = 3;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));
                    $removes = explode('|', $_POST['removes']);

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to DELETE DATING. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('user_dating')
                        ->deleteSql()
                        ->where('id = \'' . $removes[0] . '\'')
                        ->orWhere('id = \'' . $removes[1] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы удалили ваше знакомство.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-nick'])) {

                $sum = 199;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $nickname = $server->charsString($_POST['name']);
                    $nickname = preg_replace('/\s\s+/', ' ', $nickname);
                    $nickname = preg_replace('/[^a-zA-Z\s]+/', '', $nickname);

                    if (count(explode(' ', $nickname)) < 2) {
                        $modal['show'] = true;
                        $modal['text'] = 'Ник введён не по формату, введите: Имя Фамилия.';
                        return false;
                    }

                    if (!empty($user->getUserInfo($nickname))) {
                        $modal['show'] = true;
                        $modal['text'] = 'Данный ник уже занят.';
                        return false;
                    }

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to NICK. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['name'], [$nickname])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили ник вашего персонажа.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-phone'])) {

                $sum = 499;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $newPhone = intval($_POST['new-phone']);
                    if (strlen($newPhone) != 10) {
                        $modal['show'] = true;
                        $modal['text'] = 'Телефон должен состоять только из 10 цифр, не больше и не меньше.';
                        return false;
                    }

                    $isValidUser = $qb
                        ->createQueryBuilder('users')
                        ->selectSql()
                        ->where('phone = \'' . $newPhone . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;
                    $isValidItem = $qb
                        ->createQueryBuilder('items')
                        ->selectSql()
                        ->where('params LIKE \'%' . $newPhone . '%\' AND item_id = 29')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    if (!empty($isValidUser) || !empty($isValidItem)) {
                        $modal['show'] = true;
                        $modal['text'] = 'Данный телефон уже занят.';
                        return false;
                    }

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['phone'] == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо экипировать телефон, а после уже сменить его номер.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PHONE NUMBER. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $qb
                        ->createQueryBuilder('items')
                        ->otherSql('UPDATE items SET params = REPLACE(params, \'' . $uInf['phone'] . '\', \'' . $newPhone . '\') WHERE params LIKE \'%' . $uInf['phone'] . '%\' AND item_id = 29', false)
                        ->executeQuery()
                        ->getSingleResult()
                    ;
                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['phone'], [$newPhone])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили номер телефона.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-card'])) {

                $sum = 199;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {


                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    $prefix = substr($uInf['bank_card'], 0, 4);
                    $newCard = intval($prefix . $_POST['new-card']);

                    if (strlen($newCard) != 16) {
                        $modal['show'] = true;
                        $modal['text'] = 'Карта должена состоять только из 12 цифр, не больше и не меньше.';
                        return false;
                    }

                    $isValidUser = $qb
                        ->createQueryBuilder('users')
                        ->selectSql()
                        ->where('bank_card = \'' . $newCard . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;
                    $isValidItem = $qb
                        ->createQueryBuilder('items')
                        ->selectSql()
                        ->where('params LIKE \'%' . $newCard . '%\' AND item_id = 50')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    if (!empty($isValidUser) || !empty($isValidItem)) {
                        $modal['show'] = true;
                        $modal['text'] = 'Данный телефон уже занят.';
                        return false;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['bank_card'] == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо экипировать карту, а после уже сменить ее номер.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to CARD NUMBER. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $qb
                        ->createQueryBuilder('items')
                        ->otherSql('UPDATE items SET params = REPLACE(params, \'' . $uInf['bank_card'] . '\', \'' . $newCard . '\') WHERE params LIKE \'%' . $uInf['bank_card'] . '%\' AND item_id = 50', false)
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['bank_card'], [$newCard])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы сменили номер карты.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-slot'])) {

                $sum = 499;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $slot = intval($_POST['slot']);

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['car_id' . $slot . '_free'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы уже купили этот слот.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to SLOT ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot . '_free'], [1])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили дополнительный слот для транспорта.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['buy-slot-d'])) {

                $sum = 3000000;
                $slot = intval($_POST['slot']);

                $uInf = $user->getUserInfo(intval($_POST['uid']));

                if($uInf['is_online'] == 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Необходимо выйти с сервера.';
                    return false;
                }
                if($uInf['money'] < $sum) {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас нет $' . number_format($sum) . ' наличными.';
                    return false;
                }
                if($uInf['car_id' . $slot . '_free'] == 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вы уже купили этот слот.';
                    return false;
                }

                $moneyDonateConvert = $uInf['money'] - $sum;

                $do = 'Convert ' . $sum . '$. to SLOT ' . $slot . '. Balance: ' . $moneyDonateConvert;
                $this->qb
                    ->createQueryBuilder('log_donate_trade')
                    ->insertSql(
                        ['user_id', 'action', 'money_was', 'money_remove'],
                        [$uInf['id'], $do, $uInf['money'], $sum]
                    )
                    ->executeQuery()
                    ->getSingleResult()
                ;


                $this->qb
                    ->createQueryBuilder('users')
                    ->updateSql(['car_id' . $slot . '_free', 'money'], [1, $moneyDonateConvert])
                    ->where('id = \'' . $uInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы купили дополнительный слот для транспорта.';
            }

            if (isset($_POST['dc-pack-oracle'])) {

                $sum = 299;

                $vName = 'Premier';
                if ($_POST['group1'] == 2)
                    $vName = 'Enduro';
                if ($_POST['group1'] == 3)
                    $vName = 'Virgo3';
                if ($_POST['group1'] == 4)
                    $vName = 'Issi3';

                $vehInfo = $this->qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql()
                    ->where('display_name = \'' . $vName . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['donate_pack1'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы уже покупали этот набор.';
                        return false;
                    }
                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов под транспорт.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_1 (' . $vehInfo['display_name'] . ') ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $rpDateTime = $qb
                        ->createQueryBuilder('daynight')
                        ->selectSql()
                        ->limit(1)
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $currentDate = '01/01/' . $rpDateTime['year'];
                    $endDate = '01/01/' . ($rpDateTime['year'] + 2);


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(
                            [
                                'car_id' . $slot,
                                'money',
                                'work_lvl',
                                'donate_pack1',
                                'a_lic',
                                'a_lic_create',
                                'a_lic_end',
                                'b_lic',
                                'b_lic_create',
                                'b_lic_end',
                                'c_lic',
                                'c_lic_create',
                                'C_lic_end',
                            ],
                            [
                                $lastCar['id'],
                                $uInf['money'] + 75000,
                                $uInf['work_lvl'] + 1,
                                1,
                                1,
                                $currentDate,
                                $endDate,
                                1,
                                $currentDate,
                                $endDate,
                                1,
                                $currentDate,
                                $endDate,
                            ]
                        )
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 7);
                    $user->giveRandomMask($uInf['id'], 70);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили стартовый набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-sultan'])) {

                $sum = 999;

                $vName = 'Cheburek';
                if ($_POST['group1'] == 2)
                    $vName = 'Kanjo';
                if ($_POST['group1'] == 3)
                    $vName = 'Granger';
                if ($_POST['group1'] == 4)
                    $vName = 'Sultan2';

                $vehInfo = $this->qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql()
                    ->where('display_name = \'' . $vName . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['donate_pack2'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы уже покупали этот набор.';
                        return false;
                    }
                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов под транспорт.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_2 (' . $vehInfo['display_name'] . ') ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $rpDateTime = $qb
                        ->createQueryBuilder('daynight')
                        ->selectSql()
                        ->limit(1)
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(
                            [
                                'car_id' . $slot,
                                'money',
                                'work_lvl',
                                'donate_pack2',
                                'stats_strength', 'stats_shooting', 'stats_endurance', 'stats_lung_capacity', 'stats_flying', 'stats_driving'
                            ],
                            [
                                $lastCar['id'],
                                $uInf['money'] + 250000,
                                $uInf['work_lvl'] + 2,
                                1,
                                99, 99, 99, 99, 99, 99
                            ]
                        )
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 14);
                    $user->giveRandomMask($uInf['id'], 50);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-seven'])) {

                $sum = 4999;

                $vName = 'Dubsta';
                if ($_POST['group1'] == 2)
                    $vName = 'Drafter';
                if ($_POST['group1'] == 3)
                    $vName = 'Superd';
                if ($_POST['group1'] == 4)
                    $vName = 'Akuma';

                $vehInfo = $this->qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql()
                    ->where('display_name = \'' . $vName . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($uInf['donate_pack3'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы уже покупали этот набор.';
                        return false;
                    }
                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов под транспорт.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_3 (' . $vehInfo['display_name'] . ') ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $rpDateTime = $qb
                        ->createQueryBuilder('daynight')
                        ->selectSql()
                        ->limit(1)
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(
                            [
                                'car_id' . $slot,
                                'money',
                                'work_lvl',
                                'donate_pack3',
                                'car_id6_free', 'car_id7_free', 'car_id8_free', 'car_id9_free', 'car_id10_free'
                            ],
                            [
                                $lastCar['id'],
                                $uInf['money'] + 1000000,
                                $uInf['work_lvl'] + 3,
                                1,
                                1, 1, 1, 1, 1
                            ]
                        )
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 30);
                    $user->giveRandomMask($uInf['id'], 30);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-1'])) {

                $sum = 99;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_1. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 25000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 1);
                    $user->giveRandomMask($uInf['id'], -1);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-2'])) {

                $sum = 199;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 50000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 3);
                    $user->giveRandomMask($uInf['id'], -1);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-3'])) {

                $sum = 499;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 125000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 7);
                    $user->giveRandomMask($uInf['id'], -1);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-4'])) {

                $sum = 999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 250000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 14);
                    $user->giveRandomMask($uInf['id'], 70);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-5'])) {

                $sum = 1999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 500000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 30);
                    $user->giveRandomMask($uInf['id'], 70);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-6'])) {

                $sum = 4999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$uInf['money'] + 1500000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 60);
                    $user->giveRandomMask($uInf['id'], 70);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-7'])) {

                $sum = 9999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if ($slot === 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободного слота под транспорт.';
                        return false;
                    }

                    if($uInf['car_id' . $slot] > 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Этот слот занят другим автомобилем.';
                        return false;
                    }


                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $vehInfo = $this->qb
                        ->createQueryBuilder('veh_info')
                        ->selectSql()
                        ->where('id = \'' . intval(843) . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete', 'tax_sale'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1, 100]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot, 'money'], [$lastCar['id'], $uInf['money'] + 3000000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 90);
                    $user->giveRandomMask($uInf['id'], 40);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-8'])) {

                $sum = 14999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if ($slot === 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободного слота под транспорт.';
                        return false;
                    }

                    if($uInf['car_id' . $slot] > 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Этот слот занят другим автомобилем.';
                        return false;
                    }


                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $vehInfo = $this->qb
                        ->createQueryBuilder('veh_info')
                        ->selectSql()
                        ->where('id = \'' . intval(766) . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete', 'tax_sale'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1, 100]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot, 'money'], [$lastCar['id'], $uInf['money'] + 5000000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 120);
                    $user->giveRandomMask($uInf['id'], 40);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['dc-pack-9'])) {

                $sum = 29999;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if ($slot === 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободного слота под транспорт.';
                        return false;
                    }

                    if($uInf['car_id' . $slot] > 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Этот слот занят другим автомобилем.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to PACK_D_2. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $vehInfo = $this->qb
                        ->createQueryBuilder('veh_info')
                        ->selectSql()
                        ->where('id = \'' . intval(840) . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete', 'tax_sale'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1, 100]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot, 'money'], [$lastCar['id'], $uInf['money'] + 11000000])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $user->giveVipAccount($uInf['id'], 2, 150);
                    $user->giveRandomMask($uInf['id'], 40);

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили набор.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['buy-car'])) {

                global $donateCars;


                $vehInfo = $this->qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql()
                    ->where('id = \'' . intval($_POST['vid']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                //$sum = intval($vehInfo['price_dc'] / 2 - 1);
                $sum = intval($vehInfo['price_dc']);

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов или вы не выбрали нужный слот.';
                        return false;
                    }
                    if($uInf['car_id' . $slot] > 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Этот слот занят другим автомобилем.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'dc. to VEHICLE (' . $vehInfo['display_name'] . ') ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete', 'tax_sale'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1, 100]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot], [$lastCar['id']])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили транспорт.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }

            if (isset($_POST['buy-car-2'])) {

                global $donateCars;


                $vehInfo = $this->qb
                    ->createQueryBuilder('veh_info')
                    ->selectSql()
                    ->where('id = \'' . intval($_POST['vid']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                //$sum = intval($vehInfo['price_dc'] / 2 - 1);
                $sum = intval($vehInfo['price_dc'] * 3);

                $uInf = $user->getUserInfo(intval($_POST['uid']));

                if($uInf['money_donate'] >= $sum && $uInf['money_donate'] > 0 && $sum > 0) {

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }
                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов или вы не выбрали нужный слот.';
                        return false;
                    }
                    if($uInf['car_id' . $slot] > 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'Этот слот занят другим автомобилем.';
                        return false;
                    }

                    $moneyDonateConvert = $uInf['money_donate'] - $sum;


                    $do = 'Convert ' . $sum . 'bp. to VEHICLE (' . $vehInfo['display_name'] . ') ' . $slot . '. Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $uInf['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete', 'tax_sale'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], 0, $vehInfo['fuel_full'], 1, 100]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;


                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot], [$lastCar['id']])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили транспорт.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно BONUS POINT.';
                }
            }

            if (isset($_POST['buy-vip-light'])) {

                $days = intval($_POST['days']);
                $sum = 0;
                if ($days === 1)
                    $sum = 20;
                if ($days === 3)
                    $sum = 60;
                if ($days === 7)
                    $sum = 100;
                if ($days === 14)
                    $sum = 200;
                if ($days === 30)
                    $sum = 400;
                if ($days === 60)
                    $sum = 750;
                if ($days === 90)
                    $sum = 1100;

                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;
                    $idx = 1;
                    $moneyServer = 1;

                    $do = 'Convert ' . $sum . 'dc. to VIP LIGHT (' . $days . 'd). Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $offsetTime = 0;

                    if ($uInf['vip_time'] > time()) {
                        $offsetTime = intval($uInf['vip_time'] - time());
                    }

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['vip_type', 'vip_time'], [1, time() + $offsetTime + $days * 86400])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили VIP LIGHT на ' . $days . 'д.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-vip-hard'])) {

                $days = intval($_POST['days']);
                $sum = 0;
                if ($days === 1)
                    $sum = 30;
                if ($days === 3)
                    $sum = 80;
                if ($days === 7)
                    $sum = 160;
                if ($days === 14)
                    $sum = 300;
                if ($days === 30)
                    $sum = 600;
                if ($days === 60)
                    $sum = 1100;
                if ($days === 90)
                    $sum = 1600;


                if($userInfo['money_donate'] >= $sum && $userInfo['money_donate'] > 0 && $sum > 0) {

                    $uInf = $user->getUserInfo(intval($_POST['uid']));

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $userInfo['money_donate'] - $sum;
                    $idx = 1;
                    $moneyServer = 1;

                    $do = 'Convert ' . $sum . 'dc. to VIP HARD (' . $days . 'd). Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $userInfo['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $offsetTime = 0;

                    if ($uInf['vip_time'] > time()) {
                        if ($uInf['vip_type'] == 1)
                            $offsetTime = intval(($uInf['vip_time'] - time()) / 2);
                        if ($uInf['vip_type'] == 2)
                            $offsetTime = intval($uInf['vip_time'] - time());
                    }

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['vip_type', 'vip_time'], [2, time() + $offsetTime + $days * 86400])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили VIP HARD на ' . $days . 'д.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно STATECOIN.';
                }
            }
            if (isset($_POST['buy-vip-hard-2'])) {

                $days = intval($_POST['days']);
                $sum = 0;
               
                if ($days === 7)
                    $sum = 500;
                if ($days === 14)
                    $sum = 1000;
                if ($days === 30)
                    $sum = 2000;
                if ($days === 60)
                    $sum = 3000;
                if ($days === 90)
                    $sum = 4000;


                $uInf = $user->getUserInfo(intval($_POST['uid']));
                if($uInf['money_donate'] >= $sum && $uInf['money_donate'] > 0 && $sum > 0) {

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    $moneyDonateConvert = $uInf['money_donate'] - $sum;
                    $idx = 1;
                    $moneyServer = 1;

                    $do = 'Convert ' . $sum . 'dc. to VIP HARD (' . $days . 'd). Balance: ' . $moneyDonateConvert;
                    $this->qb
                        ->createQueryBuilder('log_donate_trade')
                        ->insertSql(
                            ['user_id', 'action', 'money_was', 'money_remove'],
                            [$uInf['id'], $do, $uInf['money_donate'], $sum]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $offsetTime = 0;

                    if ($uInf['vip_time'] > time()) {
                        if ($uInf['vip_type'] == 1)
                            $offsetTime = intval(($uInf['vip_time'] - time()) / 2);
                        if ($uInf['vip_type'] == 2)
                            $offsetTime = intval($uInf['vip_time'] - time());
                    }

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['vip_type', 'vip_time'], [2, time() + $offsetTime + $days * 86400])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money_donate'], [$moneyDonateConvert])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы купили VIP HARD на ' . $days . 'д.';
                }
                else {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно BONUS POINT.';
                }
            }

            if (isset($_POST['pay-tax-vip'])) {

                $transferPrice = intval($_POST['price']);

                $usrInf = $user->getUserInfo(intval($_POST['uid']));

                if($usrInf['is_online'] == 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вы находитесь на сервере.';
                    return false;
                }
                if(hash('sha256', $usrInf['name']) != $_POST['hex']) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вы находитесь на сервере.';
                    return false;
                }

                if ($usrInf["vip_type"] === 0) {
                    $modal['show'] = true;
                    $modal['text'] = 'У Вас нет вип статуса.';
                    return false;
                }

                if ($usrInf['login_date'] < time() - (86400 * 7) && $usrInf['id'] != 633) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вы не заходили в игру больше 7 дней.';
                    return false;
                }

                if ($transferPrice < 10) {
                    $modal['show'] = true;
                    $modal['text'] = 'Ошибка транзакции, попробуйте позже.';
                    return false;
                }

                if ($transferPrice > $usrInf['money_bank']) {
                    $modal['show'] = true;
                    $modal['text'] = 'На банковском счету недостаточно средств.';
                    return false;
                }

                $qb
                    ->createQueryBuilder('users')
                    ->updateSql(['money_bank'], [$usrInf['money_bank'] - $transferPrice])
                    ->where('id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $qb
                    ->createQueryBuilder('cars')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $qb
                    ->createQueryBuilder('houses')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $qb
                    ->createQueryBuilder('condos')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $qb
                    ->createQueryBuilder('business')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                /*$qb
                    ->createQueryBuilder('apartment')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;*/
                $qb
                    ->createQueryBuilder('stocks')
                    ->updateSql(['tax_money'], [0])
                    ->where('user_id = \'' . $usrInf['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $success = true;
                $modal['show'] = true;
                $modal['text'] = 'Вы успешно оплатили все ваши долги';
            }

            if (isset($_POST['r-item-active'])) {

                $item = $qb
                    ->createQueryBuilder('donate_case_user_items')
                    ->selectSql('*, donate_case_user_items.id as idx')
                    ->leftJoin('donate_case_items on donate_case_user_items.item_id = donate_case_items.id')
                    ->where('donate_case_user_items.id = ' . intval($_POST['id']))
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $uInf = $user->getUserInfo(intval($_POST['uid']));

                if ($uInf['is_online'] == 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Ваш персонаж находится в игре';
                    return;
                }

                if ($item['p_type'] == 0) {

                    $moneyConvert = $uInf['money'] + $item['p_win'];

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['money'], [$moneyConvert])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы активировали $' . number_format($item['p_win']) . ' на ваш аккаунт';
                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;
                }
                else if ($item['p_type'] == 1) {

                    $this->qb
                        ->createQueryBuilder('accounts')
                        ->updateSql(['money_donate'], [$userInfo['money_donate'] + $item['p_win']])
                        ->where('id = \'' . $userInfo['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Вы активировали ' . number_format($item['p_win']) . 'dc на ваш аккаунт';
                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;
                }
                else if ($item['p_type'] == 2) {
                    $user->giveVipAccount($uInf['id'], 2, $item['p_win']);
                    $modal['show'] = true;
                    $modal['text'] = 'Вы активировали VIP HARD на ' . $item['p_win'] . ' д.';
                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;
                }
                else if ($item['p_type'] == 3) {
                    $user->giveMask($uInf['id'], $item['mask_id']);
                    $modal['show'] = true;
                    $modal['text'] = 'Ваша маска лежит в инвентаре.';
                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;
                }
                else if ($item['p_type'] == 4) {
                    $vName = $item['p_win'];

                    $vehInfo = $this->qb
                        ->createQueryBuilder('veh_info')
                        ->selectSql()
                        ->where('display_name = \'' . $vName . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    if (empty($uInf)) {
                        $modal['show'] = true;
                        $modal['text'] = 'Неизвестная ошибка, попробуйте еще раз.';
                        return false;
                    }

                    $slot = 0;
                    for ($i = 10; $i > 0; $i--) {
                        if ($uInf['car_id' . $i . ''] == 0)
                            $slot = $i;
                    }

                    if($uInf['is_online'] == 1) {
                        $modal['show'] = true;
                        $modal['text'] = 'Необходимо выйти с сервера.';
                        return false;
                    }

                    if($slot == 0) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет свободных слотов под транспорт.';
                        return false;
                    }

                    $color = random_int(0, 160);
                    $number = $server->genVehicleNumber();

                    $qb
                        ->createQueryBuilder('cars')
                        ->insertSql(
                            ['color1', 'color2', 'number', 'user_id', 'user_name', 'class', 'name', 'price', 'fuel', 'with_delete'],
                            [$color, $color, $number, $uInf['id'], $uInf['name'], $vehInfo['class_name'], $vehInfo['display_name'], intval($vehInfo['price']), $vehInfo['fuel_full'], 1]
                        )
                        ->executeQuery()
                        ->getResult()
                    ;

                    $lastCar = $qb
                        ->createQueryBuilder('cars')
                        ->selectSql()
                        ->orderBy('id DESC')
                        ->limit(1)
                        ->where('number = \'' . $number . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('users')
                        ->updateSql(['car_id' . $slot], [$lastCar['id']])
                        ->where('id = \'' . $uInf['id'] . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Транспорт был активирован на аккаунт.';
                }
                else if ($item['p_type'] == 5) {
                    $user->addWorkExp($uInf['id'], $item['p_win']);
                    $modal['show'] = true;
                    $modal['text'] = 'Вы активировали WORK EXP на ' . $item['p_win'] . 'ед.';
                    $this->qb
                        ->createQueryBuilder('donate_case_user_items')
                        ->deleteSql()
                        ->where('id = \'' . $item['idx'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;
                }
            }

            if (isset($_POST['r-item-sell'])) {

                $item = $qb
                    ->createQueryBuilder('donate_case_user_items')
                    ->selectSql('*, donate_case_user_items.id as idx')
                    ->leftJoin('donate_case_items on donate_case_user_items.item_id = donate_case_items.id')
                    ->where('donate_case_user_items.id = ' . intval($_POST['id']))
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $sum = $item['dc'];
                $moneyDonateConvert = $userInfo['money_donate'] + $sum;
                $do = 'Sell roulette item ' . $item['p_win'] . ' ' . $sum . 'dc. Balance: ' . $moneyDonateConvert;
                $this->qb
                    ->createQueryBuilder('log_donate_trade')
                    ->insertSql(
                        ['user_id', 'action', 'money_was', 'money_remove'],
                        [$userInfo['id'], $do, $userInfo['money_donate'], $sum]
                    )
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money_donate'], [$moneyDonateConvert])
                    ->where('id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('donate_case_user_items')
                    ->deleteSql()
                    ->where('id = \'' . $item['idx'] . '\'')
                    ->executeQuery()
                    ->getResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Предмет был продан за ' . $sum . 'sc.';
            }


            if (isset($_POST['report-send'])) {

                global $qb;
                global $user;
                global $userInfo;

                if ($user->isLogin()) {
                    $links = $server->charsString($_POST['links']);
                    $ids = $server->charsString($_POST['ids']);
                    $text = $server->charsString($_POST['text']);
                    $date = $server->charsString($_POST['date']);

                    $success = $qb
                        ->createQueryBuilder('report_user')
                        ->insertSql(
                            ['user_id', 'target', 'links', 'datetime', 'text', 'timestamp'],
                            [intval($userInfo['id']), $ids, $links, $date, $text, time()]
                        )
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    if($success) {
                        $modal['show'] = true;
                        $modal['text'] = 'Ваша жалоба успешно отправлена, ожидайте рассмотрения.';
                    }
                    else {
                        $modal['show'] = true;
                        $modal['text'] = 'Ошибка отправки сообщения.';
                    }
                }
            }
            if (isset($_POST['report-answer'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);
                    $text = $server->charsString($_POST['text']);

                    $report = $qb->createQueryBuilder('report_user')->selectSql()->where('id = ' . $id)->executeQuery()->getSingleResult();

                    if ($userInfo['admin_level'] > 0 || $report['user_id'] == $userInfo['id']) {

                        $rpName = $userInfo['login'];
                        if ($userInfo['admin_level'] > 0) {
                            $rpName = $user->getAdminName() . ' (' . ($userInfo['admin_level'] == 5 ? 'Главный ' : '') . 'Администратор)';
                        }

                        $success = $qb
                            ->createQueryBuilder('report_user_answer')
                            ->insertSql(
                                ['report_id', 'name_from', 'social_from', 'text', 'owner_id', 'is_read', 'timestamp'],
                                [$id, $rpName, $userInfo['social'], $text, $report['user_id'], $userInfo['admin_level'] == 0 ? 1 : 0, time()]
                            )
                            ->executeQuery()
                            ->getSingleResult()
                        ;

                        if($success) {
                            $modal['show'] = true;
                            $modal['text'] = 'Комментарий был опубликован.';
                        }
                        else {
                            $modal['show'] = true;
                            $modal['text'] = 'Ошибка отправки сообщения.';
                        }
                    }
                    else {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы не можете опубликовать комментарий.';
                    }
                }
            }
            if (isset($_POST['report-status1'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);
                    $text = htmlspecialchars('Здравствуйте. Ваша жалоба на рассмотрении.');

                    if ($userInfo['admin_level'] > 0) {

                        $rpName = $userInfo['login'];
                        if ($userInfo['admin_level'] > 0) {
                            $rpName = $userInfo['login'] . ' (' . ($userInfo['admin_level'] == 5 ? 'Главный ' : '') . 'Администратор)';
                            if ($userInfo['id'] == 1)
                                $rpName = 'WannaCry (Основатель проекта)';
                        }

                        $success = $qb
                            ->createQueryBuilder('report_user_answer')
                            ->insertSql(
                                ['report_id', 'name_from', 'social_from', 'text', 'timestamp'],
                                [$id, $rpName, $userInfo['social'], $text, time()]
                            )
                            ->executeQuery()
                            ->getSingleResult()
                        ;

                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['status', 'admin_id'], [1, $userInfo['id']])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Статус жалобы был изменён.';
                    }
                }
            }
            if (isset($_POST['report-status2'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);

                    if ($userInfo['admin_level'] > 0) {
                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['status'], [2])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Статус жалобы был изменён.';
                    }
                }
            }
            if (isset($_POST['report-status3'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);

                    if ($userInfo['admin_level'] > 0) {
                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['status'], [3])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Статус жалобы был изменён.';
                    }
                }
            }
            if (isset($_POST['report-rating1'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);
                    $report = $qb->createQueryBuilder('report_user')->selectSql()->where('id = ' . $id)->executeQuery()->getSingleResult();

                    if ($userInfo['id'] == $report['user_id']) {
                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['rating'], [1])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Рейтинг жалобы был изменён.';
                    }
                }
            }
            if (isset($_POST['report-rating2'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);
                    $report = $qb->createQueryBuilder('report_user')->selectSql()->where('id = ' . $id)->executeQuery()->getSingleResult();

                    if ($userInfo['id'] == $report['user_id']) {
                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['rating'], [2])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Рейтинг жалобы был изменён.';
                    }
                }
            }
            if (isset($_POST['report-rating3'])) {

                global $qb;
                global $user;
                global $userInfo;
                global $serverName;

                if ($user->isLogin()) {
                    $id = intval($_POST['id']);
                    $report = $qb->createQueryBuilder('report_user')->selectSql()->where('id = ' . $id)->executeQuery()->getSingleResult();

                    if ($userInfo['id'] == $report['user_id']) {
                        $qb
                            ->createQueryBuilder('report_user')
                            ->updateSql(['rating'], [3])
                            ->where('id = ' . $id)
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = 'Рейтинг жалобы был изменён.';
                    }
                }
            }

            if(isset($_POST['send_vote'])) {

                global $userInfo;

                $isVote = $qb
                    ->createQueryBuilder('rp_gov_vote')
                    ->selectSql()
                    ->where('user_id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getResult()
                ;

                $_POST['name'] = $server->charsString($_POST['name']);

                if(empty($isVote)) {
                    $isValideName = $qb
                        ->createQueryBuilder('rp_gov_vote')
                        ->selectSql()
                        ->where('name = \'' . $_POST['name'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    if(!empty($isValideName)) {
                        $result = $qb
                            ->createQueryBuilder('rp_gov_vote')
                            ->insertSql(['user_id', 'name'], [$userInfo['id'], $_POST['name']])
                            ->executeQuery()
                            ->getResult()
                        ;

                        if($result)
                            $message = 'Ваш голос был успешно отправлен';
                        else
                            $message = 'Произошла ошибка, попробуйте еще раз';
                    }
                    else {
                        $message = 'Кандидат не найден';
                    }
                }
                else {
                    $message = 'Вы уже голосовали';
                }

                $modal['show'] = true;
                $modal['text'] = $message;
            }

            if(isset($_POST['trade-trans-to-card'])) {
                global $userInfo;

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для вывода средств';
                }
                else {
                    $money = intval($_POST['money']);

                    if ($money < 200 && $userInfo['admin_level'] < 5) {
                        $modal['show'] = true;
                        $modal['text'] = 'Минимальная сумма вывода 200sc';
                        return;
                    }
                    if ($userInfo['money_donate'] < $money) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет столько StateCoins';
                        return;
                    }

                    $lastOut = $qb->createQueryBuilder('log_coin_out')->selectSql()->orderBy('id DESC')->limit(1)->executeQuery()->getSingleResult();
                    $result = $qb->createQueryBuilder('log_coin_out')->selectSql()->orderBy('id DESC')->limit(1)->executeQuery()->getSingleResult();

                    if (!$result) {
                        $modal['show'] = true;
                        $modal['text'] = 'Проиошла ошибка выплаты, повторите попытку снова';
                        return;
                    }

                    $domain = 'unitpay.money'; // Your working domain: unitpay.money or unitpay.ru
                    $secretKey  = 'FDFC7614E74-7B59D2C1F78-309BCE1397';
                    $unitPay = new UnitPay($domain, $secretKey);

                    $response = $unitPay->api('massPayment', [
                        'sum'     => intval($money / 2),
                        'purse'        => $_POST['number'],
                        'login'         => 'channelappi@gmail.com',
                        'transactionId' => $lastOut['id'] + 1,
                        'paymentType'   => $_POST['type']
                    ], [
                        'projectId' => 357791
                    ]);


                    if (isset($response->result)) {
                        $qb->createQueryBuilder('accounts')->updateSql(['money_donate', 'wallet_' . $server->charsString($_POST['type'])], [$userInfo['money_donate'] - $money, $server->charsString($_POST['number'])])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

                        $qb
                            ->createQueryBuilder('log_coin_out')
                            ->insertSql(
                                ['payout_id', 'user_id', 'type', 'payer', 'sum', 'state', 'timestamp'],
                                [$response->result->payoutId, $userInfo['id'], $server->charsString($_POST['type']), $server->charsString($_POST['number']), $money, 'WAIT', time()]
                            )
                            ->executeQuery()
                            ->getResult()
                        ;

                        $modal['show'] = true;
                        $modal['text'] = $response->result->message;
                    }
                    else {
                        $modal['show'] = true;
                        $modal['text'] = 'Произошла ошибка: ' . $response->error->message;

                        $qb
                            ->createQueryBuilder('log_coin_out')
                            ->insertSql(
                                ['user_id', 'type', 'payer', 'sum', 'state', 'timestamp'],
                                [$userInfo['id'], $server->charsString($_POST['type']), $server->charsString($_POST['number']), $money, 'ERROR_' . $response->error->code, time()]
                            )
                            ->executeQuery()
                            ->getResult()
                        ;
                    }
                }
            }

            if(isset($_POST['trade-trans-status'])) {
                global $userInfo;

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для вывода средств';
                }
                else if ($userInfo['admin_level'] < 5) {
                    $modal['show'] = true;
                    $modal['text'] = 'На данный момент эта функция недоступна';
                }
                else {

                    $lastOut = $qb->createQueryBuilder('log_coin_out')->selectSql()->where('payout_id = ' . intval($_POST['id']))->executeQuery()->getSingleResult();

                    if (empty($lastOut)) {
                        return;
                    }

                    $domain = 'unitpay.money'; // Your working domain: unitpay.money or unitpay.ru
                    $secretKey  = 'FDFC7614E74-7B59D2C1F78-309BCE1397';
                    $unitPay = new UnitPay($domain, $secretKey);

                    $response = $unitPay->api('massPayment', [
                        'sum'     => 1,
                        'purse'        => 1,
                        'login'         => 'channelappi@gmail.com',
                        'transactionId' => $lastOut['id'],
                        'paymentType'   => 'card'
                    ], [
                        'projectId' => 357791
                    ]);

                    if ($userInfo['admin_level'] > 5) {
                        //print_r($response);
                        //die;
                    }

                    if ($lastOut['state'] == 'WAIT') {
                        if ($userInfo['id'] == $lastOut['user_id']) {
                            if (isset($response->result)) {

                                if ($response->result->status == 'not_completed') {

                                }
                                else {
                                    $qb
                                        ->createQueryBuilder('log_coin_out')
                                        ->updateSql(
                                            ['state'],
                                            ['SUCCESS']
                                        )
                                        ->where('id = ' . intval($lastOut['id']))
                                        ->executeQuery()
                                        ->getResult()
                                    ;
                                }

                                $modal['show'] = true;
                                $modal['text'] = 'Текущий статус: ' . $response->result->message;
                            }
                            else {
                                $qb->createQueryBuilder('accounts')->updateSql(['money_donate'], [$userInfo['money_donate'] + $lastOut['sum']])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();
                                $qb
                                    ->createQueryBuilder('log_coin_out')
                                    ->updateSql(
                                        ['state'],
                                        ['ERROR_' . $response->error->code]
                                    )
                                    ->where('id = ' . intval($lastOut['id']))
                                    ->executeQuery()
                                    ->getResult()
                                ;

                                $modal['show'] = true;
                                $modal['text'] = 'Ошибка: ' . $response->error->message . ', средства были возвращены на ваш аккаунт';

                            }
                        }
                    }

                }
            }

            if(isset($_POST['trade-player-to-account'])) {
                global $userInfo;

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                }
                else {
                    $playerId = intval($_POST['player']);
                    $money = intval($_POST['money']);
                    $player = [];
                    $isValid = false;
                    foreach ($user->getPlayers() as $p) {
                        if ($p['id'] == $playerId)
                        {
                            $isValid = true;
                            $player = $p;
                        }
                    }
                    if ($playerId == 0 || !$isValid) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы не выбрали вашего персонажа';
                    }
                    else if ($player['is_online']) {
                        $modal['show'] = true;
                        $modal['text'] = 'Ваш персонаж находится в игре, пожалуйста покиньте сервер для совершения операции';
                    }
                    else if ($player['money'] < $money) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет столько налички на персонаже';
                    }
                    else {
                        $qb->createQueryBuilder('users')->updateSql(['money'], [$player['money'] - $money])->where('id = ' . $player['id'])->executeQuery()->getResult();
                        $qb->createQueryBuilder('accounts')->updateSql(['money'], [$userInfo['money'] + $money])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

                        $modal['show'] = true;
                        $modal['text'] = 'Вы успешно совершили обмен';
                    }
                }
            }

            if(isset($_POST['trade-account-to-player'])) {
                global $userInfo;

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                }
                else {
                    $playerId = intval($_POST['player']);
                    $money = intval($_POST['money']);
                    $player = [];
                    $isValid = false;
                    foreach ($user->getPlayers() as $p) {
                        if ($p['id'] == $playerId)
                        {
                            $isValid = true;
                            $player = $p;
                        }
                    }
                    if ($playerId == 0 || !$isValid) {
                        $modal['show'] = true;
                        $modal['text'] = 'Вы не выбрали вашего персонажа';
                    }
                    else if ($player['is_online']) {
                        $modal['show'] = true;
                        $modal['text'] = 'Ваш персонаж находится в игре, пожалуйста покиньте сервер для совершения операции';
                    }
                    else if ($userInfo['money'] < $money) {
                        $modal['show'] = true;
                        $modal['text'] = 'У вас нет столько валюты на аккаунте';
                    }
                    else {
                        $qb->createQueryBuilder('users')->updateSql(['money'], [$player['money'] + $money])->where('id = ' . $player['id'])->executeQuery()->getResult();
                        $qb->createQueryBuilder('accounts')->updateSql(['money'], [$userInfo['money'] - $money])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

                        $modal['show'] = true;
                        $modal['text'] = 'Вы успешно совершили обмен';
                    }
                }
            }

            if (isset($_POST['trade-money'])) {

                $ac = intval($_POST['sc']);
                $money = intval($_POST['money']);

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                    return;
                }

                if($ac < 1 || $money < 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Нельзя вводить число меньше 1.';
                    return false;
                }

                if($ac > 1147483648 || $money > 1147483648) {
                    $modal['show'] = true;
                    $modal['text'] = 'Нельзя вводить число больше 1147483648.';
                    return false;
                }

                if($money > $ac * 501) {
                    $modal['show'] = true;
                    $modal['text'] = 'Курс SC к $ не должен превышать 1sc к $500';
                    return false;
                }

                if($money > $userInfo['money']) {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас нет столько игровой валюты';
                    return false;
                }

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money'], [$userInfo['money'] - $money])
                    ->where('id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getResult()
                ;
                $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->insertSql(
                        ['user_id', 'user_name', 'user_social', 'ac', 'money', 'timestamp'],
                        [$userInfo['id'], $userInfo['login'], $userInfo['social'], $ac, $money, time()]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
                $success = true;
                $modal['show'] = true;
                $modal['text'] = 'Вы выставили предложение на биржу';
                
            }
            if (isset($_POST['trade-sc'])) {

                $ac = intval($_POST['sc']);
                $money = intval($_POST['money']);

                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                    return;
                }

                if($ac < 1 || $money < 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Нельзя вводить число меньше 1.';
                    return false;
                }
                if($ac > 1147483648 || $money > 1147483648) {
                    $modal['show'] = true;
                    $modal['text'] = 'Нельзя вводить число больше 1147483648.';
                    return false;
                }

                if($ac > $userInfo['money_donate']) {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас нет столько StateCoins';
                    return false;
                }

                if ($money > $ac * 501) {
                    $modal['show'] = true;
                    $modal['text'] = 'Курс SС к $ не должен превышать 1sc к $500';
                    return false;
                }

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money_donate'], [$userInfo['money_donate'] - $ac])
                    ->where('id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->insertSql(
                        ['user_id', 'user_name', 'user_social', 'ac', 'money', 'type', 'timestamp'],
                        [$userInfo['id'], $userInfo['login'], $userInfo['social'], $ac, $money, 1, time()]
                    )
                    ->executeQuery()
                    ->getResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы выставили предложение на биржу';
                
            }
            if (isset($_POST['trade-buy-money'])) {

                $id = intval($_POST['id']);


                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                    return;
                }

                $result = $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->selectSql()
                    ->where('id = \'' . $id . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if (empty($result)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Предложение было удалено';
                    return false;
                }

                if($result['type'] == 1) {
                    $modal['show'] = true;
                    $modal['text'] = 'Неизвестная ошибка';
                    return false;
                }

                if($result['ac'] > $userInfo['money_donate']) {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно StateCoins';
                    return false;
                }

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money', 'money_donate'], [$userInfo['money'] + $result['money'], $userInfo['money_donate'] - $result['ac']])
                    ->where('id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $userInfoFrom = $user->getAccountInfo($result['user_id']);

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money_donate'], [$userInfoFrom['money_donate'] + $result['ac']])
                    ->where('id = \'' . $result['user_id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->deleteSql()
                    ->where('id = \'' . $id . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('log_coin_trade')
                    ->insertSql(
                        ['user_from_id', 'user_to_id', 'coin', 'money', 'type', 'date', 'timestamp'],
                        [$result['user_id'], $userInfo['id'], $result['ac'], $result['money'], $result['type'], gmdate('Y-m-d'), time()]
                    )
                    ->executeQuery()
                    ->getResult()
                ;


                $modal['show'] = true;
                $modal['text'] = 'Вы купили $' . $result['money'] . ' за ' . $result['ac'] . 'sc';
                
            }
            if (isset($_POST['trade-buy-sc'])) {

                $id = intval($_POST['id']);


                if ($userInfo['reg_timestamp'] > time() - (3600 * 24 * 7)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Вам необходимо быть зарегестрированым в нашей системе 7 дней, для того, чтобы вы могли пользоваться торговой площадкой';
                    return;
                }

                $result = $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->selectSql()
                    ->where('id = \'' . $id . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if (empty($result)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Предложение было удалено';
                    return false;
                }

                if($result['type'] == 0) {
                    $modal['show'] = true;
                    $modal['text'] = 'Неизвестная ошибка';
                    return false;
                }

                if($result['money'] > $userInfo['money']) {
                    $modal['show'] = true;
                    $modal['text'] = 'У вас недостаточно игровой валюты';
                    return false;
                }

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money', 'money_donate'], [$userInfo['money'] - $result['money'], $userInfo['money_donate'] + $result['ac']])
                    ->where('id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $userInfoFrom = $user->getAccountInfo($result['user_id']);

                $this->qb
                    ->createQueryBuilder('accounts')
                    ->updateSql(['money'], [$userInfoFrom['money'] + $result['money']])
                    ->where('id = \'' . $result['user_id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->deleteSql()
                    ->where('id = \'' . $id . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $this->qb
                    ->createQueryBuilder('log_coin_trade')
                    ->insertSql(
                        ['user_from_id', 'user_to_id', 'coin', 'money', 'type', 'date', 'timestamp'],
                        [$result['user_id'], $userInfo['id'], $result['ac'], $result['money'], $result['type'], gmdate('Y-m-d'), time()]
                    )
                    ->executeQuery()
                    ->getResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы купили ' . $result['ac'] . 'sc за $' . $result['money'];
                
            }

            if (isset($_POST['trade-remove'])) {

                $id = intval($_POST['id']);

                $result = $this->qb
                    ->createQueryBuilder('trade_coin')
                    ->selectSql()
                    ->where('id = \'' . $id . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if (!empty($result) && $result['user_id'] == $userInfo['id']) {
                    if ($result['type'] == 1) {
                        $this->qb
                            ->createQueryBuilder('accounts')
                            ->updateSql(['money_donate'], [$userInfo['money_donate'] + $result['ac']])
                            ->where('id = \'' . $userInfo['id'] . '\'')
                            ->executeQuery()
                            ->getSingleResult()
                        ;
                    } else {
                        $this->qb
                            ->createQueryBuilder('accounts')
                            ->updateSql(['money'], [$userInfo['money'] + $result['money']])
                            ->where('id = \'' . $userInfo['id'] . '\'')
                            ->executeQuery()
                            ->getSingleResult()
                        ;
                    }
                    $this->qb
                        ->createQueryBuilder('trade_coin')
                        ->deleteSql()
                        ->where('id = \'' . $id . '\'')
                        ->executeQuery()
                        ->getSingleResult()
                    ;

                    $modal['show'] = true;
                    $modal['text'] = 'Предложение было снято с торговой площадки';
                }
            }

            if(isset($_POST['send_vote'])) {

                global $userInfo;

                $isVote = $qb
                    ->createQueryBuilder('rp_gov_vote')
                    ->selectSql()
                    ->where('user_id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getResult()
                ;

                $_POST['name'] = $server->charsString($_POST['name']);

                if(empty($isVote)) {
                    $isValideName = $qb
                        ->createQueryBuilder('rp_gov_vote')
                        ->selectSql()
                        ->where('name = \'' . $_POST['name'] . '\'')
                        ->executeQuery()
                        ->getResult()
                    ;

                    if(!empty($isValideName)) {
                        $result = $qb
                            ->createQueryBuilder('rp_gov_vote')
                            ->insertSql(['user_id', 'name'], [$userInfo['id'], $_POST['name']])
                            ->executeQuery()
                            ->getResult()
                        ;

                        if($result)
                            $message = 'Ваш голос был успешно отправлен';
                        else
                            $message = 'Произошла ошибка, попробуйте еще раз';
                    }
                    else {
                        $message = 'Кандидат не найден';
                    }
                }
                else {
                    $message = 'Вы уже голосовали';
                }

                $modal['show'] = true;
                $modal['text'] = $message;
            }


            if (isset($_POST['send-consignment'])) {

                $_POST['usermain'] = $server->charsString($_POST['usermain']);
                $_POST['desc'] = $server->charsString($_POST['desc']);
                $_POST['text'] = $server->charsString($_POST['text']);
                $_POST['img'] = $server->charsString($_POST['img']);
                $_POST['title'] = $server->charsString($_POST['title']);

                $success = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->insertSql(
                        ['title', 'content', 'img', 'user_owner', 'user_id', 'content_desc'],
                        [$_POST['title'], $_POST['text'], $_POST['img'], $_POST['usermain'], $userInfo['id'], $_POST['desc']]
                    )
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $result = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->selectSql('id')
                    ->limit(1)
                    ->orderBy('id DESC')
                    ->where('user_id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы создали партию!';
                $_SERVER['REQUEST_URI'] = '/network/gov/consignment-info?id=' . reset($result);
            }
            if (isset($_POST['edit-consignment'])) {

                $_POST['usermain'] = $server->charsString($_POST['usermain']);
                $_POST['desc'] = $server->charsString($_POST['desc']);
                $_POST['text'] = $server->charsString($_POST['text']);
                $_POST['img'] = $server->charsString($_POST['img']);
                $_POST['title'] = $server->charsString($_POST['title']);

                $success = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->updateSql(
                        ['title', 'content', 'img', 'user_owner', 'user_id', 'content_desc'],
                        [$_POST['title'], $_POST['text'], $_POST['img'], $_POST['usermain'], $userInfo['id'], $_POST['desc']]
                    )
                    ->where('id = \'' . intval($_POST['pid']) . '\' AND user_id = \'' . $userInfo['id'] . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $result = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->selectSql('id')
                    ->limit(1)
                    ->orderBy('id DESC')
                    ->where('user_id = ' . $userInfo['id'])
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы отредактировали партию!';
                $_SERVER['REQUEST_URI'] = '/network/gov/consignment-info?id=' . reset($result);
            }
            if (isset($_POST['network-save-rules'])) {

                $_POST['text'] = $server->charsString($_POST['text']);

                $success = $qb
                    ->createQueryBuilder('rp_rules')
                    ->updateSql(
                        ['text'],
                        [$_POST['text']]
                    )
                    ->where('id = \'' . intval($_POST['id']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы отредактировали текст!';
            }
            if (isset($_POST['delete-consignment'])) {

                $id = intval($_POST['id']);

                if ($userInfo['admin_level'] > 5) {
                    $success = $qb
                        ->createQueryBuilder('rp_gov_party_list')
                        ->deleteSql()
                        ->where('id = ' . $id)
                        ->executeQuery()
                        ->getSingleResult()
                    ;
                }
                else {
                    $success = $qb
                        ->createQueryBuilder('rp_gov_party_list')
                        ->deleteSql()
                        ->where('user_id = ' . $userInfo['id'])
                        ->andWhere('id = ' . $id)
                        ->executeQuery()
                        ->getSingleResult();
                }

                $modal['show'] = true;
                $modal['text'] = 'Вы удалили партию!';
                $_SERVER['REQUEST_URI'] = '/network/gov/consignment';
            }
            if (isset($_POST['send-network-news']) && ($user->isLeader() || $user->isAdmin(2))) {

                $_POST['text'] = $server->charsString($_POST['text']);
                $_POST['img'] = $server->charsString($_POST['img']);
                $_POST['title'] = $server->charsString($_POST['title']);

                $_POST['text'] = $server->charsString($_POST['text']);

                $success = $qb
                    ->createQueryBuilder('rp_news')
                    ->insertSql(
                        ['title', 'text', 'img', 'author_name', 'author_id', 'fraction', 'date', 'time', 'timestamp'],
                        [$_POST['title'], $_POST['text'], $_POST['img'], $userInfo['login'], $userInfo['id'], intval($_POST['id']), $server->dateNow(),$server->timeNow(), $server->timeStampNow()]
                    )
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Ваша новость опубликована.';
            }
            if (isset($_POST['delete-network-news']) && ($user->isLeader() || $user->isAdmin(2))) {

                $success = $this->qb
                    ->createQueryBuilder('rp_news')
                    ->deleteSql()
                    ->where('id = \'' . intval($_POST['id']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Ваша новость была удалена.';
            }

            if (isset($_POST['edit-user-rp-info'])) {

                $uInf = null;

                foreach ($user->getPlayers() as $player) {
                    if ($player['id'] == $_POST['id'])
                        $uInf = $player;
                }

                if (empty($uInf)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Персонаж не был найден';
                    return;
                }

                $_POST['rp_growth'] = $server->charsString($_POST['rp_growth']);
                $_POST['rp_weight'] = $server->charsString($_POST['rp_weight']);
                $_POST['rp_avatar'] = $server->charsString($_POST['rp_avatar']);
                $_POST['rp_character'] = $server->charsString($_POST['rp_character']);
                $_POST['rp_diseases'] = $server->charsString($_POST['rp_diseases']);
                $_POST['rp_distinctive_features'] = $server->charsString($_POST['rp_distinctive_features']);
                $_POST['rp_qualities'] = $server->charsString($_POST['rp_qualities']);

                $success = $qb
                    ->createQueryBuilder('users')
                    ->updateSql(
                        ['rp_growth', 'rp_weight', 'rp_avatar', 'rp_character', 'rp_diseases', 'rp_distinctive_features', 'rp_qualities'],
                        [$_POST['rp_growth'], $_POST['rp_weight'], $_POST['rp_avatar'], $_POST['rp_character'], $_POST['rp_diseases'], $_POST['rp_distinctive_features'], $_POST['rp_qualities']]
                    )
                    ->where('id = \'' . intval($uInf['id']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы отредактировали профиль!';
            }

            if (isset($_POST['edit-user-rp-bio'])) {

                $uInf = null;

                foreach ($user->getPlayers() as $player) {
                    if ($player['id'] == $_POST['id'])
                        $uInf = $player;
                }

                if (empty($uInf)) {
                    $modal['show'] = true;
                    $modal['text'] = 'Персонаж не был найден';
                    return;
                }

                $_POST['rp_biography'] = $server->charsString($_POST['rp_biography']);

                $success = $qb
                    ->createQueryBuilder('users')
                    ->updateSql(
                        ['rp_biography'],
                        [$_POST['rp_biography']]
                    )
                    ->where('id = \'' . intval($uInf['id']) . '\'')
                    ->executeQuery()
                    ->getSingleResult()
                ;

                $modal['show'] = true;
                $modal['text'] = 'Вы отредактировали биографию!';
            }

            if (!isset($_POST['reset-redirect'])) {
                $_SESSION['modal-show'] = $modal['show'];
                $_SESSION['modal-msg'] = $modal['text'];

                header("Cache-Control: no-store,no-cache,mustrevalidate");
                header("Location: " . $_SERVER['REQUEST_URI']);
                die;
            }
        }
        return true;
    }
}