
#AD-TEK
##Windows users:

1. Download XAMPP: https://www.apachefriends.org/download.html
2. Download and extract cmder mini: https://github.com/cmderdev/cmder/releases/download/v1.1.4.1/cmder_mini.zip
3. Update windows environment variable path to point to your php install folder (inside XAMPP installation dir) (here is how you can do this http://stackoverflow.com/questions/17727436/how-to-properly-set-php-environment-variable-to-run-commands-in-git-bash)
cmder will be refered as console

##Mac Os, Ubuntu and windows users continue here:

1. Create a database locally named homestead utf8_general_ci
2. Download composer https://getcomposer.org/download/
3. Pull Laravel/php project from git provider.
4. Rename .env.example file to .envinside your project root and fill the database information. (windows wont let you do it, so you have to open your console cd your project root directory and run mv .env.example .env )
5. Open the console and cd your project root directory
6. Run composer install or php composer.phar install
7. Run php artisan key:generate
8. Run php artisan migrate
9. Run php artisan db:seed to run seeders, if any.
10. Run php artisan serve
#####You can now access your project at localhost:8000 :)
If for some reason your project stop working do these:

1. composer install
2. php artisan migrate
#BRANCH DEV