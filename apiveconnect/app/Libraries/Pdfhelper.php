<?php

namespace App\Libraries;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class PdfHelper
{
    public static function make(string $html, string $fileName, array $options = []): string
    {
        $tempDir = WRITEPATH . 'mpdf';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $defaults = [
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 15,
            'margin_right'  => 15,
            'margin_top'    => 15,
            'margin_bottom' => 15,
            'default_font'  => 'dejavusans',
            'tempDir'       => $tempDir,
        ];

        $mpdf = new Mpdf(array_merge($defaults, $options));
        $mpdf->WriteHTML($html);

        return $mpdf->Output($fileName, Destination::STRING_RETURN);
    }

    public static function makeFromView(string $viewPath, array $data, string $fileName, array $options = []): string
    {
        return self::make(view($viewPath, $data), $fileName, $options);
    }

    public static function download($response, string $html, string $fileName, array $options = [])
    {
        // Pass $options into self::make()
        $pdfContent = self::make($html, $fileName, $options);

        return $response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', (string) strlen($pdfContent))
            ->setBody($pdfContent);
    }
}
