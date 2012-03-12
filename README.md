Lightspeed-PHP framework
========================

![Lightspeed-PHP logo](http://lightspeed-php.com/images/logo.png "Lightspeed-PHP")

**LIGHTSPEED-PHP IS A MINIMALISTIC AND FAST PHP FRAMEWORK** aiming to provide basic structure that helps you build your applications faster and more efficiently on solid architecture. It's designed to be small, fast, easy to understand and extend.


WHY ANOTHER FRAMEWORK?
----------------------
I have used several frameworks in my time and designed a few before. While there are other great libraries out there like the Zend Framework, they are **often bloated, slow and hard to understand** and thus extend. Lightspeed is down to business and you can go over the whole core code of it in a coffee-break time. It still has all the main features you would expect from a framework and its **structured similarly to Zend Framework** so anyone from that background will have no problem understanding it quickly.

Every part of the system has been designed **with performance in mind** and caching is used internally and also provided for simple use to the user, using both APC and Memcache solutions.

It's licenced under the **very permissive [MIT licence](http://en.wikipedia.org/wiki/MIT_License)** so you can do pretty much anything with it.


FEATURES
--------
Lightspeed PHP is **Model-View-Controller** design pattern oriented. The main tasks it solves are the following:

* Bootstrap the application
* Accept and understand a HTTP request
* Route and dispatch this request to an appropriate controller
* Execute business logic and render the results in a view
* Send back the response

In addition, the core framework handles translations for both routes and page content and provides simple interfaces for accessing caches and sessions.

Thats pretty much it, the core consists of only around ten classes and around thousand lines of code gets executed to handle the request, routing, dispatching, translations, view rendering and response. It really is **tiny and fast**.

For additional performance boost, it has been built from ground up to play well with the wonderful PHP to C++ transformer and compiler called **Hiphop-PHP by Facebook** that can make a simple Lightspeed application respond to requests in under a millisecond showing where the name comes from.

If you’re into performance and scalability like me, you might want to check out my [Cassandra PHP Client Library](https://github.com/kallaspriit/Cassandra-PHP-Client-Library "CPCL") built for reliable and fast communication with the wonderful Cassandra non-relational database.


GETTING STARTED
---------------
Starting with Lightspeed PHP is easy, just download the framework and you will have a hello world application running. You may then want to check out [the tutorial](http://lightspeed-php.com/tutorial), that aims to teach you the core concepts to get up and running quickly. When you need specific help about some components, check out [the manual](http://lightspeed-php.com/manual) explaining how to use and extend each part of the system. There is also a low-level [reference](http://lightspeed-php.com/reference) generated from the source code, explaining every method.


ADDONS
------
One aim of the project is to keep the core of Lightspeed PHP framework as compact as possible not to litter it with functionality that you might not need. For this reason any non-essential functionality such as database layers are maintained as seperate projects and you may choose to use something else.

Below is a list of official add-ons:
* [PdoModel database access layer](https://github.com/kallaspriit/Lightspeed-PHP-PdoModel-Addon)

Should you build something cool then let we know and it will be listed here


LICENCE
-------
Lightspeed-PHP is **free open-source software** licenced under the very permissive **MIT licence** which generally means that you can do pretty much anything you like with it.