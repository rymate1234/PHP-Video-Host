<?php
// Start the session
session_start();
require __DIR__ . '/vendor/autoload.php';
require "db.php";
ignore_user_abort(true);

header('Content-type: application/json');

$return = array('error' => false, "message" => "");

$title = getRequest('title', false);
$description = getRequest('description', true);
$ffmpeg = FFMpeg\FFMpeg::create();

if ($mysqli->connect_errno) {
    $return["message"] = "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") ";
    $return["error"] = true;
} else {
    if (isset($_FILES['file'])) {
        if (0 < $_FILES['file']['error']) {
            $return["message"] = 'Error: ' . $_FILES['file']['error'] . '<br>';
            $return["error"] = true;
        } else {
            $fileId = generateRandomString();

            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $fileId . ".temp");

            $video = $ffmpeg->open('uploads/' . $fileId . ".temp");

            $video
                ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))
                ->save("uploads/$fileId.jpg");

            $format = new FFMpeg\Format\Video\X264();

            $format->setAudioCodec("libmp3lame");

            $format->on('progress', function ($video, $format, $percentage) {
                $_SESSION["progress"] = "$percentage% processed";
            });

            $video->save($format, "uploads/$fileId.mp4");

            if (!$mysqli->query("INSERT INTO videos(video_id, video_title, video_desc) VALUES ('$fileId', '$title', '$description')")) {
                $return["message"] = "failed: (" . $mysqli->errno . ") " . $mysqli->error;
                $return["error"] = true;
            }
            $return["message"] = $fileId;
        }
    } else {
        $return["message"] = "No file uploaded!";
        $return["error"] = true;
    }
}


function getRequest($key, $optional)
{
    global $mysqli;

    $value = isset($_REQUEST[$key]) ? $mysqli->real_escape_string($_REQUEST[$key]) : null;

    if ($value == null && !$optional) {
        $return["message"] = "Please enter in the video $key!";
        $return["error"] = true;
    }

    return $value;
}

function generateRandomString($length = 10)
{
    return substr(str_shuffle(MD5(microtime())), 0, $length);
}

header('Content-type: application/json');
echo json_encode($return);
