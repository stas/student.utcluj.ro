<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$site_title; if($page_title) echo " / ".$page_title; ?></title>
    <link rel="stylesheet" href="/css/base.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />
    <link rel="shortcut icon" href="http://www.utcluj.ro/favicon.ico" type="image/x-icon" /> 
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
</head>
<body>
  <div id="container">
    <div id="header">
      <div id="logo"></div>
      <h1><a href="/"><?php echo $site_title; ?></a></h1>
      <div id="main-navigation">
        <ul class="wat-cf">
          <?php include "menu.php"; ?>
        </ul>
      </div>
    </div>
    <div id="wrapper" class="wat-cf">
      <div id="main">
        <div class="block" id="block-text">
            
          <div class="content">
            <?=$content ?>
          </div>
        </div>
      </div>
      <div id="sidebar">
        <?php include $site_simple_block; ?>
        <?php include $site_notice_block; ?>
      </div>
    </div>
    <div id="footer">
        <?php include $site_footer; ?>
    </div>
</body>
</html>