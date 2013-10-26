fossilize
=========

A small php script to manage fossil repositores hosted on a web server.

###Motivation

Have a personal cloud of fossil repositories that can be accessed
remotely as keep them on a place that has scheduled backups. Also
have a better fossil skin that is responsive by using bootstrap.

###Installation

To install adjust repos.cgi and repos.php to match your settings. You
may also need to modify the .htaccess file to meet your needs.

###Setting files

We ship 2 default set of settings:

* private_settings.fossil
* public_settings.fossil

Basically these 2 files are fossil repositories with predefined 
permissions and bootstrap skin that seamlessly adjust to repos.php
script. When you add a new repo and select private the private fossil
repo is used as a template for your newly created repo. The same 
applies for public. If you select default then none of this templates
is used as a base.

The default username and password for these 2 fossil repositories is:

    username: user
    password: user
