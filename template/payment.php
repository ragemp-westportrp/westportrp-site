<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

header('Content-Type: application/json');

global $qb;
global $user;
global $serverName;

$secret = '26a9b0c42a3e7c47a3dc06263cee820f';

ini_set('display_errors', '1');

$result = ['error' => ['message' => 'Payment was not completed']];

$answer = $_GET;

if(!isset($answer['params']) || !isset($answer['method']))
    exit(json_encode($result, JSON_UNESCAPED_UNICODE));

$params = $answer['params'];

$acc = explode('|', $params['account']);

if(!isset($acc[1])) {
    $acc[1] = $acc[0];
    $acc[0] = 1;
}

switch ($answer['method']) {
    case 'check':
        $isUser = $user->getAccountInfo($acc[1]);
        if(!empty($isUser)) {

            $content = file_get_contents('https://unitpay.ru/api?method=getPayment&params[paymentId]=' . $params['unitpayId'] . '&params[secretKey]=' . $secret);
            $json = json_decode($content, true);
            if (isset($json['result']['purse'])) {
                if ($isUser['donate_wallet_1'] == '') {
                    $qb->createQueryBuilder('accounts')->updateSql(['donate_wallet_1'], [$json['result']['purse']])->where('id = ' . $isUser['id'])->executeQuery()->getResult();
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else if ($isUser['donate_wallet_1'] == $json['result']['purse']) {
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else if ($isUser['donate_wallet_2'] == '') {
                    $qb->createQueryBuilder('accounts')->updateSql(['donate_wallet_2'], [$json['result']['purse']])->where('id = ' . $isUser['id'])->executeQuery()->getResult();
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else if ($isUser['donate_wallet_2'] == $json['result']['purse']) {
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else if ($isUser['donate_wallet_3'] == '') {
                    $qb->createQueryBuilder('accounts')->updateSql(['donate_wallet_3'], [$json['result']['purse']])->where('id = ' . $isUser['id'])->executeQuery()->getResult();
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else if ($isUser['donate_wallet_3'] == $json['result']['purse']) {
                    $result = ['result' => ['message' => 'Верификация прошла успешна']];
                }
                else
                    $result = ['error' => ['message' => 'Превышен лимит, разрешены платежи только с этих реквизитов: ' . $isUser['donate_wallet_1'] . ', ' . $isUser['donate_wallet_2'] . ', ' . $isUser['donate_wallet_3']]];
            }
            else {
                $result = ['error' => ['message' => 'Проблема, повторите ошибку еще раз или обратитесь к администрации State-99.com']];
            }
        }
        else
            $result = ['error' => ['message' => 'Аккаунт не найден в системе STATE 99 RolePlay']];
        break;
    case 'pay':

        //if(round($params['orderSum']) >= round($params['payerSum'])) {

        $signature = $user->getSignature($answer['method'], $params, $secret);

        if($signature == $params['signature']) {

            $userInfo = $user->getAccountInfo($acc[1]);

            $donateMoney = round($params['orderSum']);

            /*if($donateMoney >= 10000)
                $donateMoney = round($donateMoney * 2.3);
            else if($donateMoney >= 5000)
                $donateMoney = $donateMoney * 2;*/

            $donateMoney = $donateMoney * 1;


            $resultSql = $qb
                ->createQueryBuilder('accounts')
                ->updateSql(['money_donate'], [round($donateMoney + $userInfo['money_donate'])])
                ->where('id = \'' . $acc[1] . '\'')
                ->orWhere('login = \'' . $acc[1] . '\'')
                ->executeQuery()
                ->getResult()
            ;

            $resultSqlLog = $qb
                ->createQueryBuilder('log_donate_payment')
                ->insertSql(
                    ['user_id', 'money_s', 'money_p', 'money_f'],
                    [$acc[1], $donateMoney, round($params['profit']), round($donateMoney + $userInfo['money_donate'])]
                )
                ->executeQuery()
                ->getResult()
            ;

            if ($resultSql)
                $result = ['result' => ['message' => 'Спасибо за пожертвование, ваш кошелёк StateCoin был пополнен']];
            else
                $result = ['error' => ['message' => 'Ошибка платежа, попробуйте еще раз']];
        }
        else
            $result = ['error' => ['message' => 'Hacking attempt']];
        /*}
        else
            $result = ['error' => ['message' => 'Amount does not match. Order: ' . round($params['orderSum']) . ', Payer: ' . round($params['payerSum'])]];*/
        break;
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);