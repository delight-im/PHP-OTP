<?php

/*
 * PHP-OTP (https://github.com/delight-im/PHP-OTP)
 * Copyright (c) delight.im (https://www.delight.im/)
 * Licensed under the MIT License (https://opensource.org/licenses/MIT)
 */

namespace Delight\Otp\Throwable;

/** Error that is thrown when no secure source of randomness could be found */
class MissingRandomSourceError extends Error {}
