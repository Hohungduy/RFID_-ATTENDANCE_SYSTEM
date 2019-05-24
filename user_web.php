<?php
require 'common.php';

//if (extension_loaded('gd') && function_exists('gd_info')){
//   echo "PHP GD library is installed on your web server";
//}
//else{
//  echo "PHP GD library is NOT installed on your web server";
//}
//die();

//Grab all the users from our database
$users = $database->select("users", [
    'id',
    'name',
    'MSSV',
    'rfid_uid',
    'updateA',
    'photo'
]);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Attendance System</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.comm/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    </head>
    <body>

    <nav class="navbar navbar-light" style="background-color:#edb593;">
        <a class="navbar-brand" style="color:Gray;font-family:verdana;font-size:200%" href="#"><b>HỆ THỐNG ĐIỂM DANH</b></a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="attendance.php" class="nav-link"><b>Xem điểm danh</b></a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link active"><b>Xem thành viên</b></a>
            </li>
        </ul>
    </nav>
    <div class="container">
        <div class="row">
            <h2>
               <img src="1200px-Logo-hcmut.svg.png" class="img-thumbnail" alt="1200px-Logo-hcmut" width="80" height="85">
               <b> Thành viên </b>
            </h2>
            <input class="form-control" id="myInput" type="text" placeholder="Tìm kiếm ..">
        </div>
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Tên thành viên</th>
		    <th scope="col">MSSV</th>
                    <th scope="col">Mã số RFID</th>
                    <th scope="col">Thời gian đăng kí</th>
		    <th scope="col">Hình ánh</th>
                </tr>
            </thead>
            <tbody id="myTable">
                <?php
                //Loop through and list all the information of each user including their RFID UID

                foreach($users as $user) {
                    echo '<tr>';
                    echo '<td scope="row">' . $user['id'] . '</td>';
                    echo '<td>' . $user['name'] . '</td>';
		    echo '<td>' . $user['MSSV'] . '</td>';
                    echo '<td>' . $user['rfid_uid'] . '</td>';
                    echo '<td>' . $user['updateA'] . '</td>';
                    $image_1 = $user['photo'];
//                    header("content-Type: image/jpeg");
                    echo '<td scope="row"> <img src="data:image/jpeg;base64,'.  base64_encode($image_1) . '" width="200" height="200"> </td>';
                    echo '</tr>';
                }
'<img src="data:image/jpeg;base64,' . base64_encode($image) . '" width="200" height="200">'

                ?>
            </tbody>
        </table>
    </div>
<script>
$(document).ready(function(){
 $("#myInput").on("keyup",function() {
  var value = $(this).val().toLowerCase();
  $("#myTable tr".filter(function() {
    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  });
 });
});
</body>
</html>
