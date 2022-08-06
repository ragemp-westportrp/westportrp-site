<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}


global $qb;
global $user;
global $userInfo;
global $serverName;
global $server;
global $bId;
global $tmp;



$business = $qb->createQueryBuilder('business')->selectSql()->where('id = ' . $this->bid)->executeQuery()->getSingleResult();
$leaderName = empty($business['user_name']) ? 'Отсутствует' : $business['user_name'];
$bId =  $business['id'];
?>

<img src="https://i.imgur.com/3Qxdoyf.png" style="position: absolute; z-index: -1; width: 100%; height: 400px; object-fit: cover;">

<div class="container">
    <div class="section">
        <div class="row" style="margin-top: 20px">
            <div class="col s12 m6 l7 white-text">
                <img style="width: 120px;" src="https://gtalogo.com/img/6765.png">
                <h3><?php echo $business['name'] ?></h3>
                <div><label style="color: rgba(255,2552,255,0.7)">Владелец:</label> <?php echo $leaderName; ?></div>
            </div>
            <div class="col s12 m6 l5">
                <div class="center" style="margin-top: 80px">
                    <i style="font-size: 4rem" class="material-icons white-text">account_balance</i>
                    <div class="center" style="color: rgba(255,2552,255,0.7)">Стоимость</div>
                    <h3 class="center white-text">$<?php echo number_format($business['price']); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container" style="padding: 5px 0;">
    <div class="section">
        <div class="row">

        </div>
    </div>
</div>
<?php
foreach ($user->getPlayers() as $player) {
    if ($player['business_id'] == $business['id'])
        $tmp->showBlockPage('network/all/log-business');
}
if ($user->isAdmin(2))
    $tmp->showBlockPage('network/all/log-business');
?>
