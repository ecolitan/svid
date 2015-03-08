<html>
<head>
  <title>Svid</title>
</head>
<body>

<table style="width:100%" border="1">
<?php
function beginEncoding($filename) {
  echo("/home/transmission/" . $filename);
}

if (isset($_POST['encode'])) {
  beginEncoding($_POST['encode']);
 // fork encoding process off and write logs to file
}

$all_files = explode("\n", shell_exec('find /home/transmission -type f -printf "%f\n"'));
foreach ($all_files as $fstring) {
  echo("<tr>\n");
  echo("  <td>" . $fstring . "</td>\n");
  echo("  <td>\n");
  echo('    <form action="index.php" method="post">');
  echo('      <button type="submit" value="' . $fstring . '" name="encode">Encode</button>' . "\n");
  echo('    </form>');
  echo("  </td>\n");
  echo("</tr>\n");
}
?>
</body>
</html>
