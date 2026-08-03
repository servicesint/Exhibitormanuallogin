<?php

if (!function_exists('encryptData')) {
    function encryptData($data)
    {
        try {
            if (empty($data)) {
                return false;
            }
            $masterKey = getenv('encryption.key');
            if (!$masterKey) {
                log_message('error', 'Data encryption failed: encryption.key not configured');
                return false;
            }
            $encKey = hash('sha256', $masterKey . 'enc', true);
            $macKey = hash('sha256', $masterKey . 'mac', true);
            if (!is_string($data)) {
                $data = json_encode($data);
            }
            if (!is_string($data) || json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', 'Data encryption failed: Data normalization failed');
                return false;
            }
            $iv = random_bytes(16);
            $ciphertext = openssl_encrypt($data, 'AES-256-CBC', $encKey, OPENSSL_RAW_DATA, $iv);
            if ($ciphertext === false) {
                log_message('error', 'Data encryption failed: OpenSSL encryption failed');
                return false;
            }
            $hmac = hash_hmac('sha256', $iv . $ciphertext, $macKey, true);
            $encrypted = $iv . $hmac . $ciphertext;
            return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
        } catch (Throwable $t) {
            log_message('error', 'Data encryption throwable: ' . $t->getMessage());
            return false;
        }
    }
}

if (!function_exists('decryptData')) {
    function decryptData($payload)
    {
        try {
            if (empty($payload)) {
                return false;
            }
            $masterKey = getenv('encryption.key');
            if (!$masterKey) {
                log_message('error', 'Data decryption failed: encryption.key not configured');
                return false;
            }
            $encKey = hash('sha256', $masterKey . 'enc', true);
            $macKey = hash('sha256', $masterKey . 'mac', true);
            $b64 = strtr($payload, '-_', '+/');
            $mod4 = strlen($b64) % 4;
            if ($mod4) {
                $b64 .= str_repeat('=', 4 - $mod4);
            }
            $data = base64_decode($b64, true);
            if ($data === false || strlen($data) < 48) {
                log_message('error', 'Data decryption failed: Invalid base64 or payload too short');
                return false;
            }
            $iv   = substr($data, 0, 16);
            $hmac = substr($data, 16, 32);
            $ct   = substr($data, 48);
            $calcHmac = hash_hmac('sha256', $iv . $ct, $macKey, true);
            if (!hash_equals($hmac, $calcHmac)) {
                log_message('error', 'Data decryption failed: HMAC verification failed');
                return false;
            }
            $plain = openssl_decrypt($ct, 'AES-256-CBC', $encKey, OPENSSL_RAW_DATA, $iv);
            if ($plain === false) {
                log_message('error', 'Data decryption failed: OpenSSL decryption failed');
                return false;
            }
            return $plain;
        } catch (Throwable $t) {
            log_message('error', 'Data decryption throwable: ' . $t->getMessage());
            return false;
        }
    }
}

if (!function_exists('generateEncryptionKey')) {
    function generateEncryptionKey($length = 32)
    {
        try {
            $key = random_bytes($length);
            return base64_encode($key);
        } catch (Throwable $t) {
            log_message('error', 'Encryption key generation throwable: ' . $t->getMessage());
            return false;
        }
    }
}
