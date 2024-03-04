<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    
    <style>
	.table-responsive {
    max-height:450px;
}
	</style>
    </head>
<body>
  <div class="form-group" align="center" style="border: 2px double #000; width: 500px; margin: 20px auto; ">
  <h3>BERHAMPORE POLICE BANK SMS</h3>

<?php



  $conn = mysqli_connect("localhost", "u974046780_system_point", "System_Point@123", "u974046780_system_point");
  
  $result = mysqli_query($conn, 'SELECT SUM(total) AS value_sum from sms_mdppcc');
  $row = mysqli_fetch_assoc($result); 
  $response = 43820 - $row['value_sum'];
  
  echo '<font color="green" >Balance '.$response."</font>".'<button style="margin-right: 10px;" class="btn btn-primary float-right btn-sm" onClick="window.location.href=window.location.href">Refresh Page</button>';
?>
<form enctype="multipart/form-data" method="post" role="form" style="padding:15px;" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <div class="form-group">
        <label for="exampleInputFile">File Upload</label>
        <input type="file" name="file_name" id="file" size="200">
       
   
    <button type="submit" class="btn btn-default" name="submit" value="submit">Upload</button>
     </div>
</form>

</div>
<div class="container table-responsive">

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {	
    
date_default_timezone_set('Asia/kolkata');

//print_r($_POST);exit;
require_once "Classes/PHPExcel.php";

 $path =$_FILES['file_name']['tmp_name']; //path and extention location and file name
$excelReader = PHPExcel_IOFactory::createReaderForFile($path);
$excel_obj = $excelReader->load($path);

$worksheet = $excel_obj->getSheet('0');
$worksheet->getCell('A1')->getValue();

$lastRow = $worksheet->getHighestRow();
$colCount = $worksheet->getHighestDataColumn();
$columnconunt_number = PHPExcel_Cell::columnIndexFromString($colCount);

	echo "<table class='table table-striped table-sm table-bordered'>";
	for($row=1; $row <= $lastRow; $row++){
				$mobileNumber= $worksheet->getCell('A'.$row);
				$message = $worksheet->getCell('B'.$row);
					
				$authKey = "228887AjllYBo7Qcyh5b5ebaaa";

			
				$dlt = "1207166115689631150";

				//Sender ID,While using route4 sender id should be 6 characters long.
				$senderId = "MDPPCC";				

				//Define route 
				$route = "4";
				//Prepare you post parameters
				$postData = array(
				    'authkey' => $authKey, 
				    'mobiles' => $mobileNumber,
				    'message' => 'Message, '.$message.' by GS3 SOLUTION',		   
				    'sender' => $senderId,
				    'route' => $route,
				    'DLT_TE_ID' => $dlt
				);

				//API URL							
				$url = "https://api.msg91.com/api/sendhttp.php";

				$ch = curl_init();
				curl_setopt_array($ch, array(
				    CURLOPT_URL => $url,
				    CURLOPT_RETURNTRANSFER => true,
				    CURLOPT_POST => true,
				    CURLOPT_POSTFIELDS => $postData
				    //,CURLOPT_FOLLOWLOCATION => true
				));


				//Ignore SSL certificate verification
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


				//get response
				$output = curl_exec($ch);

				//Print error if any
				if(curl_errno($ch))
				{
				    echo 'error:' . curl_error($ch);
				}

				curl_close($ch);

				//echo $output;

				echo "<tr>";				
				for($col=0; $col <= $columnconunt_number; $col++){
				
				echo "<td>";
				echo $row.') ';  				
				echo $worksheet ->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
				echo "</td>";
				}				
				echo "</tr>";					
		//	echo "<br>";
		//echo $worksheet->getCell('A'.$row) . ' ' . $worksheet->getCell('B'.$row);
    		$time = date("Y-m-d h:i:sa");
    		
    		$number = strlen($message); // Replace this with your desired number
            $divisor = 160;
            $remainder = $number % $divisor;
            
            if ($remainder == 0) {
                $result = $number / $divisor;
            } else {
                $result = floor($number / $divisor) + 1;
            }
            
    		$sql = "INSERT INTO sms_mdppcc VALUES ('', '$mobileNumber', '$message','$result','$time')";
    		
    		if(mysqli_query($conn, $sql)){
                //echo "<h1>data stored in a database successfully</h3>";
            } else{
                //echo "ERROR: Hush! Sorry $sql. ". mysqli_error($conn);
            }
		
		}
	echo "</table>";
	if(!empty($output))	{
		echo "message send successfully...";
	}
	// Close connection
	mysqli_close($conn);
}		
?>
</div>
</body>
</html>