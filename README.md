# Laravel Przelewy24 module

This module makes integration with [przelewy24.pl](http://przelewy24.pl) payment system easier. It supports making payments using przelewy24.pl system.
 
## New version 3.0 released

Package supports recently version Przelewy24 REST API.

The old Payment Provider Api version will expire before end of year 2021.

__We recommend immediately upgrading the package version.__
 
### Installation

1. Run

   ```php   
   composer require devpark/laravel-przelewy24
   ``` 
   
   in console to install this module
   
2. Open `config/app.php` and add

   ###### Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider. You can go to 3.
    
   ```php
   Devpark\Transfers24\Providers\Transfers24ServiceProvider::class
   ```
        
   in section `providers`
          
3. Run

    ```php
    php artisan vendor:publish --provider="Devpark\Transfers24\Providers\Transfers24ServiceProvider"
    ```
    
    in your console to publish default configuration files `transfers24.php` in `config` directory

    __Add  flag `force` to alter configuration after upgrade package__

    These are all configuration settings. Some settings can be changed via environment settings, see below.
        
4. Open `.env` and add your configuration:

 * `PRZELEWY24_MERCHANT_ID` -a Company or an Individual number, who has signed a contract with Przelewy24 (Merchant ID), 
 * `PRZELEWY24_POS_ID` - the identification number of the shop (default: Merchant ID)
 * `PRZELEWY24_CRC` -a  random  string,  used  to  calculate  a  CRC  value,  shown  in Przelewy24 Admin panel.
 * `PRZELEWY24_REPORT_KEY` -a  report key,  used  to  calculate  a  Report Key  value,  shown  in Przelewy24 Admin panel.
   
 * `PRZELEWY24_TEST_SERVER` - if true, set the test environment
 * `PRZELEWY24_URL_RETURN` - Return address, where Client will be redirected to, after the transaction is completed (default 'transfers24/callback').
 * `PRZELEWY24_URL_STATUS` - (POST route) address where the status of a transaction is sent. It can be omitted if stored in P24 system (default 'transfers24/status').
 
### Usage

In order to use the system, you need to do a few things:

1. You need to launch the registration request in order to init payment

2. You need to handle customer returning routes to your app. By default there are routes 'transfers24/callback'

3. You should ensure transaction verify. Here you should send verify request of payment after receiving notification about correct transaction from payment system. You need to handle returning routes to status  of  the  transaction which is  sent automatically from payment system. By default there are routes 'transfers24/status'

#### Registration request

This is main request you need to launch to init payment. 
        
The most basic sample code for authorization request could look like this:

```php      
$payment = app()->make(\App\Payment::class);
$registration_request = app()->make(\Devpark\Transfers24\Requests\Transfers24::class);

$register_payment = $registration_request->setEmail('test@example.com')->setAmount(100)->init();

if($register_payment->isSuccess())
{
    // save registration parameters in payment object
    
    return $registration_request->execute($register_payment->getToken(), true);
}
```            
This code should be run in controller as it's returning response which will takes few things.

1. Status registration payment

2. Token, if registration done with success

3. Error code return from payment system

4. Error Message return from payment system

5. Request parameters send to payment system


For `setAmount` default currency is PLN. If you want to use other currency, you should use currency constant from `\Devpark\Transfers24\Currency` class as 2nd parameter. Also please notice that amount you should give to this function is real amount (with decimal places) and not converted already to Przelewy24 format.
  
For `\Devpark\Transfers24\Requests\Transfers24::execute` method 2nd parameter decides of redirection to payment system when true or return url for making payment when false

#### Define customer returning routes

You should create routes that will redirect customer after the completed transaction (those routes will be launched using `GET` HTTP method), 

#### Handling transaction verify route

To make sure the payment was really successful you should use `\Devpark\Transfers24\Requests\Transfers24::receive` method. The simplest code could look like this:

```php
$payment_verify = app()->make(\Devpark\Transfers24\Requests\Transfers24::class);
$payment_response = $payment_verify->receive($request);

if ($payment_response->isSuccess()) {
    $payment = Payment::where('session_id',$payment_response->getSessionId())->firstOrFail();
   // process order here after making sure it was real payment
}
echo "OK";
```

This code should be run in controller, because you should return non-empty response when receiving valid przelewy24 request for transaction verify. As you see, you should make sure it was real payment before you process the order and then you need to make sure that it was successful. You can identify payment via session_id (unique ID generate during registration of payment)).
 
### Licence

This package is licenced under the [MIT license](http://opensource.org/licenses/MIT)
