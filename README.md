# Slim + PHP 7 Sample Application

This is a sample application built with the Slim framework and PHP 7. 

After a user registers or logs in, a 30 second preview of a song will be played. 
Users can either like or dislike songs. Songs that have been liked by a user will be displayed
on a table in the Music tab.

# Installation:
Dependencies can be installed through Composer with `composer install`

The app config is located at `src/config/environment/main.yaml`. Adjust as necessary.

#Development Environment:
A `Vagrantfile` and `scripts` directory are provided to enable a development environment. To create 
an environment:

```
$ vagrant up
```

After vagrant is set up, login to the virtual machine:
```
$ ssh 10.0.60.12 -l vagrant
```

Once prompted, enter the password `vagrant` and then move to the directory `/vagrant/scripts`.

From `scripts`, connect to MySQL to create a new database:
```
$ mysql
mysql> CREATE DATABASE test;
mysql> USE test;
mysql> source schema.sql;
mysql> exit
```


To reset the environment including the database:

```
$ vagrant provision
```

The url for this environment is `test1.loc`