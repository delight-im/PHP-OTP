# PHP-OTP

One-time password (OTP) implementation for two-factor authentication with TOTP in accordance with RFC 6238 and RFC 4226

## Requirements

 * PHP 5.6.0+
   * OpenSSL extension (`openssl`)

## Installation

 1. Include the library via Composer [[?]](https://github.com/delight-im/Knowledge/blob/master/Composer%20(PHP).md):

    ```bash
    $ composer require delight-im/otp
    ```

 1. Include the Composer autoloader:

    ```php
    require __DIR__ . '/vendor/autoload.php';
    ```

## Usage

### Creating a new secret key, shared secret or seed

```php
\Delight\Otp\Otp::createSecret();
// string(32) "WQ2S54TEQYY4Z2PWHB2Y6W443ZCEKJCQ"


// or


\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_HIGH);
// string(32) "X4YSNUYTL6NE2PF7PMFN4QDUD3WPUR75" (160 bits or 20 bytes)

// or

\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_MODERATE);
// string(26) "7OX7TNZAKKXFAWH2P4RB4VR2DE" (128 bits or 16 bytes)

// or

\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_LOW);
// string(16) "3KXNADOF5GTCCTKZ" (80 bits or 10 bytes)
```

After creating the new secret, you need to store the string (in your database) and associate it with the user that it was generated for. The secret must be unique per user, i.e. newly generated and not re-used between users. Next, the secret must be presented to the user once to transfer it to the client application that will be used to generate one-time passwords, i.e. to their authenticator application. Always use a secure channel, e.g. HTTPS or TLS, to share the secret between server and client.

### Presenting the secret to set up an authenticator application

Use the stored secret along with the name of your service or application and the user’s account name to let the user set up an authenticator application on the client side:

```php
\Delight\Otp\Otp::createTotpKeyUriForQrCode('app.example.com', 'john.doe@example.org', $storedSecret);
// string(116) "otpauth://totp/app.example.com:john.doe%40example.org?secret=T7...4D&issuer=app.example.com"
```

Now you can encode the key URI as a QR code, preferably on the client side, and ask the user to scan it using their authenticator application.

Additionally, you should allow the user to view the stored secret (which is a string in Base32 encoding) once during setup and allow them to enter it in their authenticator application manually, in case they are not able to scan a QR code with their authenticator application.

Both forms of transferring the seed from the server side to the client side must only happen *once*, during setup.

Now that server and client have a shared secret, that secret can be used to generate one-time passwords safely.

But before completing the setup of two-factor authentification and enabling the use of one-time passwords for the user, you should require successful verification of one initial one-time password as shown below, to make sure the user completed the setup correctly.

If you want any custom configuration for the user’s one-time passwords, such as a different length of one-time passwords (e.g. 8 characters instead of 6) or a non-standard interval after which one-time passwords refresh, this point during setup is the time to set this up. The custom configuration can be provided to the user’s authenticator app via additional parameters in the key URI, but unfortunately, these are not all supported by every authenticator application. So make sure the configuration that the server expects, and stores for the user, is consistent with what the user’s client-side authenticator application is going to use.

**Important:** You should generate and store a few random codes, consisting of at least 8 characters, e.g. using [PHP-Random](https://github.com/delight-im/PHP-Random), to be used as backup codes. Present these to the user once during setup.

### Verifying a one-time password

Just provide the one-time password that the user entered, along with the shared secret stored during setup, to verify whether the one-time password entered by the user (which they got from their authenticator application) is valid for the secret stored on the server side:

```php
\Delight\Otp\Otp::verifyTotp($storedSecret, '390108');
// bool(true)
```

To account for slight clock skews, network latency and user delays during input, a few older and newer one-time passwords are accepted, providing a good balance of security and usability.

**Important:** You must prevent brute-force attacks using throttling on the server side. The user must not be allowed more than a few attempts to enter a one-time passwords for a given period.

**Important:** When a one-time password has been verified as correct, you need to prevent replay attacks using a denylist, e.g. by tracking successfully used one-time passwords per user in your database and preventing them from being used again. The one-time passwords tracked in the denylist should expire after a few minutes. That expiry time must be adjusted when you use custom validity periods or refresh intervals for your one-time passwords, or when you allow for more past or future one-time passwords to be accepted.

If you provided any custom configuration to the user’s client-side authenticator application during setup earlier, you must use the same configuration now when verifying one-time passwords received from the user:

```php
$lookBehindSteps = 3;
$lookAheadSteps = 3;
$currentTime = \time();
$otpLength = 8;
$refreshInterval = 60;
$epoch = 0;
$hashFunction = \Delight\Otp\Otp::HASH_FUNCTION_SHA_512;

\Delight\Otp\Otp::verifyTotp(
    $storedSecret,
    '38618901',
    $lookBehindSteps,
    $lookAheadSteps,
    $currentTime,
    $otpLength,
    $refreshInterval,
    $epoch,
    $hashFunction
);
// bool(true)
```

## Contributing

All contributions are welcome! If you wish to contribute, please create an issue first so that your feature, problem or question can be discussed.

## License

This project is licensed under the terms of the [MIT License](https://opensource.org/licenses/MIT).
