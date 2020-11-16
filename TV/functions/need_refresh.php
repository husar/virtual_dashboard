<?php 

    include "../../connect.ini.php";

    $query="SELECT ID FROM slides WHERE fromDate <= 'CURDATE()' AND refresh='1'";
    $apply=mysqli_query($connect,$query);

    $result=mysqli_fetch_array($apply);

    if(!empty($result)){
        $query="UPDATE SLIDES SET refresh='0' WHERE fromDate <= CURDATE() AND refresh='1'";
        $apply=mysqli_query($connect,$query);
        echo "yes";
    }else{
        echo "no";  
    }

?>