<?php
class PDFEngine {
    public static function render($templatePath, $data, $filename = 'document.pdf', $outputMode = 'stream') {
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (!file_exists($autoload)) {
            throw new Exception("Composer autoloader not found. Please run 'composer install'.");
        }
        require_once $autoload;

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        // Get the absolute path to the project root
        $projectRoot = realpath(__DIR__ . '/../');
        
        // Add project root to data
        $data['project_root'] = $projectRoot;
        extract($data); // This needs to be here to make variables available in the template

        // Start output buffering and include the template
        ob_start();
        include __DIR__ . '/../' . $templatePath;
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($outputMode === 'stream') {
            $dompdf->stream($filename, ["Attachment" => false]);
        } else {
            $output = $dompdf->output();
            file_put_contents(__DIR__ . '/../public/assets/docs/' . $filename, $output);
            return '/assets/docs/' . $filename;
        }
    }

    /**
     * Converts an image file to a Base64 encoded string.
     *
     * @param string $path The absolute path to the image file.
     * @return string The Base64 encoded image string, or an empty string if the file is not found.
     */
    public static function imageToBase64($path) {
        if (!file_exists($path)) {
            error_log("Image file not found: " . $path);
            return '';
        }
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
}
