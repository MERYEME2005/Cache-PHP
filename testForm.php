<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="testForm.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <button type="submit">Submit</button>
</form>  
    
<?php
if(isset($_POST['username'])){
  $username=$_POST['username'];
  echo "welcome   ".$username;
}
?>
</body>
</html>