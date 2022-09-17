# DIGITAL WALLET SOAP

_Laravel *.x project._

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Requirements

This is a Laravel 9.x project, so you must meet its requirements.

### Installing

Clone the project

```bash
git clone git@github.com:stevensgsp/digital-wallet-soap.git
cd digital-wallet-soap
composer install
cp .env.example .env
php artisan key:generate
```

Edit .env and put credentials, indicate environment, url and other settings.

Run migrations

```bash
php artisan migrate
```
