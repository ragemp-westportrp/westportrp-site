<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
if($this->modal['show']) {
    //echo '<script type="text/javascript">$(document).ready(function(){ $("#modalInfo").openModal(); });</script>';
    echo '<script type="text/javascript">M.toast({html: \'' . $this->modal['text'] . '\', classes: \'rounded\'});</script>';
}
?>
</body>
</html>
