<?php
/**
* Image upload and thumbnail creation
*
* @author 	 Rudy Palacios <rudypalacios at gmail.com>
* @see 		 https://github.com/rudypalacios/Secure-File-Upload
* @copyright Just keep the author :)
*/

/* == CONFIGURATION ==*/
	//Name of the "file" field in your form
		define('_FIELD_NAME','my_image');
	//Path where your image will be temporarily uploaded for processing
		define('_UPLOAD_TMP','/my/temp/folder');
	//Path where the processed image will be moved to
		define('_UPLOAD_PATH','my/oficial/folder');
	//Path where the mid size thumbnail will be stored
		define('_UPLOAD_MID_PATH',_UPLOAD_PATH.'/mid');
	//Path where thumbnail will be stored
		define('_UPLOAD_THUMB_PATH',_UPLOAD_PATH.'/thumb');
	//Mid size thumbnail width
		define('_MID_WIDTH',250);
	//Small size thumbnail width
		define('_THUMB_WIDTH',64);

/**
* Image Upload Verification
* Get the $_FILES var and process an image through 5 security layers
* to avoid upload of malicious code.
*
* @param 	 array $files 	This will receive the $_FILES var
*
* @return 	 bool/string 	If there is any error FALSE will be returned
* 							allowing the user to customize the error message,
* 							if no error happens, it will return the encoded
*							file name to be used as needed(save to db, print, etc.)
*
*/
function imageUpload($files){
	//File info
		$filename = $files[_FIELD_NAME];
		$file_extension = strrchr($filename, ".");
		//5th. layer, change/encode name
		$new_filename = md5($filename.microtime()).$file_extension;
	//Validation
		$valid_file_extensions = array(".jpg", ".jpeg",".pjpeg", ".gif", ".png");
		$valid_mime_types = array( "image/gif", "image/png", "image/jpeg","image/pjpeg");

	 //1st. layer, Check if extension is allowed
	if (in_array($file_extension, $valid_file_extensions)) {
		//2nd. layer, check content type		
		if (in_array($files["type"], $valid_mime_types)) {
	    	//3rd. layer, recreate image
	    	if(recreateImage($files,$new_filename) === true){
	    		//4t. layer, check if content inside is a valid image
				if (@getimagesize(_UPLOAD_TMP.'/'.$new_filename) !== false) {
					//Move to where it belongs
					if(rename(_UPLOAD_TMP.'/'.$new_filename,
						   _UPLOAD_PATH.'/'.$new_filename)){
						return $new_filename;
					}else{
						return false; //Unable to be moved
					}
				}else{
					return false; //Image data incorrect
				}
			}else{
				return false; //Unable to recreate
			}
		}else{
			return false; //Incorrect type
		}
	}else{
		return false; //Incorrect extension
	}
}

/**
* Recreate Image
* Process $_FILES var, move from server's tmp folder to my _UPLOAD_TMP folder
* recreate it according to format and create thumbnail if requested.
*
* @param 	 array  $file 			This will receive the $_FILES var
* @param 	 string $new_filename 	New filename hashed (MD5) with extension
* @param 	 bool   $thumbnail 		Whether to create a thumbnail or not, default FALSE
*
* @return 	 bool 					TRUE on success, FALSE on error.
*
*/
function recreateImage($file,$new_filename,$thumbnail = false) {

	//Set data
		$result 		= false;
		$tmp_filename 	= basename($file["tmp_name"]);
		$tmp_src 		= _UPLOAD_TMP.'/'.$tmp_filename;
		$dest 			= _UPLOAD_TMP.'/'.$new_filename;

	//Move file from tmp location to my _UPLOAD_TMP folder
	if( move_uploaded_file($file["tmp_name"], $tmp_src) ){
		//Check mime from image
		$mime = image_type_to_mime_type(exif_imagetype($tmp_src));

		//Select how to recreate it
		if($mime !== false){
			switch ($mime) {
				case "image/gif":
					$im = @imagecreatefromgif($tmp_src);
					if($im !== false){
						$result = imagegif($im, $dest);
					}
				break;
				case "image/png":
					$im = @imagecreatefrompng($tmp_src);
					if($im !== false){
						$result = imagepng($im, $dest,8.5);
					}
				break;
				case "image/jpeg":
					$im = @imagecreatefromjpeg($tmp_src);
					if($im !== false){
						$result = imagejpeg($im, $dest,85);
					}
				break;
			}
		}

		//Add Thumbnail, if success
		if($result && $thumbnail){
			createThumbnail($new_filename,$im,$mime);
		}
		//Delete tmp/original, correct or not
		if(file_exists($tmp_src)){
			unlink($tmp_src);
		}
		return $result;
	}
	return false;
}

/**
* Create thumbnail
* Get previously created image and resize it to create 2 thumbnail sizes
*
* @see		 Create Image Thumbnails Using PHP(https://davidwalsh.name/create-image-thumbnail-php)
*
* @param 	 string $new_filename 	New filename hashed (MD5) with extension
* @param 	 string $im 			Previously created image path
* @param 	 string $mime 			Mime type obtained earlier to avoid recall of functions
* @param 	 string $desired_width 	New created image width(px), height will be automatically calculated
* @param 	 string $path 			Path to store the resized image
*
* @return 	 bool 					TRUE on success, FALSE on error.
*
*/
function createThumbnail($new_filename,$im,$mime,$desired_width=_THUMB_WIDTH,$path = _UPLOAD_THUMB_PATH){
	    
	   	$dest = $path.'/'.$new_filename;
	    /* read the source image */
		    $width  = imagesx($im);
		    $height = imagesy($im);
		    $result = false;

	    /* find the "desired height" of this thumbnail, relative to the desired width  */
	    	$desired_height = floor($height * ($desired_width / $width));
	    
	    /* create a new, "virtual" image */
	    	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	    
	    /* copy source image at a resized size */
	    	imagecopyresampled($virtual_image, $im, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	    
	    /* create the physical thumbnail image to its destination / 85 of quality */
	    switch ($mime) {
			case "image/gif":
				$result = imagegif($virtual_image, $dest);
			break;
			case "image/png":
				$result = imagepng($virtual_image, $dest,8.5);
			break;
			case "image/jpeg":
				$result = imagejpeg($virtual_image, $dest,85);
			break;
		}
		//Create mid size image if not exists
		if(!file_exists(_UPLOAD_MID_PATH.'/'.$new_filename)){
			createThumbnail($new_filename,$im,$mime,_MID_WIDTH,_UPLOAD_MID_PATH);	
		}
					
	return $result;
}
?>