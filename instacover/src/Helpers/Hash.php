<?php

namespace Core\Instacover\Helpers;
class Hash 
{
    private string $salt = 'dklshf3245ased3';

    /**
     * Hashes the given data using a secure algorithm.
     *
     * @param mixed $data
     * @return string
     */
    public function hashData($data): string
    {
        return base64_encode($data . '|' . $this->salt);
    }

    /**
     * Decodes the hash and returns the original id if valid.
     *
     * @param string $hash
     * @return mixed|null
     */
    public function decodeHash(string $hash)
    {
        $decoded = base64_decode($hash, true);
        if ($decoded === false) {
            return null;
        }
        $parts = explode('|', $decoded);
        if (count($parts) !== 2 || $parts[1] !== $this->salt) {
            return null;
        }
        return $parts[0];
    }
}