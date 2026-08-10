<?php

namespace App\Models;

use CodeIgniter\Model;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;


class ExhibitorBadgeModel extends Model
{
    protected $table = 'manual_exhibitor_badges';
    protected $primaryKey = 'id';

    public function getBadgeForVendor(int $badgeId, int $exhibitorId): ?array
    {
        $row = $this->db
            ->table('manual_exhibitor_badges')
            ->where('id', $badgeId)
            ->where('exhibitor_id', $exhibitorId)
            ->where('is_deleted', 0)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    public function getEventName(int $subEventId): string
    {
        $subEvent = $this->db
            ->table('manual_setups')
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRowArray();
        $subEvent_name = $this->db
            ->table('company_sub_events')
            ->where('id', $subEventId)
            ->get()
            ->getRowArray();

        if (!$subEvent_name) {
            return '';
        }

        if (!empty($subEvent_name['sub_event_name'])) {
            return $subEvent_name['sub_event_name'];
        }

        if (!empty($subEvent['manual_welcome_note'])) {
            $eventName = strip_tags(
                html_entity_decode(
                    $subEvent['manual_welcome_note']
                )
            );

            $eventName = trim(
                preg_replace('/\s+/', ' ', $eventName)
            );

            return mb_substr($eventName, 0, 80);
        }

        return '';
    }

    public function getEventTheme(int $subEventId): array
    {
        $row = $this->db
            ->table('manual_setups')
            ->select(
                'exhibitor_badge_color,
             exhibitor_badge_background'
            )
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRowArray();

        $color = trim($row['exhibitor_badge_color'] ?? '');
        $background = trim($row['exhibitor_badge_background'] ?? '');

        return [
            'primary' => $color ?: '#1a1a2e',
            'secondary' => $color ?: '#1a1a2e',
            'color' => $color,
            'background' => $background,
        ];
    }

    private function resolveBackgroundBase64(?string $backgroundPath): ?string
    {
        if (empty($backgroundPath)) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $backgroundPath)) {
            return $this->pathToBase64($backgroundPath);
        }

        $baseUrl = rtrim(env('UPLOAD_BASE_URL'), '/');
        $imageUrl = $baseUrl . '/' . ltrim($backgroundPath, '/');
        $imageContent = @file_get_contents($imageUrl);

        if ($imageContent === false) {
            log_message('error', 'Unable to fetch badge background image: ' . $imageUrl);
            return null;
        }

        $extension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };

        return 'data:' . $mime . ';base64,' . base64_encode($imageContent);
    }

    public function getExhibitorCompanyName(int $exhibitorId, ?array $badge = null): string
    {
        if (!empty($badge['company_name'])) {
            return $badge['company_name'];
        }
        if (!empty($badge['company'])) {
            return $badge['company'];
        }

        $row = $this->db
            ->table('exhibitors')
            ->where('id', $exhibitorId)
            ->get()
            ->getRowArray();

        if (!$row) {
            return '';
        }

        return $row['organisation_name'] ?? '';
    }

    public function pathToBase64(?string $pathOrUrl): ?string
    {
        if (empty($pathOrUrl)) {
            return null;
        }
        if (preg_match('/^https?:\/\//i', $pathOrUrl)) {
            $bytes = @file_get_contents($pathOrUrl);
            if ($bytes === false) {
                log_message(
                    'error',
                    'Unable to fetch image: ' . $pathOrUrl
                );

                return null;
            }
            $mime =
                $this->guessMimeFromBytes($bytes)
                ?: 'image/jpeg';
            return
                'data:' .
                $mime .
                ';base64,' .
                base64_encode($bytes);
        }
        if (
            !is_file($pathOrUrl) ||
            !is_readable($pathOrUrl)
        ) {
            return null;
        }
        $mime =
            mime_content_type($pathOrUrl)
            ?: 'image/jpeg';

        $bytes = file_get_contents($pathOrUrl);

        if ($bytes === false) {
            return null;
        }
        return
            'data:' .
            $mime .
            ';base64,' .
            base64_encode($bytes);
    }

    private function guessMimeFromBytes(string $bytes): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($bytes) ?: null;
    }

    private function resolveImageBase64(
        ?string $image,
        string $defaultDirectory = ''
    ): ?string {
        if (empty($image)) {
            return null;
        }
        if (
            preg_match(
                '/^https?:\/\//i',
                $image
            )
        ) {
            return $this->pathToBase64($image);
        }
        $directPath = FCPATH . ltrim(
            $image,
            '/\\'
        );
        if (is_file($directPath)) {
            return $this->pathToBase64(
                $directPath
            );
        }
        if (!empty($defaultDirectory)) {
            $directoryPath =
                rtrim(
                    FCPATH . $defaultDirectory,
                    '/\\'
                )
                .
                DIRECTORY_SEPARATOR
                .
                basename($image);
            if (is_file($directoryPath)) {
                return $this->pathToBase64(
                    $directoryPath
                );
            }
        }
        return null;
    }

    public function generateQrBase64(string $value, int $size = 400): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $value,
            size: $size,
            margin: 10
        );
        $result = $builder->build();
        return $result->getDataUri();
    }

    private function makeCircularPhotoBase64(string $imageContent, int $size = 400): ?string
    {
        if (!class_exists('Imagick')) {
            return null;
        }
        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($imageContent);
            $imagick->setImageFormat('png');
            $imagick->trimImage(0.02 * \Imagick::getQuantum());
            $imagick->setImagePage(0, 0, 0, 0);
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            $cropSize = min($width, $height);
            $x = (int) (($width - $cropSize) / 2);
            $y = (int) (($height - $cropSize) / 2);
            $imagick->cropImage($cropSize, $cropSize, $x, $y);
            $imagick->setImagePage(0, 0, 0, 0);
            $imagick->resizeImage($size, $size, \Imagick::FILTER_LANCZOS, 1);
            $mask = new \Imagick();
            $mask->newImage($size, $size, new \ImagickPixel('transparent'));
            $mask->setImageFormat('png');
            $draw = new \ImagickDraw();
            $draw->setFillColor(new \ImagickPixel('black'));
            $draw->circle($size / 2, $size / 2, $size / 2, 0);
            $mask->drawImage($draw);
            $imagick->setImageMatte(true);
            $imagick->compositeImage($mask, \Imagick::COMPOSITE_DSTIN, 0, 0);
            $result = $imagick->getImageBlob();
            $imagick->clear();
            $imagick->destroy();
            $mask->clear();
            $mask->destroy();
            return 'data:image/png;base64,' . base64_encode($result);
        } catch (\Throwable $e) {
            log_message('error', 'makeCircularPhotoBase64 failed: ' . $e->getMessage());
            return null;
        }
    }

    public function buildBadgeViewData(
        int $badgeId,
        int $exhibitorId,
        int $subEventId,
        string $fallbackPhotoPath = ''
    ): ?array {
        $badge = $this->getBadgeForVendor(
            $badgeId,
            $exhibitorId
        );
        if (!$badge) {
            return null;
        }
        $eventName = $this->getEventName(
            $subEventId
        );
        $theme = $this->getEventTheme(
            $subEventId
        );
        $photoBase64 = null;
        if (!empty($badge['exhibitor_image'])) {
            $baseUrl = rtrim(env('UPLOAD_BASE_URL'), '/');
            $imageUrl = $baseUrl . '/' . ltrim($badge['exhibitor_image'], '/');
            $imageContent = @file_get_contents($imageUrl);
            if ($imageContent !== false) {
                $photoBase64 = $this->makeCircularPhotoBase64($imageContent, 400);

                if (!$photoBase64) {
                    $extension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
                    $mime = match ($extension) {
                        'png'  => 'image/png',
                        'jpg', 'jpeg' => 'image/jpeg',
                        'gif'  => 'image/gif',
                        'webp' => 'image/webp',
                        default => 'image/jpeg',
                    };
                    $photoBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageContent);
                }
            }
        }
        if (
            !$photoBase64 &&
            !empty($fallbackPhotoPath)
        ) {
            $fallbackContent = @file_get_contents($fallbackPhotoPath);
            if ($fallbackContent !== false) {
                $photoBase64 = $this->makeCircularPhotoBase64($fallbackContent, 400);
            }
            if (!$photoBase64) {
                $photoBase64 = $this->pathToBase64($fallbackPhotoPath);
            }
        }

        $backgroundBase64 = $this->resolveBackgroundBase64($theme['background']);

        $uniqueValue = 'EXBTR_' . (string) $badge['id'];
        $qrBase64 = $this->generateQrBase64(
            $uniqueValue
        );
        $fullName = trim(
            ($badge['first_name'] ?? '')
                .
                ' '
                .
                ($badge['last_name'] ?? '')
        );
        $companyName =
            $this->getExhibitorCompanyName(
                $exhibitorId,
                $badge
            );
        return [
            'sub_event_name' => $eventName ?: 'EXHIBITOR EVENT',
            'full_name' => $fullName,
            'first_name' => $badge['first_name'] ?? '',
            'last_name' => $badge['last_name'] ?? '',
            'email' => $badge['email'] ?? '',
            'mobile_number' => $badge['mobile_number'] ?? '',
            'company_name' => $companyName,
            'theme_primary' => $theme['primary'],
            'theme_secondary' => $theme['secondary'],
            'badge_background' => $backgroundBase64,
            'photo' => $photoBase64,
            'qr' => $qrBase64,
        ];
    }

    public function generateBadgePdf(
        int $badgeId,
        int $exhibitorId,
        int $subEventId,
        string $fallbackPhotoPath = ''
    ): ?array {

        $viewData = $this->buildBadgeViewData(
            $badgeId,
            $exhibitorId,
            $subEventId,
            $fallbackPhotoPath
        );
        if (!$viewData) {
            return null;
        }
        $html = view(
            'exhibitor_badge_pdf',
            $viewData
        );
        $tempDir = WRITEPATH . 'mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => [100, 125],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir
        ]);
        $mpdf->WriteHTML($html);
        $safeName = preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '_',
            $viewData['full_name'] ?: 'exhibitor'
        );
        $fileName = 'Badge-' . $safeName . '.pdf';
        $content = $mpdf->Output(
            '',
            Destination::STRING_RETURN
        );
        return [
            'fileName' => $fileName,
            'content' => $content
        ];
    }
}
