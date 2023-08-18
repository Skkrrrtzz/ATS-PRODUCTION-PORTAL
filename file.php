<?php include 'ATS_Prod_Header.php' ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    if ($_FILES['upload_file1']['size'] <= 0) {
        echo 'Hey, Please choose at least one file';
    } else {
        foreach ($_FILES as $key => $value) {
            if (0 < $value['error']) {
                echo 'Error during file upload ' . $value['error'];
            } else if (!empty($value['name'])) {
                $conn = mysqli_connect('localhost', 'root', '', 'ewip') or die('MySQL connect failed. ' . mysqli_connect_error());

                $sql = "insert into files_data(name, type, size, content, saved_date) values('" . $value['name'] . "', '" . $value['type'] . "', '" . filesize_formatted($value['size']) . "', '" . mysqli_escape_string($conn, file_get_contents($value['tmp_name'])) . "', '" . date('Y-m-d H:i:s') . "')";

                $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));

                if ($result) {
                    echo 'File successfully saved to database';
                    header('location:file.php');
                }
            }
        }
    }
}

function filesize_formatted($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;

    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="container">
        <form name="upload_form" enctype="multipart/form-data" action="file.php" method="POST">
            <fieldset>
                <legend>Files Save into MySQL database using PHP</legend>
                <section>
                    <label>Browse a file</label>
                    <label>
                        <input type="file" name="upload_file1" id="upload_file1" readonly="true" />
                    </label>
                    <div id="moreFileUpload"></div>
                    <div style="clear:both;"></div>
                    <div id="moreFileUploadLink" style="display:none;margin-left: 10px;">
                        <a href="javascript:void(0);" id="attachMore">Attach another file</a>
                    </div>
                </section>
            </fieldset>
            <div>&nbsp;</div>
            <footer>
                <input type="submit" name="upload" value="Upload" />
            </footer>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Saved Date</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM files_data";
                    $result = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td><?php echo $row['id'] ?></td>
                            <td><?php echo $row['name'] ?></td>
                            <td><?php echo $row['type'] ?></td>
                            <td><?php echo $row['size'] ?></td>
                            <td><?php echo $row['saved_date'] ?></td>
                            <td>
                                <?php
                                if (strpos($row['type'], 'image/png') !== false) {
                                    echo '<img class="img-thumbnail" src="data:' . $row['type'] . ';base64,' . base64_encode($row['content']) . '" alt="' . $row['name'] . '" style="max-width: 500px; max-height: 200px;">';
                                } else {
                                    echo 'Not an image';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div>
            <iframe src="" frameborder="0"></iframe>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $("input[id^='upload_file']").each(function() {
                    var id = parseInt(this.id.replace("upload_file", ""));
                    $("#upload_file" + id).change(function() {
                        if ($("#upload_file" + id).val() !== "") {
                            $("#moreFileUploadLink").show();
                        }
                    });
                });
            });
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                var upload_number = 2;
                $('#attachMore').click(function() {
                    //add more file
                    var moreUploadTag = '';

                    moreUploadTag += '<div class="element"><label for="upload_file"' + upload_number + '>Upload File ' + upload_number + '</label>';
                    moreUploadTag += '&nbsp;<input type="file" id="upload_file' + upload_number + '" name="upload_file' + upload_number + '"/>';
                    moreUploadTag += '&nbsp;<a href="javascript:void" style="cursor:pointer;" onclick="deletefileLink(' + upload_number + ')">Delete ' + upload_number + '</a></div>';

                    $('<dl id="delete_file' + upload_number + '">' + moreUploadTag + '</dl>').fadeIn('slow').appendTo('#moreFileUpload');

                    upload_number++;
                });
            });
        </script>

        <script type="text/javascript">
            function deletefileLink(eleId) {
                if (confirm("Are you really want to delete ?")) {
                    var ele = document.getElementById("delete_file" + eleId);
                    ele.parentNode.removeChild(ele);
                }
            }
        </script>
</body>

</html>