<?php include "header.php" ?>
<body>
<form id="uploadForm" action="upload.php" method="post">
    <h1>Rymate Videos</h1>
    <?php
    if (isset($_SESSION['errorMsg'])) { ?>
        <fieldset class="error">
        <legend>Errors</legend>
        <?php
        echo $_SESSION['errorMsg'];
        ?>
        </fieldset><?php }
    session_unset();
    ?>
    <fieldset>
        <legend class="bold">Video details</legend>
        <label><span class="bold">Title</span>
            <input id="title" type="text" name="title" accesskey="t" required/>
        </label>
        <label><span class="bold">Description (optional)</span>
            <textarea id="description" name="description" accesskey="d"></textarea>
        </label>

    </fieldset>
    <fieldset>
        <legend class="bold">Choose Video</legend>
        <label>
            <div id="holder" class="upload_box">
                <span>
                    <div id="status">Drag a file or click to select a video</div>
                    <input type="file" id="fileselect" name="fileselect"/>
                </span>
            </div>
        </label>
        <label>
            <?php
            $max_size = -1;

            if ($max_size < 0) {
                // Start with post_max_size.
                $max_size = parse_size(ini_get('post_max_size'));

                // If upload_max_size is less, then reduce. Except if upload_max_size is
                // zero, which indicates no limit.
                $upload_max = parse_size(ini_get('upload_max_filesize'));
                if ($upload_max > 0 && $upload_max < $max_size) {
                    $max_size = $upload_max;
                }
            }
            $max_size = $max_size / 1024 / 1024;
            echo "Max filesize: $max_size MB";

            function parse_size($size)
            {
                $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
                $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
                if ($unit) {
                    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
                    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
                } else {
                    return round($size);
                }
            }

            ?>
        </label>
    </fieldset>
    <input class="submit" type="submit" value="Upload!" accesskey="u"/>
</form>

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/index.js"></script>
</body>
</html>
