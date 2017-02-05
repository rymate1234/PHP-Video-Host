var statusText = $('#status');
var selectedFile;

$(function () {
    var fileselect = $('#file');
    fileselect.on("change", FileSelectHandler);
    $('#uploadForm').on("submit", function (e) {
        e.preventDefault();
        uploadFile();
        return false;
    });

    if (window.FileReader) {

        var holder = $('#holder');

        // is XHR2 available?
        var xhr = new XMLHttpRequest();
        if (xhr.upload) {
            // file drop
            holder.on("dragover", FileDragHover);
            holder.on("dragleave", FileDragHover);
            holder.on("drop", FileSelectHandler);
        }
    }
    else {
        $('#status').text('Click to upload a file...');
    }
});

// file drag hover
function FileDragHover(e) {
    e.stopPropagation();
    e.preventDefault();
}

function FileSelectHandler(e) {
    // cancel event and hover styling
    FileDragHover(e);

    // fetch FileList object
    var files = e.originalEvent.target.files
        || e.originalEvent.dataTransfer.files;

    // process all File objects
    for (var i = 0, f; f = files[i]; i++) {
        ParseFile(f);
    }

}

function ParseFile(file) {
    selectedFile = file
    $('#status').text("Selected: " + file.name);
    if ($('#title').val() === "") {
        $('#title').val(file.name);
    }
}

function uploadFile() {
    var form_data = new FormData();
    form_data.append('title', $('#title').val());
    form_data.append('description', $('#description').val());
    form_data.append('file', selectedFile);

    $.ajax({
        xhr: function (){
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    var percent = Math.round(percentComplete * 100);
                    $('#status').text('Progress: ' + percent + "%");
                }
            }, false);

            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    var percent = Math.round(percentComplete * 100);
                    $('#status').text('Progress: ' + percent + "%");
                }
            }, false);

            return xhr;
        },
        url: 'upload.php',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function (res) {
            if (res.error) {
                alert(res.message);
            }
            else {
                var url = window.location;

                var processReq = new FormData();
                processReq.append('id', res.message);

                $.ajax({
                    url: 'process.php',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: processReq,
                    type: 'post'
                });

                setTimeout(function() {
                    url.replace("index.php", "/");
                    console.log(url);
                    window.location = url + "video.php?id=" + res.message;
                }, 2000);

            }
        }

    });
}
