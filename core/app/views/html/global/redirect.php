<?php
$title    = $title ? $title : 'Redirecting...';
$redirect = $redirect ? $redirect : $data['redirect'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title><?php echo $title; ?></title>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset='utf-8'" />
  <meta http-equiv="refresh" content="0;URL=<?php echo $redirect; ?>'" />
  <script type="text/javascript">document.location.href = "<?php echo $redirect; ?>";</script>
</head>
<body>
</body>
</html>
