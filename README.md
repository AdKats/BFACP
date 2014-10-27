# Brief History

This tool was created originally for [ADKGamers](http://www.adkgamers.com/) exclusively back in late 2012 early 2013, but as the need for a web based admin tool to make it easier to manage bans and players through AdKats and its ever going nature a new tool had to be created to be more dynamic and more informative.

# Overview

The BFAdminCP is a web based admin tool designed to work exclusively with [AdKats](https://github.com/ColColonCleaner/AdKats) (v5.2+) and [XpKillers Chat, GUID, Stats and Mapstats Logger](https://forum.myrcon.com/showthread.php?6698) (v1.0.0.2). The software is built with the [Laravel](http://laravel.com/) PHP framework to speed up development time and make my job a lot easier.

# Requirements

* MySQL Database
* AdKats v5.2+
* XpKillers Chat, GUID, Stats and Mapstats Logger v1.0.0.2+
* PHP 5.4+
* PHP Mcrypt
* PHP PDO

# Features

* User, Role, and Permission system.
* Live Scoreboard with chat.
* Ban Management for AdKats.
* Detailed player information with graph charts.
* Server statistics page for each server showing population history, uptime history with data from UptimeRobot, and Mapstats.
* Metabans feed.
* Report notifications with ability to change alert sounds.
* Chatlog searching where you can search by multiple players and/or keywords and ability to only show from a certain date/time range.
* Message of the Day
* Quick DB Stats overview
* and more!

# Getting Started

Download the latest version from [here](http://www.adkgamers.com/files/file/35-web-admin/) or by downloading the master branch. Once downloaded unzip it to a temporary folder. Next open the `database.php` file located in the `app/config` folder in your favorite text editor. Scroll down to the Database connections section and fill in your database connection information.

Next we need to create a encryption key. This is **IMPORTANT**! The default key provided is just a placeholder string and is insecure. There are a few ways to do this. First if you have PHP installed on your current system you can open up a command prompt and CD to the directory where you have extracted the application and run the following command `php artisan key:generate`. This will create and update the key located in the file `app.php` in the `app/config` folder. If you don't have command line access or PHP installed on the current system, I have provided a page where you can get a random 32 character string. I do **NOT** save these and they are random on each refresh. You can access this page [here](http://api.gamerethos.net/random_key). Once you have your key open up the config file `app.php` and scroll down till you see the Encryption Key section. Replace the `YourSecretKey!!!` with the key that was generated. By default it will look like this.

```PHP

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => 'YourSecretKey!!!',

    'cipher' => MCRYPT_RIJNDAEL_256,

```

Once completed upload the entire application to your webserver. Once uploaded you will need to modify some file and folder permissions. Change the files and folders permissions under `app/storage` recursively to 0777.

Now load up application in your web browser and it will begin the process of creating the tables and migrating data if you used an older version of the BFAdminCP. This process will take a few seconds to run on first load. When it completed you should see the dashboard.

If all goes well it will have been successfully setup. If this is your first install it will have setup the default admin user otherwise you can login with your old user login.

## Default login

**Username**: admin

**Password**: password

You can change the default username and password by clicking on **Site Management** > **Users** > **Admin** > **Edit User**

If you are upgrading from an older version (v1.4.4) please run this query after you have replaced `YourNameHere` with your username.

```SQL

UPDATE `bfadmincp_assigned_roles`
        INNER JOIN
    `bfadmincp_users` ON `bfadmincp_assigned_roles`.`user_id` = `bfadmincp_users`.`id`
SET
    `role_id` = 1
WHERE
    `username` = 'YourNameHere'

```

If you have any questions or need help setting this up please post it [here](http://www.adkgamers.com/forum/265-adk-web-dev-support/).

For suggestions/bugs please create a ticket [here](https://github.com/Prophet731/BFAdminCP/issues).

[FAQ](https://github.com/Prophet731/BFAdminCP/wiki/FAQ)
