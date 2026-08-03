<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('encryptData')) {
    function encryptData($data)
    {
        $CI = &get_instance();

        // Encrypt data
        $encrypted = $CI->encryption->encrypt($data);

        // Make it URL safe
        return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
    }
}

if (!function_exists('decryptData')) {
    function decryptData($data)
    {
        $CI = &get_instance();

        // Decode from URL-safe base64
        $decoded = base64_decode(strtr($data, '-_', '+/'));

        // Decrypt value
        return $CI->encryption->decrypt($decoded);
    }
}

if (!function_exists('decrypt_encrypted_array')) {
    function decrypt_encrypted_array($encrypted_array)
    {
        if (!is_array($encrypted_array)) {
            return [];
        }

        $decrypted_array = [];
        foreach ($encrypted_array as $key => $value) {
            $decrypted_value = decryptData($value);
            if (!empty($decrypted_value)) {
                $decrypted_array[$key] = $decrypted_value;
            }
        }
        return $decrypted_array;
    }
}
