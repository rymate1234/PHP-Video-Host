<?php
include "header.php";
require "db.php";


$value = isset($_REQUEST["id"]) ? $mysqli->real_escape_string($_REQUEST["id"]) : null;

if ($value == null) {
    header("Location: index.php");

    $_SESSION["errorMsg"] = "No ?id=videoid in url!";

    die();
}

/* Select queries return a resultset */
if ($result = $mysqli->query("SELECT video_title, video_desc, video_uploaded FROM videos where video_id='$value'")) {
    if ($result->num_rows == 0) {
        header("Location: index.php");

        $_SESSION["errorMsg"] = "That video does not exist!";

        die();
    }
    $row = $result->fetch_row();
}

?>

<body>

<h1><?php echo htmlspecialchars($row[0]) ?></h1>
<?php
$progress = file_get_contents("uploads/$value-progress");

if (file_exists("uploads/$value.mp4") && $progress == 100) { ?>
    <video controls>
        <source src="uploads/<?php echo $value ?>.mp4" type="video/mp4">
    </video>
    <?php
} else {
    echo "<p>Video being processed, check back later!</p>";
    echo "<p>Progress: $progress%</p>";
}
?>

<?php
$desc = $row[1];
if ($desc !== "") {
    ?>
    <fieldset class="description">
        <legend class="bold">Description</legend>
        <textarea readonly><?php echo htmlspecialchars($desc) ?></textarea>
    </fieldset>
    <?php
}
?>
<p><?php echo "Posted on " . $row[2] ?></p>

<script src="js/jquery-3.1.1.min.js"></script>
</body>

<?php
/* free result set */
$result->close(); ?>

</html>
