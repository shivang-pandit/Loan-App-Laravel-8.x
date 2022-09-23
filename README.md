<p dir="auto" align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Loan App Rest API
## About Application
A Loan App Apis, including:

* Restful APIs with laravel
* MySql Database 
* User authentication with laravel sanctum
* Fully testing codes(Feature test and Unit test).
* Postman collection of api and documentation.

## Features of the site
* Login with admin and customer (api built in laravel with sanctum).
* Customer apply for loan.
* Admin approved loan.
* Customer can view own applied loan and pay installment on week bases.
* Admin can view all customers and loan.


## Installation

```bash
* composer install
* Rename .env.example to .env and configure it with your database
* Run command - `php artisan migrate`
* Run command - `php artisan db:seed`
* For testing rename .env.example to .env.testing and configure it with your testing database (You can use MySql Or SQLite)
```

## Running the app normally

```bash
# Run default app port
$ php artisan serve

# Run app on specific port
$ php -t public -S localhost:8000

```

## Test

```bash
# Run all test
$ php artisan test

# run specific test
$ php artisan test --filter=LoanControllerTest
```

## Api document and postman collection links
```bash
Postman collection - https://www.getpostman.com/collections/c00c3403e1348199acf4
Document link - https://documenter.getpostman.com/view/4063134/2s7ZLjGptv#8bc12c4b-2828-4f75-b5fd-3189ed61cae2
```

## Admin Login
```bash
Email: admin@gmail.com
Password: adminT@123
```

## Customer Login
```bash
Email: customer@gmail.com
Password: customerT@123
```

## Stay in touch

- Author - [Shivang Pandit](https://www.linkedin.com/in/shivang-pandit)

## License

[Nest](https://docs.nestjs.com/support). is [MIT licensed](LICENSE).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
