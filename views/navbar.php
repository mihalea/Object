<?php
	$path = realpath($_SERVER["DOCUMENT_ROOT"] . "/test/") . "/";
	require_once($path . "config/site.php");
	echo '<base href="' . SITE_ROOT . '/">';
	$page = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
?>

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href=<?=SITE_ROOT?>>
				<span class="glyphicon glyphicon-home" aria-hidden="true"/>
			</a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li <?php if ($page == SITE_ROOT . "index.php") echo ' class="active"'?>>
					<a href=<?=SITE_ROOT?>>Frontpage</a>
				</li>
				<li <?php if (preg_match('/test\/groups/', $page)) echo ' class="active"'?>>
					<a href=<?=SITE_ROOT, "groups?select"?>>Groups</a>
				</li>
				<li <?php if (preg_match('/test\/events/', $page)) echo ' class="active"'?>>
					<a href=<?=SITE_ROOT, "events"?>>Events</a>
				</li>
				<!--<li <?php if (preg_match('/test\/grades/', $page)) echo ' class="active"'?>>
					<a href=<?=SITE_ROOT, "grades"?>>Grades</a>-->
				</li>
			</ul>
			
				<ul class="nav navbar-nav navbar-right">
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user">&nbsp;</span><?php echo $_SESSION["name"]; ?><span class="caret"></span></a>
							<ul class="dropdown-menu" role="menu">
								<li><a href="index.php?logout"><span class="glyphicon glyphicon-log-out">&nbsp;</span>Logout</a></li>
							</ul>
						</li>
						
				</ul>
		</div>
		<!-- /.navbar-collapse -->
	</div>
	<!-- /.container-fluid -->
</nav>