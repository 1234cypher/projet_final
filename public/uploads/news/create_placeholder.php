<?php
header('Content-Type: image/jpeg');
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);
$backgroundColor = imagecolorallocate($image, 200, 200, 200); // Light gray background
$textColor = imagecolorallocate($image, 0, 0, 0); // Black text

// Fill the background
imagefill($image, 0, 0, $backgroundColor);

// Add text
$text = 'Placeholder Image';
$fontSize = 20;

// Use a built-in font for simplicity
imagestring($image, 5, 10, 10, $text, $textColor);

// Output the image
imagejpeg($image, 'public/uploads/news/default_news.jpg');
imagedestroy($image);
?>
