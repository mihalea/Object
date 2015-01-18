<?php
$page = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
$root = "//127.0.0.1/test/";
?>

<nav class="navbar navbar-default">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href=<?=$root?>>
				<span class="glyphicon glyphicon-home" aria-hidden="true"/>
			</a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li <?php if ($page == $root . "index.php") echo ' class="active"'?>>
					<a href=<?=$root?>>Frontpage</a>
				</li>
				<li <?php if (preg_match('/groups/', $page)) echo ' class="active"'?>>
					<a href=<?=$root, "groups"?>>Groups</a>
				</li>
				<li <?php if (preg_match('/events/', $page)) echo ' class="active"'?>>
					<a href=<?=$root, "events"?>>Events</a>
				</li>
				<li <?php if (preg_match('/grades/', $page)) echo ' class="active"'?>>
					<a href=<?=$root, "grades"?>>Grades</a>
				</li>
			</ul>
			
				<ul class="nav navbar-nav navbar-right">
						
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user">&nbsp;</span><?php echo $_SESSION["uname"]; ?><span class="caret"></span></a>
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