{% if type == "ip" %}
    Banned ip {{ ip }}<br>
    Reason: {{ reason }}
{% elseif type == "user" %}
    Banned user {{ user.name }}<br>
    Reason: {{ user.ban_reason }}
{% endif %}