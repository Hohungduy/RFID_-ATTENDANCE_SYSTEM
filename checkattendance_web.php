<?php
require 'common.php';
//if (extension_loaded('gd') && function_exists('gd_info')){
//   echo "PHP GD library is installed on your web server";
//}
//else{
//  echo "PHP GD library is NOT installed on your web server";
//}
//die();



//Grab all users from our database
$users = $database->select("users", [
    'id',
    'name',
    'MSSV',
    'photo'
]);

//Check if we have a year passed in through a get variable, otherwise use the current year
if (isset($_GET['year'])) {
    $current_year = int($_GET['year']);
} else {
    $current_year = date('Y');
}

//Check if we have a month passed in through a get variable, otherwise use the current year
if (isset($_GET['month'])) {
    $current_month = $_GET['month'];
} else {
    $current_month = date('n');
}

//Calculate the amount of days in the selected month
$num_days = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    </head>
    <body>

    <nav class="navbar navbar-light" style="background-color: #edb282;" >
        <a class="navbar-brand" style="color:Gray;font-family:verdana;font-size:200%;" href="#"><b> HỆ THỐNG ĐIỂM DANH</b></a>
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a href="attendance.php" class="nav-link active"><b>Xem điểm danh</b></a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link"><b>Xem thành viên</b></a>
            </li>
        </ul>
    </nav>
    <div class="container">
        <div class="row">
            <h2>
               <img src="1200px-Logo-hcmut.svg.png" class="img-thumbnail" alt="1200px-Logo-hcmut" width="80" height="85">
             <b>  Điểm Danh </b>
             <input class="form-control" id="myInput" type="text" placeholder="Tìm kiếm..">
            </h2>
        </div>
        <table class="table table-striped table-responsive">
            <thead class="thead-dark">
                <tr>
                    <th scope="col" style="min-width:50px;max-width:80px;">id</th>
                    <th scope="col" style="min-width:200px;max-width:300px;">Tên thành viên</th>
                    <th scope="col" style="min-width:50px;max-width:100px;">MSSV</th>
                    <th scope="col" style="min-width:200px;max-width:300px;">Hinh anh</th>
                    <?php
                        //Generate headers for all the available days in this month
                       for ( $iter = 1; $iter <= $num_days; $iter++) {
                           echo '<th scope="col" style="min-width:200px;max-width:300px;">' . $iter . '</th>';
                        }
                    ?>
                </tr>
            </thead>
            <tbody id="myTable">
                <?php
                    //Loop through all our available users
                    foreach($users as $user) {
                        echo '<tr>';
			echo '<td scope="row">' . $user['id'] . '</td>';
                        echo '<td scope="row">' . $user['name'] . '</td>';
			echo '<td scope="row">' . $user['MSSV'] . '</td>';
                        $image = $user['photo'];
                        header("content-Type: image/jpeg");

            echo '<td scope="row"> <img src="data:image/jpeg;base64,'.  base64_encode($image) . '" width="200" height="200"> </td>';
                       

			 //Iterate through all available days for this month
                        for ( $iter = 1; $iter <= $num_days; $iter++) {

                            //For each pass grab any attendance that this particular user might of had for that day
                            $attendance = $database->select("attendance", [
                                'clock_in'
                            ], [
                                'user_id' => $user['id'],
                                'clock_in[<>]' => [
                                    date('Y-m-d', mktime(0, 0, 0, $current_month, $iter, $current_year)),
                                    date('Y-m-d', mktime(24, 60, 60, $current_month, $iter, $current_year))
                                ]
                            ]);

                            //Check if our database call actually found anything
                            if(!empty($attendance)) {
                                //If we have found some data we loop through that adding it to the tables cell
                                echo '<td class="table-success">';
                                foreach($attendance as $attendance_data) {
                                    echo $attendance_data['clock_in'] . '</br>';
                                }
                                echo '</td>';
                            } else {
                                //If there was nothing in the database notify the user of this.
                                echo '<td class="table-secondary">No Data Available</td>';
                            }
                        }
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
  $("#myTable tr").filter(function() {
   $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
  });
 });
});
</script>
</body>
</html>