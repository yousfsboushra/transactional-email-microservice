# transactional-email-microservice

## Docker
- I tried many docker images and found that https://github.com/lephleg/laravel-lumen-docker is the fastest one to establish an environment for lumen development.

- I used https://packagist.org/packages/flipbox/lumen-generator to add more commands

## JSON API
- URL: `http://localhost/sendmail`
- Method: `POST`
- Body:
```json
{
	"recipients": [
		"john@example.com",
        "smith@example.com"
	],
	"from": "info@takeaway.com",
	"subject": "Time for Takeaway.com",
	"contentType": "html",
	"message": "<a href=\"https://www.takeaway.com\">Takeaway.com</a> is a leading online food delivery marketplace, focused on connecting consumers and restaurants through its platform in 10 European countries and Israel. <a href=\"https://www.takeaway.com\">Takeaway.com</a> offers an online marketplace where supply and demand for food delivery and ordering meet."
}
```
- Responses:
```json
{"message":"Mails were sent successfully by *mailservice*"}
```
```json
{"error":"Mails couldn't be sent by any mail service"}
```
Note: all arguments are required except `contentType`, its default value is `text`

## CLI
```
php artisan send:mail --f=From --r=Recipients* --c=Content type --s=Subject --m=Message
```

Example Command:
```
php artisan send:mail --f=info@takeaway.com --r=john@example.com --r=smith@example.com --c=text --s="Time for Takeaway.com" --m="<a href=\"https://www.takeaway.com\">Takeaway.com</a> is a leading online food delivery marketplace, focused on connecting consumers and restaurants through its platform in 10 European countries and Israel. <a href=\"https://www.takeaway.com\">Takeaway.com</a> offers an online marketplace where supply and demand for food delivery and ordering meet."
```

Responses:
```
Mails were sent successfully by *mailservice*
```

```
Mails couldn't be sent by any mail service
```

Notes
- You will be asked to fill any missing argument
- Content type default value is `text`