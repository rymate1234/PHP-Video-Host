<?php
require __DIR__ . '/vendor/autoload.php';
require 'db.php';

ignore_user_abort(true);

$fileId = isset($_REQUEST["id"]) ? $mysqli->real_escape_string($_REQUEST["id"]) : null;

if ($fileId != null && !file_exists("uploads/$fileId-progress")) {
    file_put_contents("uploads/$fileId-progress", 0);

    $ffmpeg = FFMpeg\FFMpeg::create();

    $video = $ffmpeg->open('uploads/' . $fileId . ".temp");

    $video
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
        ->save("uploads/$fileId.jpg");

    $format = new FFMpeg\Format\Video\X264();

    $format
        ->setKiloBitrate(7500)
        ->setAudioChannels(2)
        ->setAudioKiloBitrate(256);

    $format->setAudioCodec("libmp3lame");

    $format->on('progress', function ($video, $format, $percentage) {
        global $fileId;
        file_put_contents("uploads/$fileId-progress", $percentage);
    });

    $video->save($format, "uploads/$fileId.mp4");

    file_put_contents("uploads/$fileId-progress", 100);

    if (file_exists('uploads/' . $fileId . ".temp")) {
        unlink('uploads/' . $fileId . ".temp");
    }
}