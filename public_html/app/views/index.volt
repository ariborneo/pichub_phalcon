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
    </head>
	<body>
		{{ content() }}
	</body>
</html>