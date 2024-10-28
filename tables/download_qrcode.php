<?php
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $url = $_GET['url'];

   
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        $imageContent = file_get_contents($url);

        if ($imageContent !== false) {
            
            $filename = 'qrcode.png';

            
            header('Content-Description: File Transfer');
            header('Content-Type: image/png');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Content-Length: ' . strlen($imageContent));
            header('Pragma: public');

            
            echo $imageContent;
            exit;
        } else {
            die('Failed to download QR Code file');
        }
    } else {
        die('The QR Code link is invalid');
    }
} else {
    die('There is no QR Code link');
}
?>
