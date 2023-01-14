init:
	composer install
	php -r "file_exists('.env') || copy('.env.example', '.env');"
	composer dump-autoload
	php artisan optimize:clear
	php artisan key:generate
	php -r "touch('database/database.sqlite');"
	@make fresh
serve:
	php artisan serve
fresh:
	php artisan cache:clear
	php artisan config:cache
	php artisan migrate:fresh --seed --force
cache:
	php artisan config:cache
token:
	php artisan refreshToken