<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Server
 */
class Server
{

    public $timeStampNow;
    public $timeStampUTCNow;
    public $dateTimeNow;
    public $dateNow;
    public $timeNow;

    protected $config;

    /**
     * Server constructor.
     */
    function __construct()
    {
        $this->timeStampNow = time();
        $this->timeStampUTCNow = $this->timeStampNow + (3600 * $this->getClientUTC());
        $this->dateTimeNow = gmdate('Y-m-d H:i:s', $this->timeStampNow);
        $this->dateNow = gmdate('Y-m-d', $this->timeStampNow);
        $this->timeNow = gmdate('H:i:s', $this->timeStampNow);
        //$this->requestLog();

        $this->config = new Config;
        $this->config = $this->config->getAppiAllConfig()->getObjectResult();
    }

    /**
     * @param $url
     * @return string
     */
    public function getUrlPath($url) {
        $url = parse_url($url);
        return str_replace('/', '', $url['path']);
    }

    /**
     * Mehtod. Set UTC user;
     * @param $utc
     * @return bool
     */
    public function setClientUTC($utc) {
        setcookie("UTC", $utc, 0x6FFFFFFF, "/");
        return true;
    }

    /**
     * Mehtod. Set UTC user;
     * @param $name
     * @param $value
     */
    public function setCookie($name, $value) {
        setcookie($name, $value, 0x6FFFFFFF, "/");
    }

    /**
     * Mehtod. Get UTC user;
     */
    public function getClientUTC() {
        if(isset($_COOKIE['UTC']))
            return $_COOKIE['UTC'];
        return 0;
    }

    /**
     * Mehtod. Get time stamp;
     */
    public function timeStampNow() {
        return $this->timeStampNow;
    }

    /**
     * Mehtod. Get time stamp;
     */
    public function timeStampUTCNow() {
        return $this->timeStampUTCNow;
    }

    /**
     * Mehtod. Get date time;
     */
    public function dateTimeNow() {
        return $this->dateTimeNow;
    }

    /**
     * Mehtod. Get date;
     */
    public function dateNow() {
        return $this->dateNow;
    }

    /**
     * Mehtod. Get time;
     */
    public function timeNow() {
        return $this->timeNow;
    }

    /**
     * Mehtod. Get version framework;
     */
    public function getVersionFW() {
        return EnumConst::VERSION;
    }

    /**
     * Mehtod. Get console log;
     */
    public function consoleLog($text) {
        echo '<script type="text/javascript">console.log("' . $text . '")</script>';
    }

    /**
     * Mehtod. Replace quotes;
     */
    public function replaceQuotes($text) {
        $lit = ["'"];
        $sp = ['"'];
        return str_replace($lit, $sp, $text);
    }

    /**
     * Mehtod. Get referrer;
     */
    public  function getReferrer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
        return false;
    }

    /**
     * Mehtod. Get client ip;
     */
    public function getClientIp() {
        if(isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        else {
            return "localhost";
        }
    }

    /**
     * Mehtod. Get Server URL;
     */
    public function getServerURL() {
        $url = "http://";
        $url .= $_SERVER["SERVER_NAME"]; // $_SERVER["HTTP_HOST"] is equivalent
        if ($_SERVER["SERVER_PORT"] != "80") $url .= ":".$_SERVER["SERVER_PORT"];
        return $url;
    }

    /**
     * Mehtod. Get full URL;
     */
    public function getCompleteURL() {
        return $this->getServerURL() . $_SERVER["REQUEST_URI"];
    }

    /**
     * Mehtod. HtmlSpecialChars, StrIpSlashes, AddcSlashes;
     */
    public function charsString($string, $isHtmlSpecialChars = true) {
        if($isHtmlSpecialChars)
            return addcslashes(htmlspecialchars(stripslashes($string)), '\'"\\');
        else
            return addcslashes(stripslashes($string), '\'"\\');
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllSymbolsAndNumbers($string) {
        return preg_replace('/[^a-zA-Zа-яА-Я]/uix','',$string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllSymbols($string) {
        return preg_replace('![^\w]*!uix','',$string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllNumbers($string) {
        return preg_replace('/[\d]/', '', $string);
    }

    /**
     * @param $timeStamp
     * @return string
     */
    public function timeStampToDate($timeStamp) {
        return gmdate('m', $timeStamp) . '/' . gmdate('d', $timeStamp) . '/' . gmdate('Y', $timeStamp);
    }

    /**
     * @param $timeStamp
     * @return string
     */
    public function timeStampToTime($timeStamp) {
        return gmdate('H', $timeStamp) . ':' . gmdate('i', $timeStamp);
    }

    /**
     * Mehtod. Get server info;
     */
    public function serverInfo(){
        if (!@phpinfo()) echo 'No Php Info...';
        echo "<br><br>";
        $a=ini_get_all();
        $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $output.="<tr><th colspan=2>ini_get_all()</td></tr>";

        while(list($key, $value)=each($a)) {
            list($k, $v)= each($a[$key]);
            $output.="<tr><td align=right>$key</td><td>$v</td></tr>";
        }

        $output.="</table>";
        echo $output;
        echo "<br><br>";
        $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $output.="<tr><th colspan=2>\$_SERVER</td></tr>";

        foreach ($_SERVER as $k=>$v) {
            $output.="<tr><td align=right>$k</td><td>$v</td></tr>";
        }

        $output.="</table>";
        echo $output;
        echo "<br><br>";
        echo "<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $safe_mode=trim(ini_get("safe_mode"));

        if ((strlen($safe_mode)==0)||($safe_mode==0)) $safe_mode=false;
        else $safe_mode=true;

        $is_windows_server = (substr(PHP_OS, 0, 3) === 'WIN');
        echo "<tr><td colspan=2>".php_uname();
        echo "<tr><td>safe_mode<td>".($safe_mode?"on":"off");

        if ($is_windows_server) echo "<tr><td>sisop<td>Windows<br>";
        else echo "<tr><td>sisop<td>Linux<br>";

        echo "</table><br><br><table border=1 cellspacing=0 cellpadding=4 align=center>";
        $display_errors=ini_get("display_errors");
        $ignore_user_abort = ignore_user_abort();
        $max_execution_time = ini_get("max_execution_time");
        $upload_max_filesize = ini_get("upload_max_filesize");
        $memory_limit=ini_get("memory_limit");
        $output_buffering=ini_get("output_buffering");
        $default_socket_timeout=ini_get("default_socket_timeout");
        $allow_url_fopen = ini_get("allow_url_fopen");
        $magic_quotes_gpc = ini_get("magic_quotes_gpc");
        ignore_user_abort(true);
        ini_set("display_errors",0);
        ini_set("max_execution_time",0);
        ini_set("upload_max_filesize","10M");
        ini_set("memory_limit","20M");
        ini_set("output_buffering",0);
        ini_set("default_socket_timeout",30);
        ini_set("allow_url_fopen",1);
        ini_set("magic_quotes_gpc",0);
        echo "<tr><td> <td>Get<td>Set<td>Get";
        echo "<tr><td>display_errors<td>$display_errors<td>0<td>".ini_get("display_errors");
        echo "<tr><td>ignore_user_abort<td>".($ignore_user_abort?"on":"off")."<td>on<td>".(ignore_user_abort()?"on":"off");
        echo "<tr><td>max_execution_time<td>$max_execution_time<td>0<td>".ini_get("max_execution_time");
        echo "<tr><td>upload_max_filesize<td>$upload_max_filesize<td>10M<td>".ini_get("upload_max_filesize");
        echo "<tr><td>memory_limit<td>$memory_limit<td>20M<td>".ini_get("memory_limit");
        echo "<tr><td>output_buffering<td>$output_buffering<td>0<td>".ini_get("output_buffering");
        echo "<tr><td>default_socket_timeout<td>$default_socket_timeout<td>30<td>".ini_get("default_socket_timeout");
        echo "<tr><td>allow_url_fopen<td>$allow_url_fopen<td>1<td>".ini_get("allow_url_fopen");
        echo "<tr><td>magic_quotes_gpc<td>$magic_quotes_gpc<td>0<td>".ini_get("magic_quotes_gpc");
        echo "</table><br><br>";
        echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
	        window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
	        window.focus();
	    //-->
	    </script>";
        echo "</body>\n</html>";
    }

    public function requestLog() {
        if(!empty($_REQUEST)) {
            $this->log("[".$this->getCompleteURL()."] ".json_encode($_REQUEST)."\n", "logs/request.log");
        }
    }

    public function log($msg, $dir) {
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }
        //if ($this->config->isLog) {
        //error_log("[".$this->dateTimeNow."] [".$this->getClientIp()."] ".$msg . "\n", 3, $dir);
        //}*/
    }

    public function error($msg, $errorCode = null) {
        $this->log($msg.". Error Code:".$errorCode.";\n", "logs/errors.log");
        return sprintf('Error: '.$msg, $errorCode);
    }

    public function phoneFormat($data) {
        if(preg_match( '/(\d{3})(\d{3})(\d{4})$/', $data,  $matches ) )
        {
            $result = $matches[1] . ' ' .$matches[2] . '-' . $matches[3];
            return $result;
        }
        return $data;
    }

    public function bankFormat($data) {
        if(preg_match( '/(\d{4})(\d{4})(\d{4})(\d{4})$/', $data,  $matches ) )
        {
            $result = $matches[1] . ' ' .$matches[2] . ' ' . $matches[3] . ' ' . $matches[4];
            return $result;
        }
        return $data;
    }

    public function genVehicleNumber() {
        $text = '';
        $possible = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $possible = str_split($possible);

        for ($i = 0; $i < 8; $i++) {
            try { $text .= $possible[random_int(0, count($possible) - 1)]; } catch (\Exception $e) {};
        }
        return $text;
    }

    public function getRareName($proc) {
        if ($proc === 0)
            return 'Обычная';
        if ($proc <= 10)
            return 'Легендарная';
        if ($proc <= 20)
            return 'Засекреченная';
        if ($proc <= 30)
            return 'Мистическая';
        if ($proc <= 40)
            return 'Элитная';
        if ($proc <= 50)
            return 'Невероятно редкая';
        if ($proc <= 60)
            return 'Очень редкая';
        if ($proc <= 70)
            return 'Редкая';
        if ($proc <= 80)
            return 'Необычная';
        if ($proc <= 90)
            return 'Ширпотреб';
        return 'Обычная';
    }

    public function getRandomMask($num) {
        global $maskList;
        $maskTemp = [];
        $idx = 0;
        foreach ($maskList as $mask) {
            if ($mask[14] > $num)
                array_push($maskTemp, $idx);
            $idx++;
        }

        return $maskTemp[rand(0, count($maskTemp) - 1)];
    }

    public function getDaysFromTime($timestamp) {
        return (time() - $timestamp) / (60*60*24);
    }

    public function getFormSignature($account, $currency, $desc, $sum, $secretKey) {
        $hashStr = $account.'{up}'.$currency.'{up}'.$desc.'{up}'.$sum.'{up}'.$secretKey;
        return hash('sha256', $hashStr);
    }

    public function generateToken() {
        return md5($this->timeStampNow . rand(0, PHP_INT_MAX));
    }

    public function escapeWin($path) {
        $path = strtoupper ($path);
        return strtr($path, array("\U0430"=>"а", "\U0431"=>"б", "\U0432"=>"в",
            "\U0433"=>"г", "\U0434"=>"д", "\U0435"=>"е", "\U0451"=>"ё", "\U0436"=>"ж", "\U0437"=>"з", "\U0438"=>"и",
            "\U0439"=>"й", "\U043A"=>"к", "\U043B"=>"л", "\U043C"=>"м", "\U043D"=>"н", "\U043E"=>"о", "\U043F"=>"п",
            "\U0440"=>"р", "\U0441"=>"с", "\U0442"=>"т", "\U0443"=>"у", "\U0444"=>"ф", "\U0445"=>"х", "\U0446"=>"ц",
            "\U0447"=>"ч", "\U0448"=>"ш", "\U0449"=>"щ", "\U044A"=>"ъ", "\U044B"=>"ы", "\U044C"=>"ь", "\U044D"=>"э",
            "\U044E"=>"ю", "\U044F"=>"я", "\U0410"=>"А", "\U0411"=>"Б", "\U0412"=>"В", "\U0413"=>"Г", "\U0414"=>"Д",
            "\U0415"=>"Е", "\U0401"=>"Ё", "\U0416"=>"Ж", "\U0417"=>"З", "\U0418"=>"И", "\U0419"=>"Й", "\U041A"=>"К",
            "\U041B"=>"Л", "\U041C"=>"М", "\U041D"=>"Н", "\U041E"=>"О", "\U041F"=>"П", "\U0420"=>"Р", "\U0421"=>"С",
            "\U0422"=>"Т", "\U0423"=>"У", "\U0424"=>"Ф", "\U0425"=>"Х", "\U0426"=>"Ц", "\U0427"=>"Ч", "\U0428"=>"Ш",
            "\U0429"=>"Щ", "\U042A"=>"Ъ", "\U042B"=>"Ы", "\U042C"=>"Ь", "\U042D"=>"Э", "\U042E"=>"Ю", "\U042F"=>"Я",
            "<\/B>"=>"</b>"));
    }
}