<html>
<head>
  <title>Svid</title>
  <meta http-equiv="refresh" content="30">
  <link rel="stylesheet" type="text/css" href="mystyle.css">
</head>
<body>

<table style="width:100%">
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

function deleteVideo($fstring) {
  $farray = explode(',', $fstring);
  $htmlfilename = "../video/" . $farray[1];
  $vidfilename = "../video/" . substr($farray[1], 0, -5) . ".mp4";
  unlink($htmlfilename);
  unlink($vidfilename);
}

if (isset($_POST['encode'])) {
  beginEncoding($_POST['encode']);
}
if (isset($_POST['delete'])) {
  deleteVideo($_POST['delete']);
}

//path,filename,size(bytes)
$all_files = explode("\n", shell_exec('find /home/transmission -type f -exec ./test_if_video.sh \{\} \; -printf "%h,%f,%s\n"'));
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
</table>

<table style="width:100%">
<br><br><br><br>
<tr>
  <th>Encoded File</th>
  <th>Link</th>
  <th>Delete</th>
</tr>

<?php
$all_files = explode("\n", shell_exec('find ../video -name "*.html" -type f -printf "%h,%f\n"'));
foreach ($all_files as $fstring) {
  if ($fstring != '') {
    $farray = explode(',', $fstring);
  } else {
    continue;
  }

  echo("<tr>\n");
  // Filename
  echo("  <td>" . substr($farray[1], 0, -5) . "</td>\n");
  // Link
  echo("  <td><a href='../video/" . $farray[1] . "'>Link</a></td>\n");
  // Delete Button
  echo("  <td>\n");
  echo('    <form action="index.php" method="post">');
  echo('      <button type="submit" value="' . $fstring . '" name="delete">Delete</button>' . "\n");
  echo('    </form>');
  echo("  </td>\n");

  echo("</tr>\n");
}

?>
</table>
</body>
</html>
