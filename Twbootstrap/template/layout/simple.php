<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $this->application_name ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="/twbootstrap/bootstrap.css" rel="stylesheet">
    <style>
      body {
        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
      }
    </style>
    <link href="/twbootstrap/bootstrap-responsive.css" rel="stylesheet">
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
				<a class="brand" href="#"><?= $this->application_name ?></a>
				<div class="nav-collapse">
					<ul class="nav">
						<li class="active"><a href="#">Home</a></li>
						<li><a href="#about">About</a></li>
						<li><a href="#contact">Contact</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container">
		<?= $this->content ?>
	</div>

	<script src="/twbootstrap/jquery.js"></script>
	<script src="/twbootstrap/bootstrap-transition.js"></script>
	<script src="/twbootstrap/bootstrap-alert.js"></script>
	<script src="/twbootstrap/bootstrap-modal.js"></script>
	<script src="/twbootstrap/bootstrap-dropdown.js"></script>
	<script src="/twbootstrap/bootstrap-scrollspy.js"></script>
	<script src="/twbootstrap/bootstrap-tab.js"></script>
	<script src="/twbootstrap/bootstrap-tooltip.js"></script>
	<script src="/twbootstrap/bootstrap-popover.js"></script>
	<script src="/twbootstrap/bootstrap-button.js"></script>
	<script src="/twbootstrap/bootstrap-collapse.js"></script>
	<script src="/twbootstrap/bootstrap-carousel.js"></script>
	<script src="/twbootstrap/bootstrap-typeahead.js"></script>
</body>
</html>
