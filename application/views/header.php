<html>
<head>
<title><?php if(isset($title)) { echo $title . "-" . APPLICATION_NAME; } else { echo APPLICATION_NAME; } ?></title>

<?php $html->_Css('reset','style'); ?>
</head>
<body>
	<div id="container">
		<div id="content">
			<div id="head">
				<a href="<?php echo $html->mvcURL(DEFAULT_CONTROLLER); ?>"><h2><?php echo APPLICATION_NAME; ?></h2></a>
			</div>