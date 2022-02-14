<!--FIREBASE CONFIG-->

<?php

require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('fir-apps-14aae-firebase-adminsdk-ft3kc-f740a2b4ee.json') // Add the service_accounts.json
    ->withDatabaseUri('https://fir-apps-14aae-default-rtdb.firebaseio.com/'); // add the database uri

$database = $factory->createDatabase();

session_start();
?>


<!--HTML DOCUMENT-->
<!doctype html>
<html lang="en">

<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>iNotes - Notes taking made easy</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
    </script>
    <script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

</head>

<body style="background-color:rgb(214, 214, 214);">


<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">iNotes</a>

</nav>


<div class="container my-4">
    <h2>Add a Note to iNotes</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="title">Note Title</label>
            <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp">
        </div>

        <div class="form-group">
            <label for="desc">Note Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Note</button>
    </form>
</div>

<?php


if (isset($_GET['dkey'])) {
    $database->getReference("Notes/" . $_GET['dkey'])->remove();
    header("Location:index.php");

}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['ekey'])) {
        // Update the record
        $ekey = $_POST["ekey"];
        $title = $_POST["titleEdit"];
        $description = $_POST["descriptionEdit"];

        date_default_timezone_set("Asia/Calcutta");
        $timestamp = date("d-m-Y H:i:s");

        $postData = [

            'Title' => $title,
            'Description' => $description,
            'Timestamp' => $timestamp

        ];
        $ref_table = $ekey;
        $result = $database->getReference($ref_table)->update($postData);

        if ($result) {

            $_SESSION['status'] = "Success";
            $edit = true;
            header("Location:index.php");
        } else {

            $_SESSION['status'] = " Error";
            echo "The record was not inserted successfully because of this error ---> " . $_SESSION['status'];
            $edit = false;
        }
    } else {
        $title = $_POST["title"];
        $description = $_POST["description"];

        date_default_timezone_set("Asia/Calcutta");
        $timestamp = date("d-m-Y H:i:s");

        $postData = [

            'Title' => $title,
            'Description' => $description,
            'Timestamp' => $timestamp


        ];
        $ref_table = "Notes";
        $result = $database->getReference($ref_table)->push($postData);

        if ($result) {

            $_SESSION['status'] = "Success";
            $insert = true;
            header("Location:index.php");
        } else {

            $_SESSION['status'] = " Error";
            echo "The record was not inserted successfully because of this error ---> " . $_SESSION['status'];
            $insert = false;
        }
    }
}


?>

<div class="container my-4">


    <table class="table" id="myTable">
        <thead>
        <tr align="center">
            <th scope="col">S.No</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>


        <?php


        $ref = "Notes";
        $data1 = $database->getReference($ref)->getValue();
        $i = 0;
        foreach ($data1 as $key => $data2) {
            $i++;

            ?>

            <tr align="center">
                <th scope='row'><?php echo $i; ?></th>
                <td><?php echo $data2['Title']; ?></td>
                <td><?php echo $data2['Description']; ?></td>
                <td>
                    <button class='edit btn btn-sm btn-primary' id="<?php echo $data2['Description']; ?>">Edit</button>
                    <button class='delete btn btn-sm btn-primary' id="d<?php echo $key; ?>">Delete</button>
                </td>

                <input id="key" type="hidden" value="Notes/<?php echo $key; ?>">

            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
</div>
<hr>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ekey" id="snoEdit">
                    <div class="form-group">
                        <label for="title">Note Title</label>
                        <input type="text" class="form-control" id="titleEdit" name="titleEdit"
                               aria-describedby="emailHelp">
                    </div>

                    <div class="form-group">
                        <label for="desc">Note Description</label>
                        <textarea class="form-control" id="descriptionEdit" name="descriptionEdit"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer d-block mr-auto">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<script>
    $(document).ready(function () {
        $('#myTable').DataTable();

    });
</script>
<script>
    edits = document.getElementsByClassName('edit');
    Array.from(edits).forEach((element) => {
        element.addEventListener("click", (e) => {
            console.log("edit ");
            tr = e.target.parentNode.parentNode;
            title = tr.getElementsByTagName("td")[0].innerText;
            description = tr.getElementsByTagName("td")[1].innerText;
            console.log(title, description);
            titleEdit.value = title;
            descriptionEdit.value = description;
            snoEdit.value = tr.getElementsByTagName("input")[0].value;
            console.log(e.target.id)
            $('#editModal').modal('toggle');
        })
    })
    deletes = document.getElementsByClassName('delete');
    Array.from(deletes).forEach((element) => {
        element.addEventListener("click", (d) => {
            console.log("delete ");
            key = d.target.id.substr(1);

            if (confirm("Are you sure you want to delete this note!")) {
                console.log("yes");
                window.location = `index.php?dkey=${key}`;
                // TODO: Create a form and use post request to submit a form
            } else {
                console.log("no");
            }
        })
    })
</script>

</body>

</html>