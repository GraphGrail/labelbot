<p align="center">
    <h1 align="center">LabelBot (Label Assignment Bot) Project</h1>
    <br>
</p>

Telegram Bot for data labeling.

INSTALLATION
------------

```
php composer.phar install
php init
php yii migrate
```



Nginx config
------------

For pretty ulrls in yii, add to nginx virtual host config:

```
location / {
    ...
    try_files $uri $uri/ /index.php&$query_string;
}
```



Telegram webhook setting:
----------------

a) Setting webhook for local development use ngrok [https://ngrok.com] to redirect webhook via ssl:

```
ngrok http -host-header=<your_local_domain.name> 80
php yii telegram/set-webhook <your_local_domain.name>
```

b) Setting webhook for Production:
```
php yii telegram/set-webhook <domain.name> </path/to/self-signed_certificate>
```

Example:
```
php yii telegram/set-webhook labelbot.graphgrail.com /etc/nginx/ssl/labelbot.graphgrail.com.pe
```

Webhook testing:
```
php yii telegram/get-webhook-info
```

