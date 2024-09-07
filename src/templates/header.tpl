<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	{if $redirect != ''}
		<meta http-equiv="refresh" content="{$redirect}; url={$url}" />
	{/if}

	<!-- Title and other stuffs -->
	<title>WDiL?</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="Neil Thompson">

	<!-- Stylesheet -->
	<link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/png" href="/img/favicon.png">
	<link rel="apple-touch-icon" href="/img/favicon.png">

</head>

<body>

    <header>
        <h1><a href="/">WDiL?</a></h1>
		{if $smarty.session.database|isset}
		<nav>
			<a href="/">Home</a>
			{if $smarty.const.ADMIN == $smarty.session.username}
			<a href="/admin">Admin</a>
			{/if}
			<a href="/logout">Logout</a>
		</nav>
		{/if}
    </header>

    <main>
        <!-- Main content goes here -->
		{if $error != ''}
			<p><mark>{$error}</mark></p>
		{/if}
