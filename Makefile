init:
	composer install
	cp .env.example .env
	composer dump-autoload
	php artisan optimize:clear
	php artisan key:generate
	@make fresh
serve:
	php artisan serve
fresh:
	php artisan cache:clear
	php artisan migrate:fresh --seed
cache:
	php artisan cache:clear
token:
	php artisan refreshToken