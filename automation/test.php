<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bootstrap 4 Bordered Table</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round|Open+Sans">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style type="text/css">
    .bs-example {
        margin: 20px;
    }
    </style>
    <script type="text/javascript">
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</head>

<body>
    <div class="bs-example">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Users List</h2>
                        <button>Export XLS</button> <button>Email to Distributor</button>
                    </div>
                    <?php
include_once 'db.php';

$result = mysqli_query($conn,"SELECT * FROM ospos_people");
?>
                    <?php
if (mysqli_num_rows($result) > 0) {
?>
                    <table class='table table-bordered table-striped'>
                        <tr>
                            <td>Name</td>
                            <td>Email id</td>
                            <td>Mobile</td>
                        </tr>
                        <?php
$i=0;
while($row = mysqli_fetch_array($result)) {
?>
                        <tr>
                            <td><?php echo $row["first_name"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["phone_number"]; ?></td>
                        </tr>
                        <?php
$i++;
}
?>
                    </table>
                    <?php
}
else{
echo "No result found";
}
?>
                </div>
            </div>
        </div>
    </div>


    <div class="bs-example">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Distributors Sales List</h2>
                    </div>
                    <?php
include_once 'db.php';

$result = mysqli_query($conn,"SELECT * FROM ospos_sales");
?>
                    <?php
if (mysqli_num_rows($result) > 0) {
?>
                    <table class='table table-bordered table-striped'>
                        <tr>
                            <td>Sale Date</td>
                            <td>Sale ID</td>
                        </tr>
                        <?php
$i=0;
while($row = mysqli_fetch_array($result)) {
?>
                        <tr>
                            <td><?php echo $row["sale_time"]; ?></td>
                            <td><?php echo $row["sale_id"]; ?></td>
                        </tr>
                        <?php
$i++;
}
?>
                    </table>
                    <?php
}
else{
echo "No result found";
}
?>
                </div>
            </div>
        </div>
    </div>























</body>

</html>