<?php
	require 'lib.php';
	if($_FILES['upload_file']['name']!=''){
		$result = imageUpload($_FILES['upload_file']);
		if($result){
			$msg = '<div class="alert alert-success" role="alert">File:'.$result.' uploaded</div>';
		}else{
			$msg = '<div class="alert alert-danger" role="alert">Error on upload</div>';
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Secure file upload - Single file xample</title>

    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  </head>

  <body>

    <div class="container">     
      <br><br>
        <h1 class="text-center">Single file upload</h1>
        <div class="well col-md-6 col-md-offset-3">
			<form action="" method="post" enctype="multipart/form-data">
			  <div class="form-group">
			    <label for="upload_file">File input</label>
			    <input type="file" name="upload_file" id="upload_file">
			  </div>
			  <input type="submit" class="btn btn-success" value="Upload Image" name="submit">
			</form>
			<br>
			<?=$msg?>
        </div>    
    </div><!-- /.container -->

  </body>
</html>