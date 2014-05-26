<!DOCTYPE html>
<html>
	<head>
        {% if title is defined %}
		    <title>{{ title }} | PicHub.ru - фотохостинг</title>
        {% else %}
            <title>PicHub.ru - фотохостинг</title>
        {% endif %}
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link href="http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel='stylesheet' />
        <link href="/css/style.css" rel="stylesheet" />
        <script src="/js/jquery.min.js"></script>
        <script src="/js/main.js"></script>

        <link rel="stylesheet" href="/css/fallr/jquery-fallr-1.3.css">
        <script type="text/javascript" src="/js/fallr/jquery.easing.1.3.js"></script>
        <script type="text/javascript" src="/js/fallr/highlight.pack.js"></script>
        <script src="/js/fallr/jquery-fallr-1.3.pack.js"></script>

    </head>
	<body>
        <div id="container">
		    {{ content() }}
        </div>
	</body>
</html>