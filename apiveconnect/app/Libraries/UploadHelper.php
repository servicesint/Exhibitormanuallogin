<?php

namespace App\Libraries;

class UploadHelper
{
    public static function upload($file, string $folder, bool $returnFullUrl = true, string $prefix = 'file'): ?string
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $receiverUrl = env('UPLOAD_RECEIVER_URL', 'http://si.glomis.in/veconnect/upload_receiver.php');
        $secret = env('UPLOAD_RECEIVER_SECRET', '');

        $curlFile = new \CURLFile(
            $file->getTempName(),
            $file->getClientMimeType(),
            $file->getClientName()
        );

        $postData = [
            'secret' => $secret,
            'folder' => $folder,
            'prefix' => $prefix,
            'file'   => $curlFile,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $receiverUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception('UploadHelper cURL error: ' . $curlError . ' | URL used: ' . $receiverUrl);
        }

        $result = json_decode($response, true);

        if (!$result || empty($result['success'])) {
            throw new \Exception('UploadHelper receiver error: ' . $response . ' (HTTP ' . $httpCode . ') | URL used: ' . $receiverUrl);
        }

        $newName = $result['filename'];

        return $folder . '/' . $newName;
    }

    public static function getUrl(?string $dbValue, string $folder = ''): ?string
    {
        if (empty($dbValue)) {
            return null;
        }

        if (preg_match('#^https?://#i', $dbValue)) {
            return $dbValue;
        }

        $folderPart = $folder !== '' ? $folder . '/' : '';
        return self::baseUrl() . '/' . $folderPart . ltrim($dbValue, '/');
    }

    public static function delete(?string $dbValue, string $folder): bool
    {
        if (empty($dbValue)) {
            return true;
        }
        $receiverUrl = env('DELETE_RECEIVER_URL');
        $secret = env('UPLOAD_RECEIVER_SECRET');
        $postData = [
            'secret'   => $secret,
            'folder'   => $folder,
            'filename' => basename($dbValue),
        ];
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $receiverUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response, true);
        return !empty($result['success']);
    }

    private static function baseUrl(): string
    {
        return rtrim(env('UPLOAD_BASE_URL', 'http://si.glomis.in/veconnect/uploads'), '/');
    }
}
