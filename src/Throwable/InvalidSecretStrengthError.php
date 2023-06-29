<?php

/*
 * PHP-OTP (https://github.com/delight-im/PHP-OTP)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace Delight\Otp\Throwable;

/** Error that is thrown when an invalid strength has been specified for the secret key, shared secret or seed */
class InvalidSecretStrengthError extends Error {}
