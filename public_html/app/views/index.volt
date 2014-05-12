<!DOCTYPE html>
<html>
	<head>
        {% if title is defined %}
		    <title>{{ title }} | PicHub.ru</title>
        {% else %}
            <title>PicHub.ru</title>
        {% endif %}
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
	<body>
		{{ content() }}
	</body>
</html>