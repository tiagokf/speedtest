<?php

define('ID_OBFUSCATION_SALT_FILE', __DIR__.'/idObfuscation_salt.php');

/**
 * @return string|int
 */
function getObfuscationSalt()
{
    if (!file_exists(ID_OBFUSCATION_SALT_FILE)) {
        $bytes = openssl_random_pseudo_bytes(4);

        $saltData = "<?php\n\n\$OBFUSCATION_SALT = 0x".bin2hex($bytes).";\n";
        file_put_contents(ID_OBFUSCATION_SALT_FILE, $saltData);
    }

    if (
        file_exists(ID_OBFUSCATION_SALT_FILE)
        && is_readable(ID_OBFUSCATION_SALT_FILE)
    ) {
        require ID_OBFUSCATION_SALT_FILE;
    }

    return isset($OBFUSCATION_SALT) ? $OBFUSCATION_SALT : 0;
}

/**
 * This is a simple reversible hash function I made for encoding and decoding test IDs.
 * It is not cryptographically secure, don't use it to hash passwords or something!
 *
 * @param int|string $id
 * @param bool $dec
 *
 * @return int|string
 */
function obfdeobf($id, $dec)
{
    $salt = getObfuscationSalt() & 0xFFFFFFFF;
    $id &= 0xFFFFFFFF;
    if ($dec) {
        $id ^= $salt;
        $id = (($id & 0xAAAAAAAA) >> 1) | ($id & 0x55555555) << 1;
        $id = (($id & 0x0000FFFF) << 16) | (($id & 0xFFFF0000) >> 16);

        return $id;
    }

    $id = (($id & 0x0000FFFF) << 16) | (($id & 0xFFFF0000) >> 16);
    $id = (($id & 0xAAAAAAAA) >> 1) | ($id & 0x55555555) << 1;

    return $id ^ $salt;
}

/**
 * @param int $id
 *
 * @return string
 */
function obfuscateId($id)
{
    // Gera um ID com 5-8 caracteres aleatórios
    $obfuscated = obfdeobf($id + 1, false);
    $base36 = base_convert($obfuscated, 10, 36);
    
    // Garante pelo menos 5 caracteres
    $minLength = 5;
    if (strlen($base36) < $minLength) {
        $base36 = str_pad($base36, $minLength, '0', STR_PAD_LEFT);
    }
    
    return strtoupper($base36);
}

/**
 * @param string $id
 *
 * @return int
 */
function deobfuscateId($id)
{
    return obfdeobf(base_convert(strtolower($id), 36, 10), true) - 1;
}
