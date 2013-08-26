<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require 'config.php';

// authentification
if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {
  if (empty($_GET['page'])) $_GET['page'] = 'home';
  $_GET['page'] = htmlspecialchars($_GET['page']);
  $_GET['page'] = str_replace("\0", '', $_GET['page']);
  $_GET['page'] = str_replace(DIRECTORY_SEPARATOR, '', $_GET['page']);
  $display = true;
  function is_active($page) {
    if ($page == $_GET['page'])
      echo ' class="active"';
  }
}
else {
  $_GET['page'] = 'login';
  $display = false;
}

$page = 'pages'. DIRECTORY_SEPARATOR.$_GET['page']. '.php';
$page = file_exists($page) ? $page : 'pages'. DIRECTORY_SEPARATOR .'404.php';

$rootpermission;

if (!isset($_COOKIE['rootpermission']) || isset($_GET['forcerootpermissioncheck'])) {
	$rootpermission = exec('/usr/bin/sudo -ln');
	
	if ($rootpermission != null && $rootpermission != "")
	{
		setcookie("rootpermission", "true", time()+3600);
		$rootpermission = "true";
	}
	else
	{
		setcookie("rootpermission", "false", time()+3600);
		$rootpermission = "false";
	}	
}
else if (isset($_COOKIE['rootpermission']))
{
	$rootpermission = $_COOKIE['rootpermission'];
}

if (isset($_GET['action']) && $rootpermission == "true")
{
	$action = $_GET['action'];
	if ($action == 'reboot')
	{		
		shell_exec("sudo /sbin/shutdown -r now");
	}
	else if ($action == 'shutdown')
	{
		shell_exec("sudo /sbin/shutdown -h now");
	}
}

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Raspcontrol</title>
    <meta name="author" content="Nicolas Devenet" />
    <meta name="robots" content="noindex, nofollow, noarchive" />
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="icon" type="image/png" href="img/favicon.ico" />
    <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" />
    <link href="css/raspcontrol.css" rel="stylesheet" media="screen" />
  </head>

  <body>

    <header>
      <div class="container">
        <a href="<?php echo INDEX; ?>"><img src="img/raspcontrol.png" alt="rbpi" /></a>
        <h1><a href="<?php echo INDEX; ?>">Raspcontrol</a></h1>
        <h2>The Raspberry Pi Control Center</h2>		
      </div>	 
    </header>
	<div id="popover-requirerootpermission-head" class="hide">Root permission</div>
	<div id="popover-requirerootpermission-body" class="hide">To perform this action Raspcontrol must have root permission</div>		
    <?php if ($display) : ?>

    <div class="navbar navbar-static-top navbar-inverse">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <div class="nav-collapse collapse">
			  <ul class="nav">
				<li<?php is_active('home'); ?>><a href="<?php echo INDEX; ?>"><i class="icon-home icon-white"></i> Home</a></li>
				<li<?php is_active('details'); ?>><a href="<?php echo DETAILS; ?>"><i class="icon-search icon-white"></i> Details</a></li>
				<li<?php is_active('services'); ?>><a href="<?php echo SERVICES; ?>"><i class="icon-cog icon-white"></i> Services</a></li>
				<li<?php is_active('disks'); ?>><a href="<?php echo DISKS; ?>"><i class="icon-disks icon-white"></i> Disks</a></li>
			  </ul>
			  <ul class="nav pull-right">
				<li><a href="<?php echo LOGOUT; ?>"><i class="icon-off icon-white"></i> Logout</a></li>	
				<li><a <?php echo ($rootpermission == "true" ? 'href="' . REBOOT . '"' : 'class="popover-requirerootpermission" href="#"'); ?>><i class="icon-repeat icon-white"></i> Reboot</a></li>
				<li><a <?php echo ($rootpermission == "true" ? 'href="' . SHUTDOWN . '"' : 'class="popover-requirerootpermission" href="#"'); ?>><i class="icon-stop icon-white"></i> Shutdown</a></li>			 
			  </ul>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>

    <div id="content">
      <?php if (isset($_SESSION['message'])) { ?>
      <div class="container">
        <div class="alert alert-error">
          <strong>Oups!</strong> <?php echo $_SESSION['message']; ?>
        </div>
      </div>
      <?php unset($_SESSION['message']); } ?>

<?php
  include $page;
?>

    </div> <!-- /content -->

    <footer>
      <div class="container">
        <p>Powered by <a href="https://github.com/Bioshox/Raspcontrol">Raspcontrol</a>.</p>
        <p>Sources are available on <a href="https://github.com/Bioshox/Raspcontrol">Github</a>.</p>
      </div>
    </footer>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<?php
		// load specific scripts
		if ('details' === $_GET['page']) {
			echo '   <script src="js/details.js"></script>';
		}
		else if ('home' === $_GET['page'])
		{
			echo '   <script src="js/home.js"></script>';
		}		
		else if ('services' === $_GET['page'])
		{
			echo '   <script src="js/services.js"></script>';
		}
		else if ('disks' === $_GET['page'])
		{
			echo '   <script src="js/disks.js"></script>';
		}				
	?>
	
	<!-- General scripts -->
	<script>
	$('.popover-requirerootpermission').popover({
	html : true,
	placement : 'bottom',
	trigger : 'hover',
	title : function() {
		return $("#popover-requirerootpermission-head").html();
	},
	content : function() {
		return $("#popover-requirerootpermission-body").html();
	}
	});
	</script>
  </body>
</html>
