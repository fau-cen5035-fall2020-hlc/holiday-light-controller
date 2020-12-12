<?php
echo($_SERVER['REQUEST_URI']);
echo('<br />');
echo($_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
echo('<br />');
echo($_SERVER['PHP_SELF']);
echo('<br />');
print_r(pathinfo($_SERVER['REQUEST_URI']));
echo('<br />');
echo(strpos('Hello World', 'W'));
echo('<br />');
echo(substr('text.php?param1=true', 0, strpos('text.php?param1=true', '?')));
?>