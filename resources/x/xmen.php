<?php
  if (isset($_REQUEST('X')))
    require 'xc.php';
  require '/../gen_session.php';
  require '/../xses.php';
  if (isset($_SESSION['XMen'])): ?>
<?php 

?>
<?php else: ?>
  <span id="menuoptions">
  <!-- <form action="javascript:;" onsubmit=""> </form> -->
  </span>
<?php endif; ?>
