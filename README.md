# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/lumen-framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/lumen)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

> **Note:** In the years since releasing Lumen, PHP has made a variety of wonderful performance improvements. For this reason, along with the availability of [Laravel Octane](https://laravel.com/docs/octane), we no longer recommend that you begin new projects with Lumen. Instead, we recommend always beginning new projects with [Laravel](https://laravel.com).

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 1XX — Informational Responses

100 — Continue
A status code of 100 indicates that (usually the first) part of a request has been received without any problems, and that the rest of the request should now be sent.

## 2XX — Success Responses

200 — OK
This is the code that browsers receive when every has gone according to plan.

201 — Created
This code indicates that a request was successful and as a result, a resource has been created (for example a new page).

204 — No Content
The 204 status code means that the request was received and understood, but that there is no need to send any data back.

205 — Reset Content
This code is a request from the server to the client to reset the document from which the original request was sent. For example, if a user fills out a form, and submits it, a status code of 205 means the server is asking the browser to clear the form.

206 — Partial Content
This is a response to a request for part of a document. This is used by advanced caching tools, when a browser requests only a small part of a page, and just that section is returned.

## 3XX — Redirection Responses

300 — Multiple Choices
The 300 status code indicates that a page or document has moved. The response will also include a list of new locations so the browser can pick a place to redirect to.

301 — Moved Permanently
This tells a browser that the resource it asked for has permanently moved to a new location. The response should also include the location. It also tells the browser which URL to use the next time it wants to fetch it.

304 — Not Modified
The 304 status code is sent in response to a request (for a document) that asked for the document only if it was newer than the one the client already had. Normally, when a document is cached, the date it was cached is stored. The next time the document is viewed, the client asks the server if the document has changed. If not, the client just reloads the document from the cache.

307 — Temporary Redirect
307 is the status code that is sent when a document is temporarily available at a different URL, which is also returned. There is very little difference between a 302 status code and a 307 status code. 307 was created as another, less ambiguous, version of the 302 status code.

## 4XX — Client Error Responses

400 — Bad Request
A status code of 400 indicates that the server did not understand the request due to bad syntax.

401 — Unauthorized
A 401 status code indicates that before a resource can be accessed, the client must be authorised by the server.

402 — Payment Required
The 402 status code is not currently in use, being listed as "reserved for future use". It's interesting to think about how this will be used in the future, especially now that Chrome natively blocks some intrusive ads.

403 — Forbidden
A 403 status code indicates that the client cannot access the requested resource. That might mean that the wrong username and password were sent in the request, or that the permissions on the server do not allow what was being asked.

404 — Not Found
The best known of them all, the 404 status code indicates that the requested resource was not found at the URL given, and the server has no idea how long for.

408 — Request Timeout
A 408 status code means that the client did not produce a request quickly enough. A server is set to only wait a certain amount of time for responses from clients, and a 408 status code indicates that time has passed.

410 — Gone
A 410 status code is the 404's lesser known cousin. It indicates that a resource has permanently gone (a 404 status code gives no indication if a resource has gone permanently or temporarily), and no new address is known for it.

415 — Unsupported Media Type
A 415 status code is returned by a server to indicate that part of the request was in an unsupported format.

## 5XX — Server Error Responses

500 - Internal Server Error
A 500 status code (which developers see more often that they want) indicates that the server encountered something it didn't expect and was unable to complete the request.

503 — Service Unavailable
A 503 status code is most often seen on extremely busy servers, and it indicates that the server was unable to complete the request due to a server overload.

503 Isn't So Bad
If you ever see one of these errors on your own website, and you don't know what to do, take a look at this list. With this, you'll be able to let us (if we host your website) know what's actually going on when your website looks like it's broken.

If it's a 503, maybe you're going viral!

## 6XX — Aplication Error

601 -- Login Credentials Incorrect
A 601 status code is returned when the login credentials provided by the client are incorrect.

602 -- JWT Token Expired
A 602 status code is returned when the JWT Token Expired.

603 -- JWT Token Invalid
A 603 status code is returned when the JWT Token Invalid.

604 -- JWT Custom error
A 604 status code is returned when the JWT Custom error.
