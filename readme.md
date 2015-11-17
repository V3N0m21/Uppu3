Uppu3
=====
To deploy my filesharing app "Uppu3" on your local computer you have to go throrugh the following steps:

1. Clone git repository into the directory of your choosing.

2. In your bash go to the root directory of the project and run "composer install".

3. After all required libraries and dependencies are installed type in the bash following commands:

* mysql -u username -p -h localhost your-database-name < files.sql;
* mysql -u username -p -h localhost your-database-name < users.sql;
* mysql -u username -p -h localhost your-database-name < comments.sql;

to set up required tables in your database.

Server requirements:

Your apache2 server has to allow usage of .htaccess files and also it has to have x-sendfile module to be installed.
Also you have to add following lines into site's *.conf file to make the project work correctly:

XSendFile On

XSendFilePath /path/to/your/project/Uppu3/public/upload/