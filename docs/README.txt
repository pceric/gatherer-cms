README
======

Introduction
============
Welcome to the Gatherer Content Management System (GCMS).  GCMS was created to
make two time consuming tasks, reading your RSS feeds and updating your website,
and combine it into one.  Now while you read your daily feeds you can share your
favorites with your readers.  In addition you have the full power of a
next generation content management system.

GCMS is designed as an easy-to-use single admin system with WYSIWYG editors and
support for theming and addons.


Requirements
============
- Apache Web Server
- PHP v5.2.4+ with the following libraries:
  - mysql or pgsql
  - libxml
  - gd
  - tidy (optional)
- Smarty v3.0.9+ and The Zend Framework v1.11.14+ installed w correct paths
  configured in your php.ini.  If you don't have access to php.ini, you may
  install them into the library directory and edit application/configs/application.ini.
- Database Server (MySQL 5.0+ or PostgreSQL 9.0+ supported but any other DB
  server supported by the Zend Framework should work with some hacking).


Installation
============
1) Extract to your website's root directory.  You may want to configure
   a VHost as shown below.  Ensure the entire data directory is writable
   by PHP.
2) Create a new database and import the correct install.*.sql file found in the
   scripts directory into that database.
   e.g.: mysql> CREATE DATABASE gcms DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
         mysql> use gcms
         mysql> source install.mysql5.sql
3) Edit application/configs/application.ini with your favorite editor and enter your database info.
4) Open your web browser to your website and follow the directions.
5) To make full use of GCMS you may want to sign up for free accounts on Google+, reCAPTCHA, and Disqus.


Setting Up Your VHOST
=====================
The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/var/www/html/gatherer/public"
   ServerName gatherer.local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development
    
   <Directory "/var/www/html/gatherer/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>
    
</VirtualHost>


Importing from another CMS
==========================
There is an experimental importer for Drupal and Mambo located in the scripts
directory.  Edit import.php and enter the correct settings then run the importer.
It's just a basic data dumper so you'll have to go through and update links,
tags, etc. but it can save an hour or two of cut and pasting.


How to use GCMS
===============
Once installed you may administer GCMS by pointing your browser to /admin/ and
logging in.  GCMS was designed to work with many of the awesome free tools
offered on the web.  The best place to start is to setup a Google Reader
account and subscribe to all your favorite sites.  Once GCMS is configured with
your Google+ info and you +1 an article in Reader, GCMS will then automatically
pull in that post and place it onto your front page.  GCMS can also import from
another RSS/ATOM feed if you use another aggregator service besides Google to
read feeds.

GCMS is also a powerful Content Management System and allows easy editing and
adding of news posts and full articles using the included JavaScript
WYSIWYG editors.  Experimental image uploading support is available with the
CKEditor and will automatically generate a thumbnail image.

Both your personal and Google Reader's shared blog posts are shared through
GCMS's feed generator in both RSS 2.0 and ATOM 1.0 which you can find on your
front page.  This allows your family, friends and loyal followers to keep
up to date on the things important to you.


Customize GCMS with Themes & Filters
====================================
GCMS uses the Zend Framework and tries to follow standard ZF conventions when
possible.  This makes it simple for anyone familier with ZF to add new modules,
plugins, helpers, and other ZFisms to the underlying engine.

For theming GCMS uses the Smarty 3 template engine for fast and easy theme
creation.  GCMS is packaged with a few example themes based around the Twitter
Bootstrap and can be found in the public/themes directory.  GCMS themes are
configured in fallback mode, if a resource cannot be found it will then look
in the default theme for the correct resource.

GCMS sets a few Smarty variables for use in any template.  They
are as follows:
"gcms.config"   - contains entries of everything found in the config table
"gcms.param"    - contains a list of parameters passed to the current page
"gcms.version"  - contains the current GCMS version
"gcms.isAdmin"  - returns true if you're logged in

The use of the Smarty engine allows a wide range of existing code, such as
plugins and filters, to be used with GCMS.  GCMS includes a GeSHi filter 
when using HTML5 <code class="language-[supported lang here]"></code> notation.


Community
=========
Code hosted on GitHub at github.com/pceric/gatherer-cms


Legal
=====
Gatherer Content Management System is Copyright (c) 2007-2012 Eric Hokanson and
released under the LGPL v3.  See COPYING and COPYING.LESSER for more info.

GCMS ships with several 3rd party libraries each with their own respective
copyright and licenses.
