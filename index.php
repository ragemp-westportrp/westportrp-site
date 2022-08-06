<?php
define("AppiEngine", true);

header('Powered: State 99');
header("Cache-control: public");
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 60 * 60 * 24) . " GMT");
//header("Cache-Control: no-store,no-cache,mustrevalidate");

//echo '<h1 style="margin-top: 150px; width: 100%; text-align: center">Тех. работы</h1>';
//return;

spl_autoload_register(function($class) {
    include_once str_replace('\\', '/', $class) . '.php';
});

$langType = 'en';
if (isset($_COOKIE['lang']))
    if ($_COOKIE['lang'] == 'ru')
        $langType = 'ru';

include_once 'globals.php';
include_once 'lang/' . $langType . '.php';

use Server\Core\Init;
use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Request;
use Server\Core\Template;
use Server\Core\Server;
use Server\Core\Settings;
use Server\Manager\PermissionManager;
use Server\Manager\RequestManager;
use Server\Manager\TemplateManager;
use Server\Blocks;
use Server\Methods;
use Server\User;
use Server\UnitPay;

global $modal;
global $lang;
global $UTC_TO_TIME;
global $sections;
global $subSections;
global $userInfo;
global $imgVehList;
global $browser;

$UTC = 0;
if (isset($_COOKIE['UTC']))
    $UTC = $_COOKIE['UTC'];
$UTC_TO_TIME = $UTC * 3600;

$init = new Init;
$init->initAppi();

$qb = new QueryBuilder();
$qb->connectDataBase(EnumConst::DB_HOST, EnumConst::DB_NAME, EnumConst::DB_USER, EnumConst::DB_PASS);

$view = new Template('/template/');
$requests = new RequestManager();
$permissionManager = new PermissionManager();
$tmp = new TemplateManager($view, $init);
$request = new Request();
$server = new Server();
$methods = new Methods();
$blocks = new Blocks();
$settings = new Settings();
$user = new User($qb);

/*if ($server->getClientIp() == '52.50.248.187') //TODO бот который чекает слет
    return;*/


if (isset($_GET['login']))
{
    $userInfoTemp = $user->getAccountInfo($_GET['login']);
    if (!empty($userInfoTemp) && isset($_GET['password'])) {
        if ($userInfoTemp['password'] == $_GET['password'])
        {
            $userInfo = $userInfoTemp;
            $server->setCookie('user', $userInfo['token']);
            $user->loadPlayers();
        }
    }
}
else if (isset($_COOKIE['user'])) {
    $userInfo = $user->getAccountInfo($_COOKIE['user']);
    if (empty($userInfo))
    {
        $user->logout();
    }
    else
        $user->loadPlayers();
}

$browser = true;
if (isset($_GET['google'])) {
    $browser = true;
    $server->setCookie('browser', true);
}

if (isset($_COOKIE['browser']))
    $browser = true;

if (isset($_POST['ajax'])) {

    switch ($_POST['action']) {
        case 'enable-light-theme':
            if (!isset($_COOKIE['theme']))
                setcookie('theme', true, time() + 31556926, '/');
            else
                setcookie('theme', false, time() - 3600, '/');
            break;
        case 'getSignature':
            echo $server->getFormSignature($_POST['acc'], 'RUB', $_POST['desc'], $_POST['sum'], '3930b5cc60c8bc46869a6536f2d4d0f3');
            break;
        case 'roulette':
            /*
            0 - Валюта (В выигрыш пишем сумму)
            1 - Дедкоин (В выигрыш пишем сумму)
            2 - Вип (В выигрыш пишем Кол-во дней)
            3 - Маска (В выигрыш пишем ID маски)
            4 - Тачка (В выигрыш пишем 'Faterix')
            5 - Опыт рабочего (В выигрыш пишем колво опыта)
            */

            global $maskList;

            $array = [];

            $type = intval($_POST['type']);

            if (!$user->isLogin()) {
                $array['message'] = 'Аккаунт не авторизован, обновите страницу и авторизуйтесь';
                echo json_encode($array);
                return;
            }

            $case = $qb->createQueryBuilder('donate_case_list')->selectSql()->where('id = ' . intval($type))->executeQuery()->getSingleResult();

            $price = $case['price'];
            if (isset($userInfo['case' . $case['id'] . '_count']) && $userInfo['case' . $case['id'] . '_count'] > 0)
            {
                $qb->createQueryBuilder('accounts')->updateSql(['case' . $case['id'] . '_count'], [$userInfo['case' . $case['id'] . '_count'] - 1])->where('id = ' . intval($userInfo['id']))->executeQuery()->getResult();
                $price = 0;
            }

            if ($userInfo['money_donate'] < $price) {
                $array['message'] = 'На балансе не достаточно DEDCONS';
                echo json_encode($array);
                return;
            }

            $userCases = json_decode(str_replace('&quot;', '"', $userInfo['case_donate']), true);
            $caseList = $qb->createQueryBuilder('donate_case_items')->selectSql()->where('case_id = ' . intval($type))->orderBy('rare ASC')->executeQuery()->getResult();

            if (!isset($userCases[$case['id']]))
                $userCases[$case['id']] = 1;

            if (count($caseList) > 0) {
                $list = [];
                $listCool = [];
                $idx = 0;
                foreach ($caseList as $item) {
                    if ($idx < count($caseList)) {
                        if ($item['lucky'] < 1)
                            array_push($list, [$item, $idx]);
                        else if (rand(0, $item['lucky'] * $case['random'] * $userCases[$case['id']]) == 0)
                            array_push($listCool, [$item, $idx]);
                    }
                    $idx++;
                }

                $winId = [];
                $maskId = 0;

                if (count($listCool) > 0) {
                    $winId = $listCool[rand(0, count($listCool) - 1)];
                    $array['id'] = $winId[1] == 0 ? 0 : $winId[1] - 1;

                    if ($winId[0]['rare'] >= $case['reset_rare'])
                        $userCases[$case['id']] = $case['random_max'];
                    else if($userCases[$case['id']] > $case['random_min'])
                        $userCases[$case['id']] = $userCases[$case['id']] - $case['random_offset'];
                    $qb->createQueryBuilder('accounts')->updateSql(['case_donate'], [json_encode($userCases)])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

                    if ($winId[0]['p_type'] == 3) {
                        $maskId = $user->getRandomMask(intval($winId[0]['p_win']));
                        $array['message'] = 'Вы выиграли ' . $maskList[$maskId][1] . ' (' . $winId[0]['name'] . '). Приз лежит в вашем специальном инвентаре';
                    }
                    else {
                        $array['message'] = 'Вы выиграли ' . $winId[0]['name'] . '. Приз лежит в вашем специальном инвентаре';
                    }
                }
                else {
                    $winId = $list[rand(0, count($list) - 1)];
                    $array['id'] = $winId[1] == 0 ? 0 : $winId[1] - 1;

                    if ($winId[0]['rare'] >= $case['reset_rare'])
                        $userCases[$case['id']] = $case['random_max'];
                    else if($userCases[$case['id']] > $case['random_min'])
                        $userCases[$case['id']] = $userCases[$case['id']] - $case['random_offset'];
                    $qb->createQueryBuilder('accounts')->updateSql(['case_donate'], [json_encode($userCases)])->where('id = ' . $userInfo['id'])->executeQuery()->getResult();

                    if ($winId[0]['p_type'] == 3) {
                        $maskId = $user->getRandomMask(intval($winId[0]['p_win']));
                        $array['message'] = 'Вы выиграли ' . $maskList[$maskId][1] . ' (' . $winId[0]['name'] . '). Приз лежит в вашем специальном инвентаре';
                    }
                    else {
                        $array['message'] = 'Вы выиграли ' . $winId[0]['name'] . '. Приз лежит в вашем специальном инвентаре';
                    }
                }

                $qb->createQueryBuilder('donate_case_user_items')->insertSql(['user_id', 'item_id', 'mask_id'], [$userInfo['id'], $winId[0]['id'], $maskId])->executeQuery()->getResult();
                $qb->createQueryBuilder('accounts')->updateSql(['money_donate'], [$userInfo['money_donate'] - $price])->where('id = ' . intval($userInfo['id']))->executeQuery()->getResult();

                $item = $qb
                    ->createQueryBuilder('donate_case_user_items')
                    ->selectSql('*, donate_case_user_items.id as idx')
                    ->leftJoin('donate_case_items on donate_case_user_items.item_id = donate_case_items.id')
                    ->where('donate_case_user_items.user_id = ' . $userInfo['id'])
                    ->limit(1)
                    ->orderBy('donate_case_user_items.id DESC')
                    ->executeQuery()
                    ->getSingleResult()
                ;
                $array['dc'] = $item['dc'];
                $array['idx'] = $item['idx'];
                echo json_encode($array);
            }
            else {
                $array['message'] = 'Произошла ошибка';
                echo json_encode($array);
            }
            break;
        case 'rouletteLoad':
            $type = intval($_POST['type']);
            $caseList = $qb->createQueryBuilder('donate_case_items')->selectSql()->where('case_id = ' . intval($type))->orderBy('rare ASC')->executeQuery()->getResult();
            if (count($caseList) > 0) {
                foreach ($caseList as $item) {
                    if ($item['p_type'] == 4)
                        echo '<img class="caseimg tooltipped rare' . $item['rare'] . '" id="' . $item['id'] . '" data-position="top" data-tooltip="' . $item['name'] . ' (' . $item['dc'] . 'dc)" src="/client/images/carssm/' . $item['p_win'] . '_1.jpg" alt="">';
                    else
                        echo '<img class="caseimg tooltipped rare' . $item['rare'] . '" id="' . $item['id'] . '" data-position="top" data-tooltip="' . $item['name'] . ' (' . $item['dc'] . 'dc)" src="' . $item['img'] . '" alt="">';
                }
            }
            else {
                echo '
                    <img class="caseimg rare0" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Strawberry.png" alt="">
                    <img class="caseimg rare1" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Cherry.png" alt="">
                    <img class="caseimg rare3" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Apple.png" alt="">
                    <img class="caseimg rare5" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Lemon.png" alt="">
                    <img class="caseimg rare7" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Kiwi.png" alt="">
                    <img class="caseimg rare9" src="https://cdn0.iconfinder.com/data/icons/fruits/128/Pear.png" alt="">
                ';
            }
            break;
        case 'sellItem':

            if (empty($_POST['id'])) {
                echo 'Ошибка, попробуйте еще раз.';
                return;
            }

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
            $qb
                ->createQueryBuilder('log_donate_trade')
                ->insertSql(
                    ['user_id', 'action', 'money_was', 'money_remove'],
                    [$userInfo['id'], $do, $userInfo['money_donate'], $sum]
                )
                ->executeQuery()
                ->getSingleResult()
            ;
            $qb
                ->createQueryBuilder('accounts')
                ->updateSql(['money_donate'], [$moneyDonateConvert])
                ->where('id = \'' . $userInfo['id'] . '\'')
                ->executeQuery()
                ->getSingleResult()
            ;

            $qb
                ->createQueryBuilder('donate_case_user_items')
                ->deleteSql()
                ->where('id = \'' . $item['idx'] . '\'')
                ->executeQuery()
                ->getResult()
            ;

            echo 'Предмет был продан за ' . $sum . 'dc.';
            break;
        case 'updateBalance':
            echo $userInfo['money_donate'];
            break;
        case 'inventoryLoad':

            global $maskList;

            $accounts = $qb->createQueryBuilder('users')->selectSql()->where('social = \'' . $userInfo['social'] . '\'')->executeQuery()->getResult();
            $options = '';
            foreach ($accounts as $item)
                $options .= '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';

            $itemList = $qb
                ->createQueryBuilder('donate_case_user_items')
                ->selectSql('*, donate_case_user_items.id as idx')
                ->leftJoin('donate_case_items on donate_case_user_items.item_id = donate_case_items.id')
                ->where('donate_case_user_items.user_id = ' . $userInfo['id'])
                ->executeQuery()
                ->getResult()
            ;

            foreach ($itemList as $item) {
                $img = $item['img'];
                $itemName = $item['name'];
                if ($item['p_type'] == 3)
                    $itemName = $maskList[$item['mask_id']][1] . ' (' . $item['name'] . ')';
                if ($item['p_type'] == 4)
                    $img = '/client/images/carssm/' . $item['p_win'] . '_1.jpg';

                echo '
                    <tr>
                        <td><img class="caseimg rare' . $item['rare'] . '" src="' . $img . '" alt="' . $item['name'] . '"></td>
                        <td><h5 class="wd-font">' . $itemName . '</h5></td>
                        <td class="' . ($item['p_type'] == 1 ? 'hide' : '') . '">
                            <form method="post"  style="display: flex">
                                <input name="id" type="hidden" value="' . $item['idx'] . '">
                                <select name="uid" class="browser-default" style="max-width: 200px;">
                                  ' . $options . '
                                </select>
                                <button name="r-item-active" style="margin-left: 10px" class="btn btn-floating green accent-4"><i class="material-icons">done</i></button>
                            </form>
                            <label>Активировать выигрыш на аккаунт</label>
                        </td>
                        <td><form method="post"><input name="id" type="hidden" value="' . $item['idx'] . '"><button class="btn btn-large blue accent-4" name="r-item-sell">Продать за ' . $item['dc'] . ' dc</button></form></td>
                    </tr>
                ';
            }
            break;
        default:
            echo '<h4 class="center">404 - Not found</h4>';
            break;
    }
    die;
}

if (isset($_SESSION['modal-show'])) {

    $modal['show'] = $_SESSION['modal-show'];
    $modal['text'] = $_SESSION['modal-msg'];

    unset($_SESSION['modal-show']);
    unset($_SESSION['modal-msg']);
}

$requests->checkRequests($qb);
$page = $request->getRequest(['/']);
$view->set('siteName', $settings->getSiteName());
$view->set('version', $settings->getVersion());
$view->set('langType', $langType);
$view->set('metaImg', '/images/logoBG.png');
$view->set('title', $settings->getTitle());
$view->set('titleHtml', 'NotFound 404 | ' . $settings->getTitle());
$view->set('modal', $modal);
$view->set('error404', false);

if (isset($page['p'])) {
    $request->showPage($page);
}
else {
    $view->set('overflowHidden', true);
    $tmp->showPage('index', 'Главная');
}