<!DOCTYPE html>
<html class="no-js">
<head>
	<meta charset="utf-8">
	<title>Transportation</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet'>

	<!-- Syntax Highlighter -->
	<link rel="stylesheet" type="text/css" href="{{ asset('syntax-highlighter/styles/shCore.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('syntax-highlighter/styles/shThemeDefault.css') }}">

	<!-- Font Awesome CSS-->
	<link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">

	<!-- Normalize/Reset CSS-->
	<link rel="stylesheet" href="{{ asset('css/normalize.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/main.css') }}">
</head>

<body id="welcome">
<aside class="left-sidebar">
<div class="logo">
	<a href="#welcome">
		<h1>Transportation</h1>
	</a>
</div>

<nav class="left-nav">
	<ul id="nav">
		<li class="current"><a href="#welcome">Welcome</a></li>
		<li><a href="#installation">Installation</a></li>
		<li><a href="#tmpl-structure">Structure</a></li>
		<li><a href="#css-structure">CSS Files</a></li>
		<li><a href="#javascript">JavaScript Libraries</a></li>
		<li><a href="#contact-form">Contact Form</a></li>
		<li><a href="#subscription-form">Subscription Form</a></li>
		<li><a href="#video">Video Tutorial</a></li>
		<li><a href="#credit">Source and Credit</a></li>
	</ul>
</nav>
</aside>

<!-- Main Wrapper -->
<div id="main-wrapper">
<div class="main-content">

	<section id="welcome">
		<div class="content-header">
			<h1>Transportation</h1>
		</div>
		<div class="welcome">
			<h2 class="twenty">Welcome To Transportation</h2>
			<p>Firstly, a huge thanks for purchasing this theme, your support is truly appreciated!</p>
			<p>This document covers the installation and use of this theme and often reveals answers to common problems and issues - read this document thoroughly if you are experiencing any difficulties. If you have any questions that are beyond the scope of this document. Thank you so much!</p>
		</div>
		<div class="features">
			<h2 class="twenty">Template Features</h2>
			<ul>
				<li>Clean &amp; Simple Design</li>
				<li>HTML5 &amp; CSS3</li>
				<li>Fully Responsive Design</li>
				<li>PHP/Ajax Powered Working Contact Form</li>
				<li>All files are well commented</li>
				<li>Cross Browser Compatible with IE11+, Firefox, Safari, Opera, Chrome</li>
				<li>Extensive Documentation</li>
			</ul>
		</div>
	</section>

	<section id="installation">
		<div class="content-header">
			<h1>Transportation</h1>
		</div>
		<h2 class="title">Installing Template.</h2>
		<div class="section-content">
			<ol>
				<li>After unzip the download pack, you'll found a Template Folder with all the files.</li>
				<li>You can view this Template in any browser, or edit it offline.</li>
				<li>Contact section needs a server for sending emails.</li>
			</ol>
		</div>
	</section>

	<section id="tmpl-structure">
		<h2 class="title">Template Structure</h2>
		<p class="fifteen">Here is the general structure of index.html.</p>
	</section>

	<section id="css-structure">
		<h2 class="title">CSS Files and Structure</h2>
	</section>

	<section id="javascript">
		<h2 class="title">Javascript Files and Structure</h2>
	</section>

	<section id="contact-form">
		<h2 class="title">Contact Form</h2>
	</section>

	<section id="subscription-form">
		<h2 class="title">Subscription Form</h2>
	</section>

	<section id="video">
		<h2 class="title">Video Tutorial</h2>
		<div class="embed-responsive embed-responsive-21by9">
			<iframe class="embed-responsive-item" width="100%" height="515" src="https://www.youtube.com/embed/i7_PRPLOxVE?rel=0&controls=0&showinfo=0" frameborder="0" allowfullscreen></iframe>
		</div>
	</section>

	<section id="credit">
		<h2 class="title">Source and Credit</h2>
	</section>

</div>
</div>

<!-- JS Libraries -->
<script src="{{ asset('js/jquery-1.11.0.min.js') }}"></script>
<script src="{{ asset('js/jquery.nav.js') }}"></script>

<script src="{{ asset('syntax-highlighter/scripts/shCore.js') }}"></script>
<script src="{{ asset('syntax-highlighter/scripts/shBrushXml.js') }}"></script>
<script src="{{ asset('syntax-highlighter/scripts/shBrushCss.js') }}"></script>
<script src="{{ asset('syntax-highlighter/scripts/shBrushJScript.js') }}"></script>
<script src="{{ asset('syntax-highlighter/scripts/shBrushPhp.js') }}"></script>

<script>
	SyntaxHighlighter.all()
</script>

<script src="{{ asset('js/custom.js') }}"></script>

</body>
</html>
