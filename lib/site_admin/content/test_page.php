<?php

$D = Get('DIALOGID');

print <<<LBL1
<h1>Dialog: $D</h1>
<h2><input type="button" value="test function" onclick="$('#appform$D', top.document).css({top: 0, marginLeft:400});" /></h2>

LBL1;

