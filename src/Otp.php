<?php

/*
 * PHP-OTP (https://github.com/delight-im/PHP-OTP)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace Delight\Otp;

use Delight\Otp\Throwable\InvalidHashFunctionError;
use Delight\Otp\Throwable\InvalidOtpLengthError;
use Delight\Otp\Throwable\InvalidSecretError;
use Delight\Otp\Throwable\InvalidSecretStrengthError;
use Delight\Otp\Throwable\MissingRandomSourceError;

/** Generates time-based one-time password (TOTP) values in accordance with RFC 6238 and RFC 4226 */
final class Otp {

	/** @var int designates a shared secret with 16 human-readable characters and 80 bits (10 bytes) of randomness */
	const SHARED_SECRET_STRENGTH_LOW = 1;
	/** @var int designates a shared secret with 26 human-readable characters and 128 bits (16 bytes) of randomness */
	const SHARED_SECRET_STRENGTH_MODERATE = 2;
	/** @var int designates a shared secret with 32 human-readable characters and 160 bits (20 bytes) of randomness */
	const SHARED_SECRET_STRENGTH_HIGH = 3;
	/** @var int the default interval length or time step size (T_X) in seconds */
	const INTERVAL_LENGTH_DEFAULT = 30;
	/** @var int the default epoch in seconds to start counting time steps from, zero representing the UNIX epoch */
	const EPOCH_DEFAULT = 0;
	/** @var int designates the hash function SHA-1 for usage with HMAC */
	const HASH_FUNCTION_SHA_1 = 1;
	/** @var int designates the hash function SHA-256 for usage with HMAC */
	const HASH_FUNCTION_SHA_256 = 2;
	/** @var int designates the hash function SHA-512 for usage with HMAC */
	const HASH_FUNCTION_SHA_512 = 3;
	/** @var int the default cryptographic hash function to use with HMAC */
	const HASH_FUNCTION_DEFAULT = self::HASH_FUNCTION_SHA_1;
	/** @var int the minimum number of digits (d) in an OTP value */
	const OTP_LENGTH_MIN = 6;
	/** @var int the maximum number of digits (d) in an OTP value */
	const OTP_LENGTH_MAX = 8;
	/** @var int the default number of digits (d) to use in an OTP value */
	const OTP_LENGTH_DEFAULT = 6;

	private function __construct() { }

	/**
	 * Generates a new random secret that is encoded using Base32 for readability and transmission
	 *
	 * @param int|null $strength (optional) the strength of the secret to be generated, as one of the `SHARED_SECRET_STRENGTH_*` constants from this class
	 * @return string
	 */
	public static function createSecret($strength = null) {
		$strength = !empty($strength) ? (int) $strength : self::SHARED_SECRET_STRENGTH_HIGH;
		$numberOfRandomBits = self::determineBitsForSharedSecretStrength($strength);
		$numberOfRandomBytes = (int) \ceil($numberOfRandomBits / 8);

		$randomBytes = null;

		if (\function_exists('random_bytes')) {
			try {
				$randomBytes = \random_bytes($numberOfRandomBytes);
			}
			catch (\Exception $e) {
				$randomBytes = null;
			}
		}
		elseif (\function_exists('openssl_random_pseudo_bytes')) {
			try {
				$randomBytes = \openssl_random_pseudo_bytes($numberOfRandomBytes, $cryptographicallyStrong);
			}
			catch (\Exception $e) {
				$randomBytes = null;
			}

			if ($randomBytes === false || $cryptographicallyStrong === false) {
				$randomBytes = null;
			}
		}

		if (empty($randomBytes)) {
			throw new MissingRandomSourceError();
		}

		return \ParagonIE\ConstantTime\Base32::encodeUpperUnpadded($randomBytes);
	}

	/**
	 * Creates a key URI that can be used to set up an authenticator application on the client side
	 *
	 * The key URI can be encoded as a QR code and then be displayed to the user
	 *
	 * The secret is usually generated on the server side
	 *
	 * @param string $issuer the name of the application or service to authenticate for
	 * @param string $accountName the name of the account which is to be set up with an authenticator application, often the user's email address
	 * @param string $secret the secret key, shared secret or seed, encoded using Base32 for readability and transmission
	 * @return string the key URI that can be encoded as a QR code and then be displayed to the user
	 */
	public static function createTotpKeyUriForQrCode($issuer, $accountName, $secret) {
		return 'otpauth://totp/' . \rawurlencode($issuer) . ':' . \rawurlencode($accountName) . '?secret=' . \rawurlencode($secret) . '&issuer=' . \rawurlencode($issuer);
	}

	/**
	 * Generates a new time-based one-time password (TOTP) value in accordance with RFC 6238 and RFC 4226
	 *
	 * @param string $secret the secret key, shared secret or seed, encoded using Base32 for readability and transmission
	 * @param int|null $t (optional) the current time in seconds since the start of the (configurable) epoch
	 * @param int|null $otpLength (optional) the number of digits (d) to use for OTP values, between 6 (inclusive) and 8 (inclusive)
	 * @param int|null $t_x (optional) the interval length or time step size in seconds (after which OTP values change)
	 * @param int|null $t_0 (optional) the epoch in seconds since the UNIX epoch (to start counting time steps from)
	 * @param int|null $hashFunction (optional) the cryptographic hash function to use with HMAC, as one of the `HASH_FUNCTION_*` constants from this class
	 * @return string a new OTP value for the specified secret, with 6 to 8 characters, as requested
	 */
	public static function generateTotp($secret, $t = null, $otpLength = null, $t_x = null, $t_0 = null, $hashFunction = null) {
		$otpLength = !empty($otpLength) ? (int) $otpLength : self::OTP_LENGTH_DEFAULT;

		if ($otpLength < self::OTP_LENGTH_MIN || $otpLength > self::OTP_LENGTH_MAX) {
			throw new InvalidOtpLengthError();
		}

		$secret = !empty($secret) ? (string) $secret : '';
		$t = isset($t) ? (int) $t : \time();
		$t_x = !empty($t_x) ? (int) $t_x : self::INTERVAL_LENGTH_DEFAULT;
		$t_0 = isset($t_0) ? (int) $t_0 : self::EPOCH_DEFAULT;

		// calculate the number of time steps of size T_X between T_0 and T, and make it our counter C_T
		$c_t = (int) \floor(($t - $t_0) / $t_x);

		// make sure the supplied secret does not contain any characters outside of the Base32 alphabet
		$secret = \preg_replace('/[^A-Za-z2-7]/', '', $secret);
		// turn the secret into an all-uppercase string
		$secret = \strtoupper($secret);

		// if the (Base32-encoded) secret does not meet the minimum requirements (represented by `SHARED_SECRET_STRENGTH_LOW`)
		if (\strlen($secret) < 16) {
			throw new InvalidSecretError();
		}

		// decode the secret to a binary string from Base32 (according to RFC 4648 and RFC 3548, without padding)
		$k = \ParagonIE\ConstantTime\Base32::decodeUpper($secret, false);

		// pad the counter into a 64-bit big-endian integer represented as a binary string

		$counter64BitBigEndian = @\pack('J', $c_t);

		if (empty($counter64BitBigEndian)) {
			$counter64BitBigEndian = \chr(0) . \chr(0) . \chr(0) . \chr(0) . \pack('N', $c_t);
		}

		$hashFunction = !empty($hashFunction) ? (int) $hashFunction : self::HASH_FUNCTION_DEFAULT;

		switch ($hashFunction) {
			case self::HASH_FUNCTION_SHA_1: $hashFunctionNameForHmac = 'sha1'; break;
			case self::HASH_FUNCTION_SHA_256: $hashFunctionNameForHmac = 'sha256'; break;
			case self::HASH_FUNCTION_SHA_512: $hashFunctionNameForHmac = 'sha512'; break;
			default: throw new InvalidHashFunctionError();
		}

		// calculate HMAC(K, C_T) of the counter C_T (as the message) using the secret K (both as binary strings)
		$mac = \hash_hmac($hashFunctionNameForHmac, $counter64BitBigEndian, $k, true);
		// take the last 4 bits of the MAC to use them as a byte offset (i) afterwards
		$offset = \ord(\substr($mac, -1)) & 0x0f;
		// use the last 4 bits obtained before as a pointer and from that offset take 4 bytes from the MAC (for the 31 bits that we will ultimately need)
		$macSubstring4Bytes = \substr($mac, $offset, 4);

		// interpret the bytes found at the offset as a 32-bit integer
		$words32Bit = \unpack('N', $macSubstring4Bytes);
		$integer32Bit = \array_shift($words32Bit);

		// mask the sign bit of the integer, i.e. the most significant bit, to avoid modular arithmetic on negative numbers
		$hotp = $integer32Bit & 0x7fffffff;

		// reduce the OTP value to the requested number of (decimal) digits
		$otpValue = $hotp % \pow(10, $otpLength);

		return \str_pad($otpValue, $otpLength, '0', \STR_PAD_LEFT);
	}

	/**
	 * Verifies the provided time-based one-time password (TOTP) value in accordance with RFC 6238 and RFC 4226
	 *
	 * @param string $secret the secret key, shared secret or seed, encoded using Base32 for readability and transmission
	 * @param string $otpValue the actual OTP value provided for verification, usually entered by the user in some way
	 * @param int|null $lookBehindSteps (optional) the number of time steps to look behind to account for slight clock skews, network latency and user delays
	 * @param int|null $lookAheadSteps (optional) the number of time steps to look ahead to account for slight clock skews
	 * @param int|null $t (optional) the current time in seconds since the start of the (configurable) epoch
	 * @param int|null $otpLength (optional) the number of digits (d) to use for OTP values, between 6 (inclusive) and 8 (inclusive)
	 * @param int|null $t_x (optional) the interval length or time step size in seconds (after which OTP values change)
	 * @param int|null $t_0 (optional) the epoch in seconds since the UNIX epoch (to start counting time steps from)
	 * @param int|null $hashFunction (optional) the cryptographic hash function to use with HMAC, as one of the `HASH_FUNCTION_*` constants from this class
	 * @return bool whether the provided OTP value is valid for the supplied secret, currently or at the specified time
	 */
	public static function verifyTotp($secret, $otpValue, $lookBehindSteps = null, $lookAheadSteps = null, $t = null, $otpLength = null, $t_x = null, $t_0 = null, $hashFunction = null) {
		$otpValue = !empty($otpValue) ? (string) $otpValue : '';
		$lookBehindSteps = isset($lookBehindSteps) ? \abs((int) $lookBehindSteps) : 2;
		$lookAheadSteps = isset($lookAheadSteps) ? \abs((int) $lookAheadSteps) : 2;
		$t = isset($t) ? (int) $t : \time();
		$otpLength = !empty($otpLength) ? (int) $otpLength : self::OTP_LENGTH_DEFAULT;
		$t_x = !empty($t_x) ? (int) $t_x : self::INTERVAL_LENGTH_DEFAULT;

		// make sure the supplied OTP value does not contain any characters other than decimal digits
		$otpValue = \preg_replace('/[^0-9]/', '', $otpValue);

		if (\strlen($otpValue) < self::OTP_LENGTH_MIN || \strlen($otpValue) > self::OTP_LENGTH_MAX) {
			return false;
		}

		// if the length of the actual OTP value is not the same as the expected length of OTP values
		if (\strlen($otpValue) !== $otpLength) {
			return false;
		}

		for ($s = -$lookBehindSteps; $s <= $lookAheadSteps; $s++) {
			$expectedOtpValue = self::generateTotp($secret, $t + $t_x * $s, $otpLength, $t_x, $t_0, $hashFunction);

			if (\hash_equals($expectedOtpValue, $otpValue)) {
				return true;
			}
		}

		return false;
	}

	private static function determineBitsForSharedSecretStrength($strength) {
		switch ($strength) {
			case 1: return 80;
			case 2: return 128;
			case 3: return 160;
			default: throw new InvalidSecretStrengthError();
		}
	}

}
