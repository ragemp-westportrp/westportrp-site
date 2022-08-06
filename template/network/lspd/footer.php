<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $serverName;

?>

<footer class="grey darken-3" style="padding: 24px">
    <div class="footer-copyright grey-text">
        <div class="container">
            Copyright © <?php echo date('Y'); ?> <a class="grey-text">State 99 Team</a>
        </div>
    </div>
</footer>

<?php
if($this->modal['show']) {
    //echo '<script type="text/javascript">$(document).ready(function(){ $("#modalInfo").openModal(); });</script>';
    echo '<script type="text/javascript">M.toast({html: \'' . $this->modal['text'] . '\', classes: \'rounded\'});</script>';
}
?>
</body>
</html>