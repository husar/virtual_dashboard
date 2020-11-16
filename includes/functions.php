<?php

function updateContribution(){
    global $connect;
    if(isset($_POST['update'])){
    
        if(isset($_POST['fromDate'])){
            $query_zaznamy="UPDATE slides SET fromDate='".$_POST['fromDate']."' WHERE ID='".$_POST['id_prispevku']."'";       
            $apply_zaznamy=mysqli_query($connect,$query_zaznamy);
            unset($_POST['fromDate']);
        }
        
        if(isset($_POST['toDate'])){
            $query_zaznamy="UPDATE slides SET toDate='".$_POST['toDate']."' WHERE ID='".$_POST['id_prispevku']."'";       
            $apply_zaznamy=mysqli_query($connect,$query_zaznamy);
            unset($_POST['toDate']);
        }
        
        if(isset($_POST['duration'])){
            $query_zaznamy="UPDATE slides SET duration='".($_POST['duration']*1000)."' WHERE ID='".$_POST['id_prispevku']."'";       
            $apply_zaznamy=mysqli_query($connect,$query_zaznamy);
            unset($_POST['duration']);
        }
        
    }
    
    if(isset($_POST['cancel'])){
    
            unset($_POST['fromDate']);
            unset($_POST['toDate']);   
            unset($_POST['duration']); 
        
    }
}

function insertContribution(){
    global $connect;
    if(isset($_POST['insert'])){
        
	    $autor=$_SESSION['user_id'];
        $from="'".date('Y-m-d')."'";
        $to="NULL";
        $errors= array();
        $imageUploaded=false;
        $file_name = $_FILES['image']['name'];
        $file_size =$_FILES['image']['size'];
        $file_tmp =$_FILES['image']['tmp_name'];
        $file_type=$_FILES['image']['type'];
        $imageName=explode('.',$_FILES['image']['name']);
        $file_ext=strtolower(end($imageName));
        $target_file="slides/".url_slug($imageName[0]);
        $indexOfNamePart=1;
        
        
        $check = getimagesize($file_tmp);
        
        if($check === false) {
            $errors[]='<div class="alert alert-danger">Prosím nahrávajte iba obrázky.</div>';
        } 

        
        /*Ak je v priecinku obrazok s rovnakym menom tak ho premenuje*/
        while($indexOfNamePart < sizeof($imageName)-1){
            $target_file.=".".$imageName[$indexOfNamePart];
            $indexOfNamePart++;
        }
      
        $expensions= array("jpeg","jpg","png","gif");
        
        while (file_exists($target_file.".".$file_ext)) {
            $target_file.="1";
        }
            
        $target_file.=".".$file_ext;
        
        
        /*Kontrola ci nahraty subor je v pozadovanom formate*/
        if(in_array($file_ext,$expensions)=== false){
            $errors[]='<div class="alert alert-danger">Tento typ súboru nie je podporovaný. Zvoľte prosím niektorý z formátov JPEG, JPG, PNG, alebo GIF.</div>';
        }
      
        /*Kontrola velkosti obrazku max 30MB*/
        if($file_size > 30097152){
            $errors[]= '<div class="alert alert-danger">Obrázok je príliš veľký. Maximálna veľkosť obrázku je 30MB.</div>';
        }
        
        if((!empty($_POST['dateFrom']) && !empty($_POST['toDate'])) && ($_POST['dateFrom']>$_POST['toDate'])){
            $errors[]= '<div class="alert alert-danger">Dátum začiatku vysielania nesmie byť vyšší ako dátum ukončenia vysielania.</div>';
        }
        /*Ak nenastala ziadna chyba nahraju sa data do databazy a uploadne sa subor*/
        if(empty($errors)==true){
            if(isset($_POST['period'])){
                if(!empty($_POST['dateFrom'])){
                    $from="'".$_POST['dateFrom']."'";
                }
                if(!empty($_POST['toDate'])){
                    $to="'".$_POST['toDate']."'";
                }
            }
            
          $query_insert="INSERT INTO slides(path,fromUser,fromDate,toDate,uploadDate,duration,refresh)                          VALUES('".$target_file."','".$autor."',".$from.",".$to.",CURDATE(),'".($_POST['duration']*1000)."','1')";
	        $apply_insert=mysqli_query($connect,$query_insert);
    
            if($apply_insert){
                $imageUploaded = move_uploaded_file($file_tmp,$target_file);
            }
            
        }else{/*Inak sa zobrazia chybove hlasky*/
            $indexOfError=0;
            while($indexOfError<sizeof($errors)){
                echo $errors[$indexOfError];
                $indexOfError++;
            }
        }
        
	    if($apply_insert && $imageUploaded){
            echo '<div class="alert alert-success">Údaje boli zaznamenané.</div>';
	    }else{
            if($apply_insert){
                $query_insert="DELETE FROM slides WHERE path = '".$target_file."'";
	            $apply_insert=mysqli_query($connect,$query_insert);    
            }
            echo '<div class="alert alert-danger">Údaje sa nepodarilo zaznamenať.</div>';}
    }
}

function checkImage(){
        
    $neededDatas=[$file_tmp,$target_file];
    return $neededDatas;
}


function extractNameOfImage($path){
    $nameOfContribution=explode('slides/',$path);
    return end($nameOfContribution);
}

function showContribution(){
    global $connect;
    $query = "SELECT slides.*, user.fullname as author FROM slides INNER JOIN user ON slides.fromUser=user.id WHERE slides.ID = '".$_POST['id_prispevku']."'";
    $result = mysqli_query($connect,$query);
    $row = mysqli_fetch_assoc($result);
    return $row;
}

function deleteContribution(){
    global $connect;
    if(isset($_POST['delete'])){    
        
        $query = "SELECT path FROM slides WHERE ID = '".$_POST['id_prispevku']."'";
        $apply_zaznamy=mysqli_query($connect,$query);
        $result_zaznamy=mysqli_fetch_array($apply_zaznamy);
        $fileDeleted = unlink($result_zaznamy['path']);
        
        $query = "UPDATE slides SET refresh='1'";
        mysqli_query($connect,$query);
        
        $query = "DELETE FROM slides WHERE ID = '".$_POST['id_prispevku']."'";
        $result = mysqli_query($connect,$query);
        
            if(!$result && ! $fileDeleted){
                echo '<div class="alert alert-danger">Údaje sa nepodarilo vymazať.</div>';
            }else{
                echo '<div class="alert alert-success">Údaje boli úspešne vymazané.</div>';
            }
     
    }
}

/*
SEO URL
*/
function url_slug($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
	$defaults = array(
		'delimiter' => '-',
		'limit' => null,
		'lowercase' => true,
		'replacements' => array(),
		'transliterate' => true,
	);
	
	// Merge options
	$options = array_merge($defaults, $options);
	
	$char_map = array(
		// Latin
		'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
		'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
		'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
		'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
		'ß' => 'ss', 
		'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
		'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
		'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
		'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
		'ÿ' => 'y',

		// Latin symbols
		'©' => '(c)',

		// Greek
		'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
		'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
		'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
		'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
		'Ϋ' => 'Y',
		'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
		'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
		'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
		'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
		'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

		// Turkish
		'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
		'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 

		// Russian
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
		'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
		'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
		'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
		'Я' => 'Ya',
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
		'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
		'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
		'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
		'я' => 'ya',

		// Ukrainian
		'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
		'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

		// Czech
		'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
		'Ž' => 'Z', 
		'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
		'ž' => 'z', 

		// Polish
		'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
		'Ż' => 'Z', 
		'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
		'ż' => 'z',

		// Latvian
		'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
		'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
		'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
		'š' => 's', 'ū' => 'u', 'ž' => 'z'
	);
	
	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);
	
	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_fullname($id,$connect){
$query_getfullname="SELECT id,fullname FROM user WHERE id=".$id;
$apply_getfullname=mysqli_query($connect,$query_getfullname);
$result_getfullname=mysqli_fetch_array($apply_getfullname);
return $result_getfullname['fullname'];
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_url_fullname($id,$connect){
$query_geurltfullname="SELECT id,url_fullname FROM user WHERE id=".$id;
$apply_geurltfullname=mysqli_query($connect,$query_geurltfullname);
$result_geurltfullname=mysqli_fetch_array($apply_geurltfullname);
return $result_geurltfullname['url_fullname'];
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function date_month_short($datum){
$month=substr($datum, 5, 2);
if($month=="01"){$month_short="Jan";}
elseif($month=="02"){$month_short="Feb";}
elseif($month=="03"){$month_short="Mar";}
elseif($month=="04"){$month_short="Apr";}
elseif($month=="05"){$month_short="Máj";}
elseif($month=="06"){$month_short="Jún";}
elseif($month=="07"){$month_short="Júl";}
elseif($month=="08"){$month_short="Aug";}
elseif($month=="09"){$month_short="Sep";}
elseif($month=="10"){$month_short="Okt";}
elseif($month=="11"){$month_short="Nov";}
elseif($month=="12"){$month_short="Dec";}
echo $month_short;
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function date_nice_format($datum){
$day=substr($datum, 8, 2);
$month=substr($datum, 5, 2);
$year=substr($datum, 0, 4);
if($month=="01"){$month="Január";}
elseif($month=="02"){$month="Február";}
elseif($month=="03"){$month="Marec";}
elseif($month=="04"){$month="Apríl";}
elseif($month=="05"){$month="Máj";}
elseif($month=="06"){$month="Jún";}
elseif($month=="07"){$month="Júl";}
elseif($month=="08"){$month="August";}
elseif($month=="09"){$month="Septemper";}
elseif($month=="10"){$month="Október";}
elseif($month=="11"){$month="November";}
elseif($month=="12"){$month="December";}
echo $day.". ".$month." ".$year;
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function date_month($datum){
$month=substr($datum, 5, 2);
if($month=="01"){$month="Január";}
elseif($month=="02"){$month="Február";}
elseif($month=="03"){$month="Marec";}
elseif($month=="04"){$month="Apríl";}
elseif($month=="05"){$month="Máj";}
elseif($month=="06"){$month="Jún";}
elseif($month=="07"){$month="Júl";}
elseif($month=="08"){$month="August";}
elseif($month=="09"){$month="Septemper";}
elseif($month=="10"){$month="Október";}
elseif($month=="11"){$month="November";}
elseif($month=="12"){$month="December";}
echo $month;
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// pr.:    makeThumbnails("images/articles/thumbnail/", "images/articles/photo/".$photo_name, $photo_name,$MaxWe=200,$MaxHe=150);
 function makeThumbnails($dest,$file,$nw,$nh){
 
/* Opening the thumbnail directory and looping through all the thumbs: */

 
$allowed_types=array('jpg','jpeg','gif','png');
$file_parts=array();
$ext='';


 
    $file_parts = explode('.',$file);    //This gets the file name of the images
    $ext = strtolower(array_pop($file_parts));
 
    /* Using the file name (withouth the extension) as a image title: */
    $title = implode('.',$file_parts);
    $title = htmlspecialchars($title);
 
    /* If the file extension is allowed: */
    if(in_array($ext,$allowed_types))
    {
 
        /* If you would like to inpute images into a database, do your mysql query here */
 
        /* The code past here is the code at the start of the tutorial */
        /* Outputting each image: */

        $source = $file;
        $stype = explode(".", $source);
        $stype = $stype[count($stype)-1];
       
 
        $size = getimagesize($source);
        $w = $size[0];
        $h = $size[1];
 
        switch($stype) {
            case 'gif':
                $simg = imagecreatefromgif($source);
                break;
            case 'jpg':
                $simg = imagecreatefromjpeg($source);
                break;
            case 'png':
                $simg = imagecreatefrompng($source);
                break;
        }
 
        $dimg = imagecreatetruecolor($nw, $nh);
        $wm = $w/$nw;
        $hm = $h/$nh;
        $h_height = $nh/2;
        $w_height = $nw/2;
 
        if($w> $h) {
            $adjusted_width = $w / $hm;
            $half_width = $adjusted_width / 2;
            $int_width = $half_width - $w_height;
            imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
        } elseif(($w <$h) || ($w == $h)) {
            $adjusted_height = $h / $wm;
            $half_height = $adjusted_height / 2;
            $int_height = $half_height - $h_height;
 
            imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
        } else {
            imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
        }
            imagejpeg($dimg,$dest,100);
        }


 }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_category_seoname($id,$connect){
$query_category_seoname="SELECT id,category_seoname FROM categories WHERE id=".$id;
$apply_category_seoname=mysqli_query($connect,$query_category_seoname);
$result_category_seoname=mysqli_fetch_array($apply_category_seoname);
return $result_category_seoname['category_seoname'];
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_category_name($id,$connect){
$query_category_name="SELECT id,category_name FROM categories WHERE id=".$id;
$apply_category_name=mysqli_query($connect,$query_category_name);
$result_category_name=mysqli_fetch_array($apply_category_name);
return $result_category_name['category_name'];
}
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_category_name_from_seoname($seoname,$connect){
$query_category_name="SELECT category_name,category_seoname FROM categories WHERE category_seoname='".$seoname."'";
$apply_category_name=mysqli_query($connect,$query_category_name);
$result_category_name=mysqli_fetch_array($apply_category_name);
if(count($result_category_name)!=0){
return $result_category_name['category_name'];
}
else{return $najnovsie="Najnovšie fotografie"; }
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 function pagination($query, $per_page = 10,$page = 1, $url='?',$connect){         
    	$query = "SELECT COUNT(*) as `num` FROM {$query}";
		
    	$row = mysqli_fetch_array(mysqli_query($connect,$query));
    	$total = $row['num'];
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination pagination-lg'>";
                    $pagination .= "";
					if($page<2){
					$pagination.= "<li><a>Predchádzajúca</a></li>";
					}
					else{
					$pagination.= "<li><a href='{$url}/$prev'>Predchádzajúca</a></li>";
					}
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='active'><a>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='{$url}/$counter'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				$pagination.= "<li><a href='{$url}/$lpm1'>$lpm1</a></li>";
    				$pagination.= "";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='{$url}/1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}/2'>2</a></li>";
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}/$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				$pagination.= "<li><a href='{$url}/$lpm1'>$lpm1</a></li>";
    				$pagination.= "";		
    			}
    			else
    			{
    				$pagination.= "<li><a href='{$url}/1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}/2'>2</a></li>";
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}/$counter'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li><a href='{$url}/$next'>Ďalšia</a></li>";
                $pagination.= "";
    		}else{
    			$pagination.= "<li><a>Ďalšia</a></li>";
                $pagination.= "";
            }
    		$pagination.= "</ul>\n";		
    	}
    
    
        return $pagination;
    } 
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function pagination_search($query, $per_page = 10,$page = 1, $url='?',$connect){         
    	$query = "SELECT COUNT(*) as `num` FROM {$query}";
		
    	$row = mysqli_fetch_array(mysqli_query($connect,$query));
    	$total = $row['num'];
        $adjacents = "2"; 

    	$page = ($page == 0 ? 1 : $page);  
    	$start = ($page - 1) * $per_page;								
		
    	$prev = $page - 1;							
    	$next = $page + 1;
        $lastpage = ceil($total/$per_page);
    	$lpm1 = $lastpage - 1;
    	
    	$pagination = "";
    	if($lastpage > 1)
    	{	
    		$pagination .= "<ul class='pagination pagination-lg'>";
                    $pagination .= "";
					if($page<2){
					$pagination.= "<li><a>Predchádzajúca</a></li>";
					}
					else{
					$pagination.= "<li><a href='{$url}$prev'>Predchádzajúca</a></li>";
					}
    		if ($lastpage < 7 + ($adjacents * 2))
    		{	
    			for ($counter = 1; $counter <= $lastpage; $counter++)
    			{
    				if ($counter == $page)
    					$pagination.= "<li class='active'><a>$counter</a></li>";
    				else
    					$pagination.= "<li><a href='{$url}$counter'>$counter</a></li>";					
    			}
    		}
    		elseif($lastpage > 5 + ($adjacents * 2))
    		{
    			if($page < 1 + ($adjacents * 2))		
    			{
    				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				$pagination.= "<li><a href='{$url}$lpm1'>$lpm1</a></li>";
    				$pagination.= "";		
    			}
    			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
    			{
    				$pagination.= "<li><a href='{$url}1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}2'>2</a></li>";
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}$counter'>$counter</a></li>";					
    				}
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				$pagination.= "<li><a href='{$url}$lpm1'>$lpm1</a></li>";
    				$pagination.= "";		
    			}
    			else
    			{
    				$pagination.= "<li><a href='{$url}1'>1</a></li>";
    				$pagination.= "<li><a href='{$url}2'>2</a></li>";
    				$pagination.= "<li class='dot'><a>..</a></li>";
    				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
    				{
    					if ($counter == $page)
    						$pagination.= "<li class='active'><a>$counter</a></li>";
    					else
    						$pagination.= "<li><a href='{$url}$counter'>$counter</a></li>";					
    				}
    			}
    		}
    		
    		if ($page < $counter - 1){ 
    			$pagination.= "<li><a href='{$url}$next'>Ďalšia</a></li>";
                $pagination.= "";
    		}else{
    			$pagination.= "<li><a>Ďalšia</a></li>";
                $pagination.= "";
            }
    		$pagination.= "</ul>\n";		
    	}
    
    
        return $pagination;
    } 
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//***********************************************************************************************************
function get_metatags($modul,$param,$connect){
	$parameter = explode("/", $param);
	$query_menu="SELECT * FROM menu WHERE module_filename='".$modul."'";
	$apply_menu=mysqli_query($connect,$query_menu);
	$result_menu=mysqli_fetch_array($apply_menu);
	$title="<title>".$result_menu['name']." | Investcom.sk</title>
	";
	$og_url='<meta property="og:url" content="http://investcom.pozri-sa.sk/'.$param.'" />
	';
	$og_type='<meta property="og:type" content="article" />
	';
	$og_title='<meta property="og:title" content="'.$result_menu['name'].' | Investcom.sk" />
	';
	$og_description='<meta property="og:description" content="Investcom.sk - portál pre Vaše podnikanie. Články, služby a inzercia v oblasti podnikania" />
	';
	$og_image='<meta property="og:image" content="http://investcom.pozri-sa.sk/img/logo.png" />
	';
	if($result_menu['seo_name']=="clanok"){
		$seotitle=$parameter[2];
		$id=$parameter[3];
		$query_article="SELECT id,title,seo_title,article,main_photo,published FROM articles WHERE seo_title='".$seotitle."' AND id='".$id."' AND published='1'";
		$apply_article=mysqli_query($connect,$query_article);
		$result_article=mysqli_fetch_array($apply_article);
		$title="<title>".$result_article['title']." | Investcom.sk</title>";
		$og_title='<meta property="og:title" content="'.$result_article['title'].' | Investcom.sk" />
		';
		
		$og_image='<meta property="og:image" content="http://investcom.pozri-sa.sk/images/articles/photo/'.$result_article['main_photo'].'" />';
	}
	if($result_menu['seo_name']=="sluzby/detail"){
		$seotitle=$parameter[3];
		$id=$parameter[4];
		$query_article="SELECT id,title,seo_title,main_photo,published FROM services WHERE seo_title='".$seotitle."' AND id='".$id."' AND published='1'";
		$apply_article=mysqli_query($connect,$query_article);
		$result_article=mysqli_fetch_array($apply_article);
		$title="<title>".$result_article['title']." | Investcom.sk</title>";
		$og_title='<meta property="og:title" content="'.$result_article['title'].' | Investcom.sk" />
		';
		
		$og_image='<meta property="og:image" content="http://investcom.pozri-sa.sk/images/services/photo/'.$result_article['main_photo'].'" />';
	}
	if($result_menu['seo_name']=="biznis-reality/ponuka"){
		$seotitle=$parameter[3];
		$id=$parameter[4];
		$query_article="SELECT id,title,seo_title,main_photo,published FROM reality WHERE seo_title='".$seotitle."' AND id='".$id."' AND published='1'";
		$apply_article=mysqli_query($connect,$query_article);
		$result_article=mysqli_fetch_array($apply_article);
		$title="<title>".$result_article['title']." | Investcom.sk</title>";
		$og_title='<meta property="og:title" content="'.$result_article['title'].' | Investcom.sk"/>
		';
		
		$og_image='<meta property="og:image" content="http://investcom.pozri-sa.sk/images/reality/photo/'.$result_article['main_photo'].'"/>';
	}
	if($result_menu['seo_name']=="inzercia/inzerat"){
		$seotitle=$parameter[3];
		$id=$parameter[4];
		$query_article="SELECT id,title,seo_title,main_photo,published FROM advertisements WHERE seo_title='".$seotitle."' AND id='".$id."' AND published='1'";
		$apply_article=mysqli_query($connect,$query_article);
		$result_article=mysqli_fetch_array($apply_article);
		$title="<title>".$result_article['title']." | Investcom.sk</title>
		";
		$og_title='<meta property="og:title" content="'.$result_article['title'].' | Investcom.sk"/>
		';
		
		$og_image='<meta property="og:image" content="http://investcom.pozri-sa.sk/images/advertising/photo/'.$result_article['main_photo'].'"/>';
	}
	echo $title;
	echo $og_url;
	echo $og_type;
	echo $og_title;
	echo $og_description;
	echo $og_image;
}
//******************************************************************

function count_visits($parameter,$connect){
$query_count_views="SELECT COUNT(*) FROM page_views WHERE url='".$parameter."'";
$apply_count_views=mysqli_query($connect,$query_count_views);
$result_count_views=mysqli_fetch_array($apply_count_views);
echo $result_count_views[0];
}
function count_votes($parameter,$connect){
$query_count_votes="SELECT COUNT(*) FROM votes WHERE photo_id='".$parameter."'";
$apply_count_votes=mysqli_query($connect,$query_count_votes);
$result_count_votes=mysqli_fetch_array($apply_count_votes);
echo $result_count_votes[0];
}
function count_downloads($parameter,$connect){
$query_count_downloads="SELECT COUNT(*) FROM downloads WHERE photo_id='".$parameter."'";
$apply_count_downloads=mysqli_query($connect,$query_count_downloads);
$result_count_downloads=mysqli_fetch_array($apply_count_downloads);
echo $result_count_downloads[0];
}
function pf_email($email_to,$email_from,$msg){
	return true;
}
?>