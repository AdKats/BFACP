# Overview
The Battlefield Admin Control Panel (BFACP) is a web based admin tool designed to work exclusively with [AdKats](https://github.com/AdKats/AdKats) (v6+) and [XpKillers Chat, GUID, Stats and Mapstats Logger](https://forum.myrcon.com/showthread.php?6698) (v1.0.0.2). The software is built with the [Laravel](http://laravel.com/) PHP framework to speed up development time and make my job a lot easier.

# Requirements

* MySQL Database (5.6+)
* AdKats v6+
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
* Metabans support.
* Report notifications with ability to change alert sounds.
* Chatlog searching where you can search by multiple players and/or keywords and ability to only show from a certain date/time range.
* Message of the Day
* Quick DB Stats overview
* and more!

Download the latest version from [here](https://github.com/Prophet731/BFAdminCP/releases/latest). Once downloaded unzip it to a temporary folder. Next open the `.env.php` file located in the root folder in your favorite text editor. Scroll down to the database settings section and fill in your database connection information.

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

Once completed upload the entire application to your webserver. Once uploaded you will need to modify some file and folder permissions. Change the files and folders permissions under `app/storage` recursively to 0777. This application was designed to run on a subdomain and not from a folder from from TLD. Make sure to create a subdomain and if possable have domain point to the public folder that's located under the root folder.

Now load up application in your web browser and it will begin the process of creating the tables. This process will take a few seconds to run on first load. When it completed you should see the dashboard.

## Default login

**Username**: admin

**Password**: password

You can change the default username and password by clicking on **Site Management** > **Users** > **Admin**

If you have any questions or need help setting this up please post it [here](http://www.adkgamers.com/forum/265-adk-web-dev-support/).

Please create a ticket for bugs/requests. [here](https://github.com/Prophet731/BFAdminCP/issues).

[FAQ](https://github.com/Prophet731/BFAdminCP/wiki/FAQ)
