[![Circle CI](https://circleci.com/gh/Prophet731/BFAdminCP.svg?style=svg)](https://circleci.com/gh/Prophet731/BFAdminCP) [![Download Battlefield Admin Control Panel](https://img.shields.io/sourceforge/dt/bfacp.svg)](https://sourceforge.net/projects/bfacp/files/latest/download)[![Latest Stable Version](https://poser.pugx.org/adkgamers/bfadmincp/v/stable)](https://packagist.org/packages/adkgamers/bfadmincp) [![Total Downloads](https://poser.pugx.org/adkgamers/bfadmincp/downloads)](https://packagist.org/packages/adkgamers/bfadmincp) [![Latest Unstable Version](https://poser.pugx.org/adkgamers/bfadmincp/v/unstable)](https://packagist.org/packages/adkgamers/bfadmincp) [![License](https://poser.pugx.org/adkgamers/bfadmincp/license)](https://packagist.org/packages/adkgamers/bfadmincp)

# Overview
The Battlefield Admin Control Panel (BFACP) is a web based admin tool designed to work exclusively with [AdKats](https://github.com/AdKats/AdKats) (v6+) and [XpKillers Chat, GUID, Stats and Mapstats Logger](https://forum.myrcon.com/showthread.php?6698) (v1.0.0.2). The software is built with the [Laravel](http://laravel.com/) PHP framework to speed up development time and make my job a lot easier.

[FAQ](https://github.com/Prophet731/BFAdminCP/wiki/FAQ)

# Requirements

* MySQL Database (5.6+)
* AdKats v6+
* XpKillers Chat, GUID, Stats and Mapstats Logger v1.0.0.2+
* PHP 5.5+
* PHP Mcrypt
* PHP PDO

# Features

* User, Role, and Permission system.
* Live Scoreboard with chat.
* Ban Management for AdKats.
* Detailed player information with graph charts.
* Server statistics page for each server showing population history, uptime history with data from UptimeRobot, and Mapstats.
* Metabans support.
* Report notifications with ability to change alert sounds.
* Chatlog searching where you can search by multiple players and/or keywords and ability to only show from a certain date/time range.
* Message of the Day
* Quick DB Stats overview
* and more!


[![Download Battlefield Admin Control Panel](https://a.fsdn.com/con/app/sf-download-button)](https://sourceforge.net/projects/bfacp/files/latest/download)

Download the latest version. Once downloaded unzip it to a temporary folder on your computer. Next open the `.env.php` file located in the root folder in your favorite text editor. Scroll down to the database settings section and fill in your database connection information.

```PHP
    /**
     * Database Settings
     */
    'DB_HOST'        => 'localhost',
    'DB_USER'        => 'root',
    'DB_PASS'        => '',
    'DB_NAME'        => 'mydatabase',
```

Next we need to create a encryption key. This is **IMPORTANT**! The default key provided is just a placeholder string and is insecure. I have provided a page where you can get a random 32 character string. I do **NOT** save these and they are random on each refresh. You can access this page [here](https://api.gamerethos.net/random_key). Once you have your key open up the file `.env.php` in the root folder and scroll down till you see the `APP_KEY` field. Replace the `YourSecretKey!!!` with the key that was generated. By default it will look like this.

```PHP
    /**
     * Set your app key here
     */
    'APP_KEY'        => 'YourSecretKey!!!'
```

Once completed upload the entire application to your webserver. Once uploaded you will need to modify some file and folder permissions. Change the files and folders permissions under `app/storage` recursively to 0777. This application was designed to run on a subdomain and not from a folder from the TLD. Make sure to create a subdomain and if possible have domain point to the public folder that's located under the root folder.

Now load up application in your web browser and it will begin the process of creating the tables. This process will take a few seconds to run on first load. When it completed you should see the dashboard.

## Default login

**Username**: admin

**Password**: password

You can change the default username and password by clicking on **Site Management** > **Users** > **Admin**

If you have any questions or need help setting this up please post it [here](http://www.adkgamers.com/forum/265-adk-web-dev-support/).

Please create a ticket for bugs/requests. [here](https://github.com/Prophet731/BFAdminCP/issues).

## Installing from the command line

This method is only for those who have shell access to their web server or VPS. This will download and extract the files to your current directory. Make sure it's an empty directory.

If you have git installed you can just run the following command to install it. You must have [composer](https://getcomposer.org/doc/00-intro.md) and php command line installed.

### Composer Method (preferred)

```bash
composer create-project --prefer-dist --no-scripts --keep-vcs adkgamers/bfadmincp .
```

### Git Method

```bash
git clone https://github.com/Prophet731/BFAdminCP.git .
composer install --no-scripts
```

This will clone and install the dependency's need for the BFACP to work. This will checkout the master branch which is the stable version. If you would like to run the develop version you will need to run `git checkout develop` before you issue the composer command.

To update it all you will need to do is run `git pull` and it will pull the latest version on the current branch (master or develop).

To make this an automated process you can create a cron job for it.

```bash
* * * * * cd /path/to/bfacp; git pull >/dev/null 2>&1
```

<a href="https://goo.gl/8BlTk2" target="_blank"><img src="https://raw.githubusercontent.com/ColColonCleaner/AdKats/master/images/AdKats_Docs_Donate.jpg"></a>
