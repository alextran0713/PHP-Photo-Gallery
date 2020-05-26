<!DOCTYPE html>
<?php
// session_start();
// if( isset( $_SESSION['completed']) ) header("Location: index.html");
//First, we will check for the correct action
//If it is submit from html, we will start saving data to database
if (isset($_POST['submit'])) {
  //we create a connection to the database
  $conn = mysqli_connect('mariadb', 'cs431s41', 'Haiph1ch' ,'cs431s41');
  // $conn = mysqli_connect("localhost", "root", "", "cs431s41");

  if ($conn->connect_error) {
    die("connected failed: " . $conn->connect_error);
  }
  //We will save submitted value from html to php variable
  $photo = $_POST['photoName'];
  $name = $_POST['name'];
  $place = $_POST["location"];
  $date = $_POST['dateTaken'];
  $fileName = $_FILES["uploadFile"]["name"];

  //move the image to temp store then store it in local foulder
  move_uploaded_file($_FILES["uploadFile"]["tmp_name"],"uploads/".$_FILES["uploadFile"]["name"]);

  //Validating checking
  //If any field is missing, we will not save it into db.    
  if (!empty($photo) || !empty($name) || !empty($place) || !empty($date) || !empty($fileName)) {
    $sql = "INSERT INTO Gallery (photoName, photoTaker, place, imgURL, time) 
              VALUES ('$photo', '$name', '$place', '$fileName', '$date')";
    $result = mysqli_query($conn, $sql);
  }
  //After we finish, we need to close the connection to database
  mysqli_close($conn);
}
?>
<!--
I will be using bootstrap framework, so I included bootstrap css in the <head>
I will also include Bootstrap Javascript to create a responsive webpage. 
-->
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title></title>
  <style>
  </style>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
  <!--
This will be the fix content of gallery.php
It will include a title, a dropdown sort options list,
upload button to reroute back to Index.html for more upload
-->
  <div class="container" style="max-width:100%">
    <div class="jumbotron" style="margin-top:2vh;">
      <h1 class="display-5">View All Photos</h1>
      <hr class="my-4">
      <div class="row">
        <div class="col">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label class="input-group-text" style="border:none">
                <h4>Sort By:</h4>
              </label>
            </div>
            <form method="post">
              <select class="btn btn-primary btn-lg" name="input" id="input" aria-pressed="true" onchange="form.submit()">
                <option value="Default">Default</option>
                <option value="Photo">PhotoName</option>
                <option value="DateAs">Date</option>
                <option value="Location">Location</option>
                <option value="Name">Photographer</option>
              </select>
            </form>
          </div>
        </div>
        <div class="col">
          <a href="index.html" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Upload Image</a>
        </div>
      </div>
      <hr class="my-4">
      <!-- 
      We will connect to database, pull out data and display it
      -->
      <?php
      $conn = mysqli_connect('mariadb', 'cs431s41', 'Haiph1ch' ,'cs431s41');
      // $conn = mysqli_connect("localhost", "root", "", "cs431s41");
      if ($conn->connect_error) {
        die("connected failed: " . $conn->connect_error);
      }
      //Before user choose any option, Default will be used to display data
      //In short, it will display data according to ID value.
      $value = "Default";
      if ($value == "Default") {
        $default = "SELECT * FROM Gallery ORDER BY id ASC";
        if (!$default) {
          die('Could not get data: ' . mysqli_error($conn));
        }
        $result = mysqli_query($conn, $default);
      }
      //Checking to see if the user select sorting option
      if (isset($_POST['input'])) {
        //If yes, user option will be saved to $value
        $value = $_POST['input'];
        //If value = Name, we will sort by Photographer name, Ascending Alphabet
        if ($value == "Name") {
          $name_query = "SELECT * FROM Gallery ORDER BY photoTaker ASC";
          if (!$name_query) {
            die('Could not get data: ' . mysqli_error($conn));
          }
          $result = mysqli_query($conn, $name_query);
        }
        //If value = Location, we will sort by Location, Ascending Alphabet
        elseif ($value == "Location") {
          $place_query = "SELECT * FROM Gallery ORDER BY place ASC";
          if (!$place_query) {
            die('Could not get data: ' . mysqli_error($conn));
          }
          $result = mysqli_query($conn, $place_query);
        }
        //If value = Photo, we will sort by Photo name, Ascending Alphabet
        elseif ($value == "Photo") {
          $photo_query = "SELECT * FROM Gallery ORDER BY photoName ASC";
          if (!$photo_query) {
            die('Could not get data: ' . mysqli_error($conn));
          }
          $result = mysqli_query($conn, $photo_query);
        }
        //If value = Date, we will sort by calendar date, Ascending
        elseif ($value == "DateAs") {
          $date = "SELECT * FROM Gallery ORDER BY time ASC";
          if (!$date) {
            die('Could not get data: ' . mysqli_error($conn));
          }
          $result = mysqli_query($conn, $date);
        }
      }
      //After we finish, we need to close the connection to database
      mysqli_close($conn);
      ?>
      <div class="row">
        <?php while ($row = mysqli_fetch_array($result)) { ?>
          <div class="col-12 col-md-4 mb-5">
            <div class="card">
              <?php
              $name = $row['photoTaker'];
              $place = $row["place"];
              $date = date('m/d/Y', strtotime($row['time']));
              $fileName = $row['imgURL'];
              $photo = $row['photoName'];
              ?>
              <img src="uploads/<?php echo "$fileName" ?>" alt="" class="img-fluid w-100 mb-3">
              <div class="card-body">
                <p class="card-text">Photo name: <?php echo "$photo" ?></p>
                <p class="card-text">Date taken: <?php echo "$date" ?><p>
                <p class="card-text">Location: <?php echo "$place" ?><p>
                <p class="card-text">Photographer: <?php echo "$name" ?><p>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
    <!-- script for sort display text -->
    <script type="text/javascript">
      document.getElementById('input').value = "<?php echo $_POST['input']; ?>";
    </script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>

</html>