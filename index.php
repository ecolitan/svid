<html>
<head>
  <title>Svid</title>
  <meta http-equiv="refresh" content="30">
</head>
<body>

<table style="width:100%" border="1">
<tr>
  <th>Filename</th>
  <th>Size (bytes)</th>
  <th>Status</th>
  <th>Encode</th>
</tr>

<?php

function beginEncoding($fstring) {
  $farray = explode(',', $fstring);
  $jobfile = fopen("../queue/" . $farray[1] . ".job", "w");
  fwrite($jobfile, $farray[0]);
  fclose($jobfile);
}

if (isset($_POST['encode'])) {
  beginEncoding($_POST['encode']);
}

//path,filename,size(bytes)
$all_files = explode("\n", shell_exec('find /home/transmission -type f -printf "%h,%f,%s\n"'));
foreach ($all_files as $fstring) {
  if ($fstring != '') {
    $farray = explode(',', $fstring);
  } else {
    continue;
  }

  echo("<tr>\n");

  // Filename
  echo("  <td>" . $farray[1] . "</td>\n");

  // Size
  echo("  <td>" . $farray[2] . "</td>\n");

  // Status
  if (file_exists("../queue/" . $farray[1] . ".job")) {
    echo("  <td>Queued</td>");
  } elseif (file_exists("../processing/" . $farray[1] . ".job")) {
    echo("  <td>Processing</td>");
  } elseif (file_exists("../video/" . $farray[1] . ".html")) {
    echo("  <td><a href='../video/" . $farray[1] . ".html'>video</a></td>");
  } else {
    echo("  <td></td>");
  }

  // Encode Button
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
