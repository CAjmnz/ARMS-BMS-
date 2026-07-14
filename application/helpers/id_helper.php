<?php defined('BASEPATH')or exit('No direct script access allowed');

/**
 * Encode a raw DB id into an opaque URL-safe string
 */
function encode_id($id)
{
    $ci =& get_instance();
    $ci->load->library('encryption');

    $encrypted = $ci->encryption->encrypt((string) $id);

    // Make URL-safe (encryption output can contain +,/, =)
    return rtrim(strtr($encrypted, '+/', '-_'), '=');
}
/**
 * Decode an encoded id string back into the raw DB id.
 * return null if decoding/decryption fails (invalid or tampered value).
 */
function decode_id($encoded)
{
    $ci =& get_instance();
    $ci->load->library('encryption');

    // Reverse URL-safe transformation
    $padded = strtr($encoded, '-_', '+/');
    $padded .= str_repeat('=', (4 - strlen($padded) % 4) % 4);

    $decrypted = $ci->encryption->decrypt($padded);

    if ($decrypted === false) {
        return null;
    }

    return (int) $decrypted;
}