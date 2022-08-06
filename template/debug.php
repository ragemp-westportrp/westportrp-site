<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <meta name="theme-color" content="#ffffff">

    <link rel="shortcut icon" href="https://appi-rp.com/images/logo.png" type="image/x-icon" />
    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="/client/js/materialize.js"></script>
    <script src="/client/js/main.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/client/css/material.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/extended.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/client/css/material-font.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
    <body>

        <?php


        global $serverList;
        global $parser;
        global $qb;

        $ar[1] = 1;
        $ar[2] = 2;
        echo json_encode($ar);

        die;
        //$parser->getFiveM();
        //$serverList->updateServerOnlineList();

        $list = $qb->createQueryBuilder('accounts2')->selectSql()->executeQuery()->getResult();


        foreach ($list as $item) {
            $qb
                ->createQueryBuilder('accounts')
                ->updateSql(['password'], [$item['password']])
                ->where('id = \'' . $item['id'] . '\'')
                ->executeQuery()
                ->getResult()
            ;
        }

        echo 'SUCCESS';

        die;

        $langTest = [
            'a' => 'аурек',
            'b' => 'беш',
            'c' => 'креш',
            'd' => 'дорн',
            'e' => 'эск',
            'f' => 'форн',
            'g' => 'грек',
            'h' => 'хреф',
            'i' => 'иск',
            'j' => 'джент',
            'k' => 'крил',
            'l' => 'лет',
            'm' => 'мерн',
            'n' => 'нерн',
            'o' => 'оск',
            'p' => 'пет',
            'q' => 'кек',
            'r' => 'реш',
            's' => 'сент',
            't' => 'трил',
            'u' => 'уск',
            'v' => 'вев',
            'w' => 'уэск',
            'x' => 'ксеш',
            'y' => 'йирт',
            'z' => 'зерк',
            ' ' => ' ',
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
        ];

        $test = 'dcp';
        $resultTest = '';

        for ($i = 0; $i < strlen($test); $i++) {
            $resultTest .= $langTest[strtolower($test[$i])];
        }

        echo $resultTest;

        /*
        a - аурек
        b - беш
        c - креш
        d - дорн
        e - эск
        f - форн
        g - грек
        h - хреф
        i - иск
        j - джент
        k - крил
        l - лет
        m - мерн
        n - нерн
        o - оск
        p - пет
        q - кек
        r - реш
        s - сент
        t - трил
        u - уск
        v - вев
        w - уэск
        x - ксеш
        y - йирт
        z - зерк


        */


        die;

        use Server\API;

        global $qb;
        global $parser;

        $sampQuery = new API\SampQuery("46.105.240.185", 7772);

        $test = $sampQuery->getInfo()['hostname'];


        var_dump(mb_convert_encoding($test, 'UTF-8'));

        /*$sIPAddr = "46.105.240.185";                                                         // IP address of the server
        $iPort = 7772;                                                                  // Server port.
        $sPacket = "";                                                                  // Blank string for packet.

        $aIPAddr = explode('.', $sIPAddr);                                              // Exploding the IP addr.

        $sPacket .= "SAMP";                                                             // Telling the server it is a SA-MP packet.

        $sPacket .= chr($aIPAddr[0]);                                                   //
        $sPacket .= chr($aIPAddr[1]);                                                   //
        $sPacket .= chr($aIPAddr[2]);                                                   //
        $sPacket .= chr($aIPAddr[3]);                                                   // Sending off the server IP,

        $sPacket .= chr($iPort & 0xFF);                                                 //
        $sPacket .= chr($iPort >> 8 & 0xFF);                                            // Sending off the server port.

        $sPacket .= 'i';                                                                // The opcode that you want to send.
        // You can now send this to the server.


        $rSocket = fsockopen('udp://'.$sIPAddr, $iPort, $iError, $sError, 2);           // Create an active socket.
        fwrite($rSocket, $sPacket);                                                     // Send the packet to the server.

        echo fread($rSocket, 2048);							// Get the output from the server

        fclose($rSocket);*/

        //$parser->updateServerOnlineList();


        //$html = file_get_contents('https://ru.wikipedia.org/wiki/ISO_3166-1');

        /*$html = file_get_contents('https://servers.fivem.net/');

        $dom = new DOMDocument();

        // set error level
        $internalErrors = libxml_use_internal_errors(true);

        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('tr');

        foreach ($tables as $row) {

            //$cols[0]->nodeValue - name
            //$cols[3]->nodeValue - clients
            //$cols[4]->nodeValue - ip


            $cols = $row->getElementsByTagName('td');

            $players = explode(" ", $cols[3]->nodeValue);

            $maxPlayers = trim($players[1], "()");
            $players = $players[0];

            echo '<br>';
        }*/

        /*$dom = new DOMDocument();
        $dom->loadHTML($html);

        $dom->preserveWhiteSpace = false;
        $tables = $dom->getElementsByTagName('tr');

        foreach ($tables as $row) {
            try {
                $cols = $row->getElementsByTagName('td');
                if (!isset($cols[1]->textContent)) continue;
                $qb->createQueryBuilder('code_list')->insertSql(['code2', 'code3'], [$cols[1]->textContent, $cols[2]->textContent])->executeQuery();

            } catch (Exception $exception) {

            }
        }*/

        /*

        mp types

        1 - my
        2 - gtmp
        3 - network
        4 - rage
        5 - fivem
        6 - orange

        gameType

        0 - unc
        1 - rp
        2 - dm

        */



        ?>
    </body>
</html>