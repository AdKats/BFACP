# Running on PHP Version 7
This does not work with any version of the BFACP do not try to run this application on that version. Use the latest stable PHP 5.6.

# WARNING
DO NOT USE THIS BRANCH! This is still a work in progress and bound to be bugs. If you still wish to run this version, scroll down to the bottom and follow the instructions. You must have access to the command line and have git/composer installed on your system. 

[![Circle CI](https://circleci.com/gh/Prophet731/BFAdminCP.svg?style=svg)](https://circleci.com/gh/Prophet731/BFAdminCP) [![Download Battlefield Admin Control Panel](https://img.shields.io/sourceforge/dt/bfacp.svg)](https://sourceforge.net/projects/bfacp/files/latest/download)[![Latest Stable Version](https://poser.pugx.org/adkgamers/bfadmincp/v/stable)](https://packagist.org/packages/adkgamers/bfadmincp) [![Total Downloads](https://poser.pugx.org/adkgamers/bfadmincp/downloads)](https://packagist.org/packages/adkgamers/bfadmincp) [![Latest Unstable Version](https://poser.pugx.org/adkgamers/bfadmincp/v/unstable)](https://packagist.org/packages/adkgamers/bfadmincp) [![License](https://poser.pugx.org/adkgamers/bfadmincp/license)](https://packagist.org/packages/adkgamers/bfadmincp)

# Overview
The Battlefield Admin Control Panel (BFACP) is a web based admin tool designed to work exclusively with [AdKats](https://github.com/AdKats/AdKats) (v6+) and [XpKillers Chat, GUID, Stats and Mapstats Logger](https://forum.myrcon.com/showthread.php?6698) (v1.0.0.2). The software is built with the [Laravel](http://laravel.com/) PHP framework to speed up development time and make my job a lot easier.

[FAQ](https://github.com/Prophet731/BFAdminCP/wiki/FAQ)

# Requirements

* MySQL Database (5.6+) or MariaDB 5.5 Series or MariaDB 10.x Series
* AdKats v6+
* XpKillers Chat, GUID, Stats and Mapstats Logger v1.0.0.2+
* PHP 5.5.9+
* PHP Mcrypt
* PHP PDO

# Features

* User, Role, and Permission system.
* Live Scoreboard with chat.
* Ban Management for AdKats.
* Detailed player information with graph charts.
* Server statistics page for each server showing population history, uptime history with data from UptimeRobot, and Mapstats.
* Metabans support.
* Report notifications.
* Chatlog searching where you can search by multiple players and/or keywords and ability to only show from a certain date/time range.
* Message of the Day
* Quick DB Stats overview
* and more!


[![Download Battlefield Admin Control Panel](https://a.fsdn.com/con/app/sf-download-button)](https://sourceforge.net/projects/bfacp/files/latest/download)

Download the latest version. Once downloaded unzip it to a temporary folder on your computer. Next open the `.env.example` file located in the root folder in your favorite text editor. Scroll down to the database settings section and fill in your database connection information.

```
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```

Next we need to create a encryption key. This is **IMPORTANT**! The default key provided is just a placeholder string and is insecure. I have provided a page where you can get a random 32 character string. I do **NOT** save these and they are random on each refresh. You can access this page [here](https://adkats.gamerethos.net/api/random_key). Now go back to the `.env.example` file and replace the `SomeRandomString` with the key that was generated. By default it will look like this.

```
APP_KEY=SomeRandomString
```

## Pusher Configuration (Optional)

If you would like to have real-time online users & site chat, you will to create an account over at [Pusher](https://pusher.com/signup). Once you have your account and are logged in click on the "Your apps" tab on the left and click the button that says "Create new app". Now for the app name you can name it whatever you want, I just used my communities name. For the cluster selection choose the one that's the closest to your web server, if you know it, otherwise the default they provide is fine. After that click "Create my app" button.

You should now see your newly created app. Now click on the "App keys" tab. Copy the credentials to the approate field in the `.env.example` file.

```
PUSHER_APP_ID=null
PUSHER_KEY=null
PUSHER_SECRET=null
```

![Image of Pusher App Creation](https://i.gyazo.com/e803be53c8e1f7029cf9683a0f3aaaad.png)

Once completed, save the file in the same location as `.env.example` and name it `.env`, then upload the entire application to your webserver. Once uploaded you will need to modify some file and folder permissions. Change the files and folders permissions under `storage` recursively to 0777. This includes the storage folder itself. Also do this for the `builds` folder located at `public/js/builds`

## Note
This application was designed to run on a subdomain and not from a folder from the TLD. Make sure to create a subdomain and if possible have domain point to the `public` folder that's located under the root folder. Not a redirect.

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

This product includes GeoLite2 data created by MaxMind, available from
<a href="http://www.maxmind.com">http://www.maxmind.com</a>.

### Installing Dev Version

I will assume you already have a webspace setup for this. If not just follow directions above to get the basics setup then come back here. All you should need to do is follow the commands and you should be up and running.

Make sure the directory is empty.
```bash
cd /path/to/bfacp
git clone https://github.com/Prophet731/BFAdminCP.git .
git checkout dev-laravel5
composer install --no-scripts
chmod -R 0777 storage
chmod -R 0777 public/js/builds
cp .env.example .env
```

After you ran those command edit the file `.env` with the necessary information then save the file. Next enter these commands.

```bash
php artisan migrate --force
php artisan db:seed --force
```

If you were running a previous version then you will need to run this command as well.
```bash
php artisan bfacp:reseed
```

You should be good to go now. Make sure that you update the subdomain to point to the `public` folder for its directory (not a redirect). If you wish to update to the latest version of this branch just do a `git pull`.
