
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?= $this->application_name ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="/twbootstrap/bootstrap.css">
	<link rel="stylesheet" href="/dataTables/DT_bootstrap.css">
	<link rel="stylesheet" href="/chosen/chosen.css">
	<link rel="stylesheet" href="/bootstrap.css">
	<style>
		body {
			padding: 60px 0; /* 60px to make the container go all the way to the bottom of the topbar */
		}
	</style>
	<link href="/twbootstrap/bootstrap-responsive.css" rel="stylesheet">
	<link href="/favicon.ico" rel="icon" type="image/x-icon" />
</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="/"><?= $this->application_name ?></a>
				<div class="nav-collapse">
					<ul class="nav">
					<?php
					foreach ( $this->menu_main as $module => $menu ) {
						if ( count( $menu ) < 2 ) {
							foreach ( $menu as $page => $infos ) {
					?>
							<li><a href="<?= $infos[ 'url' ] ?>" title="<?= $infos[ 'description' ] ?>"><?= $module ?></a></li>
							<?php
							}
						} else {
						?>
						<li class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><?= $module ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
							<?php
							foreach ( $menu as $page => $infos ) {
							?>
								<li><a href="<?= $infos[ 'url' ] ?>" title="<?= $infos[ 'description' ] ?>"><?= $infos[ 'label' ] ?></a></li>
							<?php
							}
							?>
							</ul>
						</li>
						<?php
						}
					}
					?>
					</ul>

					<ul class="nav pull-right">
					<?php
					foreach ( $this->menu_session as $module => $menu ) {
						if ( count( $menu ) < 2 ) {
							foreach ( $menu as $page => $infos ) {
					?>
							<li><a href="<?= $infos[ 'url' ] ?>" title="<?= $infos[ 'description' ] ?>"><?= $infos[ 'label' ] ?></a></li>
							<?php
							}
						} else {
						?>
						<li class="dropdown">
							<a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;"><?= $module ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
							<?php
							foreach ( $menu as $page => $infos ) {
							?>
								<li><a href="<?= $infos[ 'url' ] ?>" title="<?= $infos[ 'description' ] ?>"><?= $infos[ 'label' ] ?></a></li>
							<?php
							}
							?>
							</ul>
						</li>
						<?php
						}
					}
					?>
					</ul>

				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container">
		<?php
		foreach( \Notification::pull(  ) as $notification ) {
		?>
			<div class="alert alert-<?= $notification->level ?>">
				<?= $notification ?>
			</div>
		<?php
		}
		?>
		<?= $this->display( $this->content ) ?>
	</div>

	<script src="/twbootstrap/jquery.js"></script>
	<script src="/dataTables/jquery.dataTables.min.js"></script>
	<script src="/dataTables/DT_bootstrap.js"></script>
	<script src="/chosen/chosen.jquery.min.js"></script>
	<script>
		$(".attribute select").chosen();
	</script>
</body>
</html>
