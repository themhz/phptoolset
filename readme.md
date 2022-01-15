PHP toolset  
Basic usage to backup the mysql database from your local machine and upload it to a remote ftp  
Contains some primitive handlers like database, ftp and local file management.


Commands you can use from the command line 

1. php index.php database export  //To export all the database dump  
2. php index.php database drop //To Drop all the schemas from the database  
3. php index.php database restore  //Restore all the tables to the database  
4. php index.php database upload  //Upload yous backups to the database  
5. php index.php database deleteremote  //delete all the remote files  
6. php index.php database deletelocal  //delete all the local files  
7. php index.php database backup  //run all the basic flow  



Command you can use to backup xamp
1. php index.php xamp backup