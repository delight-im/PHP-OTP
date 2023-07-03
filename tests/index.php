<?php

/*
 * PHP-OTP (https://github.com/delight-im/PHP-OTP)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

/*
 * WARNING:
 *
 * Do *not* use these files from the `tests` directory as the foundation
 * for the usage of this library in your own code. Instead, please follow
 * the `README.md` file in the root directory of this project.
 */

// enable error reporting
\error_reporting(\E_ALL);
\ini_set('display_errors', 'stdout');

\header('Content-Type: text/plain; charset=utf-8');

require __DIR__ . '/../vendor/autoload.php';

function fail($lineNumber) {
	exit('Error on line ' . $lineNumber);
}

$mockTime = \mktime(0, 0, 0, 6, 8, 1995);

(\preg_match('/^[A-Z2-7]{32}$/', \Delight\Otp\Otp::createSecret()) === 1) or \fail(__LINE__);
(\preg_match('/^[A-Z2-7]{16}$/', \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_LOW)) === 1) or \fail(__LINE__);
(\preg_match('/^[A-Z2-7]{26}$/', \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_MODERATE)) === 1) or \fail(__LINE__);
(\preg_match('/^[A-Z2-7]{32}$/', \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_HIGH)) === 1) or \fail(__LINE__);

(\Delight\Otp\Otp::createSecret() !== \Delight\Otp\Otp::createSecret()) or \fail(__LINE__);
(\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_LOW) !== \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_LOW)) or \fail(__LINE__);
(\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_MODERATE) !== \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_MODERATE)) or \fail(__LINE__);
(\Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_HIGH) !== \Delight\Otp\Otp::createSecret(\Delight\Otp\Otp::SHARED_SECRET_STRENGTH_HIGH)) or \fail(__LINE__);

(\Delight\Otp\Otp::generateTotp(' EJDV-6nto-ioob-ZE4C ', $mockTime) === '494086') or \fail(__LINE__);
(\Delight\Otp\Otp::generateTotp(' ITEJ-7GZZ-uuu4-fefs-i2sv-lb3w-LU5O-2SXP ', $mockTime) === '140726') or \fail(__LINE__);

(\Delight\Otp\Otp::generateTotp(' EJDV-6nto-ioob-ZE4C ', $mockTime) === \Delight\Otp\Otp::generateTotp(' EJDV-6nto-ioob-ZE4C ', $mockTime)) or \fail(__LINE__);
(\Delight\Otp\Otp::generateTotp(' EJDV-6nto-ioob-ZE4C ') === \Delight\Otp\Otp::generateTotp(' EJDV-6nto-ioob-ZE4C ')) or \fail(__LINE__);
(\Delight\Otp\Otp::generateTotp(' ITEJ-7GZZ-uuu4-fefs-i2sv-lb3w-LU5O-2SXP ', $mockTime) === \Delight\Otp\Otp::generateTotp(' ITEJ-7GZZ-uuu4-fefs-i2sv-lb3w-LU5O-2SXP ', $mockTime)) or \fail(__LINE__);
(\Delight\Otp\Otp::generateTotp(' ITEJ-7GZZ-uuu4-fefs-i2sv-lb3w-LU5O-2SXP ') === \Delight\Otp\Otp::generateTotp(' ITEJ-7GZZ-uuu4-fefs-i2sv-lb3w-LU5O-2SXP ')) or \fail(__LINE__);

$rfc6238TestKeySha1 = \ParagonIE\ConstantTime\Base32::encodeUpperUnpadded('12345678901234567890');
$rfc6238TestKeySha256 = \ParagonIE\ConstantTime\Base32::encodeUpperUnpadded('12345678901234567890123456789012');
$rfc6238TestKeySha512 = \ParagonIE\ConstantTime\Base32::encodeUpperUnpadded('1234567890123456789012345678901234567890123456789012345678901234');

(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C')) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time()), 0, 0) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() - 30), 0, 0) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() - 30), 0, 1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() - 30), 1, 0) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() + 30), 0, 0) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() + 30), 1, 0) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp('EJDV-6NTO-IOOB-ZE4C', \Delight\Otp\Otp::generateTotp('EJDV-6NTO-IOOB-ZE4C', \time() + 30), 0, 1) === true) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 3, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 2, 2, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 0, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 3, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '94287082', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 3, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 2, 2, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 0, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 3, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '46119246', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 3, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 2, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 2, 2, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 0, \strtotime('1970-01-01T00:00:59Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 0, \strtotime('1970-01-01T00:00:59Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 3, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '90693936', 0, 2, \strtotime('1970-01-01T00:00:59Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 3, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 2, 2, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 0, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 3, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '07081804', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 3, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 2, 2, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 0, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 3, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '68084774', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 3, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 2, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 2, 2, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 0, \strtotime('2005-03-18T01:58:29Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 0, \strtotime('2005-03-18T01:58:29Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 3, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '25091201', 0, 2, \strtotime('2005-03-18T01:58:29Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 3, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 2, 2, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 0, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 3, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '14050471', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 3, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 2, 2, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 0, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 3, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '67062674', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 3, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 2, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 2, 2, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 0, \strtotime('2005-03-18T01:58:31Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 0, \strtotime('2005-03-18T01:58:31Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 3, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '99943326', 0, 2, \strtotime('2005-03-18T01:58:31Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 3, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 2, 2, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 0, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 3, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '89005924', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 3, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 2, 2, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 0, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 3, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '91819424', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 3, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 2, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 2, 2, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 0, \strtotime('2009-02-13T23:31:30Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 0, \strtotime('2009-02-13T23:31:30Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 3, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '93441116', 0, 2, \strtotime('2009-02-13T23:31:30Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 3, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 2, 2, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 0, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 3, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '69279037', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 3, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 2, 2, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 0, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 3, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '90698825', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 3, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 2, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 2, 2, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 0, \strtotime('2033-05-18T03:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 0, \strtotime('2033-05-18T03:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 3, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '38618901', 0, 2, \strtotime('2033-05-18T03:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 3, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 2, 2, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 0, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 3, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha1, '65353130', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_1) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 3, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 2, 2, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 0, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 3, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha256, '77737706', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_256) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 3, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 2, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 2, 2, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 0, \strtotime('2603-10-11T11:33:20Z') + 0, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 0, \strtotime('2603-10-11T11:33:20Z') - 60, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 3, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === true) or \fail(__LINE__);
(\Delight\Otp\Otp::verifyTotp($rfc6238TestKeySha512, '47863826', 0, 2, \strtotime('2603-10-11T11:33:20Z') - 90, 8, 30, 0, \Delight\Otp\Otp::HASH_FUNCTION_SHA_512) === false) or \fail(__LINE__);

(\Delight\Otp\Otp::createTotpKeyUriForQrCode('Example Inc.', 'john.doe@example.org', 'EXKHF66HZ2IQEBWB3M6T2MZGE2SXHG3F') === 'otpauth://totp/Example%20Inc.:john.doe%40example.org?secret=EXKHF66HZ2IQEBWB3M6T2MZGE2SXHG3F&issuer=Example%20Inc.') or \fail(__LINE__);
(\Delight\Otp\Otp::createTotpKeyUriForQrCode('example.com', 'jane.doe@example.org', 'PGW4AWJFOQXEVTFRWMAQM3ZSPKXBJKDV') === 'otpauth://totp/example.com:jane.doe%40example.org?secret=PGW4AWJFOQXEVTFRWMAQM3ZSPKXBJKDV&issuer=example.com') or \fail(__LINE__);

echo 'ALL TESTS PASSED' . "\n";
