<div class="page-content">
<?php
  
    include "functions.php";
    deleteContribution();
    updateContribution();
    
    


?>
<div class="portlet box blue ">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-search"></i> Vyhľadávanie
											</div>
                                      
                                    </div>
                                    <div class="portlet-body form">
                                        <form class="form-horizontal" role="form" method="get" enctype="multipart/form-data" accept-charset="utf-8">
                                            <div class="form-body">
                                                <div class="form-group">
												     <input type="hidden" name="modul" value="spravovat-prispevky/zaznamy">	
													
                                                    <div class="col-md-2">
                                                        Autor:
                                                        <input class="form-control" type="text"  name="authorName" placeholder="Meno" value="<?php echo $_GET['authorName']; ?>">	
                                                        <input class="form-control" type="text"  name="authorSurname" placeholder="Priezvisko" value="<?php echo $_GET['authorSurname']; ?>">
                                                        <input class="form-control" type="text"  name="osobne_cislo" placeholder="Osobné číslo" value="<?php echo $_GET['osobne_cislo']; ?>">	
													</div>
                                                   
                                                    <div class="col-md-2">
                                                        Stav vysielania:
                                                        <select name="status" class="form-control">
                                                        
                                                            <option value="" >Stav príspevku</option>
                                                            <option value="ukoncene" <?php echo ($_GET['status'] == "ukoncene")? 'selected':'' ?>>Ukončené</option>
                                                            <option value="aktualne" <?php echo ($_GET['status'] == "aktualne")? 'selected':'' ?>>Aktuálne</option>
                                                            <option value="pripravene" <?php echo ($_GET['status'] == "pripravene")? 'selected':'' ?>>Pripravené</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                    Zoradiť podľa:
                                                    <select name="sorting" class="form-control">
                                                        
                                                        <option value="">Zoradiť podľa</option>
                                                        <option value="ID" <?php echo ($_GET['sorting'] == "ID")? 'selected':'' ?>>ID Príspevku</option>
                                                        <option value="uploadDate" <?php echo ($_GET['sorting'] == "uploadDate")? 'selected':'' ?>>Dátum pridania príspevku</option>
                                                    </select>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                    <br>
													    <input type="radio" name="srt" value="vzostupne" <?php echo ($_GET['srt'] != "zostupne")? 'checked':''?>> Vzostupne
                                                        <input type="radio" name="srt" value="zostupne" <?php echo ($_GET['srt'] == "zostupne")? 'checked':''?>> Zostupne
                   
													</div>
                                                    
                                                </div>                                              
                                            </div>
                                            
                                                <div class="form-actions right1">
                                                   <button type="submit" class="btn blue">Vyhľadať</button>
                                                </div>
                                        </form>
                                    </div>
                                </div>
                                
                                                                

 <div class="portlet box blue">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-desktop" aria-hidden="true"></i> Príspevky</div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="Zbaliť/Rozbaliť" title=""> </a>
                                            
                                        </div>
                                    </div>
                                    
                                    <?php 
                                             
                                        if(!empty($_GET['authorName']) || !empty($_GET['authorSurname']) || !empty($_GET['status']) || !empty($_GET['sorting']) || !empty($_GET['osobne_cislo'])){
                                            
                                            $authorName = mysqli_real_escape_string($connect,$_GET['authorName']); 
                                            $authorSurname = mysqli_real_escape_string($connect,$_GET['authorSurname']); 
                                            $possiblesAuthors=array();   
                                                  
                                            if(!empty($_GET['authorName']) && !empty($_GET['authorSurname'])){
                                                $queryAuthor="SELECT osobne_cislo FROM `employees` WHERE menoBD LIKE LOWER('%".$authorName."%') AND priezviskoBD LIKE LOWER('".$authorSurname."')";
                                                $applyAuthor=mysqli_query($connectToMembers,$queryAuthor);
                                                while($result_zaznamy=mysqli_fetch_array($applyAuthor)){
                                                    array_push($possiblesAuthors,$result_zaznamy['osobne_cislo']);
                                                }
                                            }elseif(!empty($_GET['authorName'])){
                                                $queryAuthor="SELECT osobne_cislo FROM `employees` WHERE menoBD LIKE LOWER('%".$authorName."%')";
                                                $applyAuthor=mysqli_query($connectToMembers,$queryAuthor);
                                                while($result_zaznamy=mysqli_fetch_array($applyAuthor)){
                                                    array_push($possiblesAuthors,$result_zaznamy['osobne_cislo']);
                                                }
                                            }elseif(!empty($_GET['authorSurname'])){
                                                $queryAuthor="SELECT osobne_cislo FROM `employees` WHERE priezviskoBD LIKE LOWER('%".$authorSurname."%')";
                                                $applyAuthor=mysqli_query($connectToMembers,$queryAuthor);
                                                while($result_zaznamy=mysqli_fetch_array($applyAuthor)){
                                                    array_push($possiblesAuthors,$result_zaznamy['osobne_cislo']);
                                                }
                                            }
                                            $conditions=array();
                                        
                                    ?>
                                    <div class="portlet-body">
									                                         <div class="table-responsive">
                                            <table class="table table-bordered">
                                               <thead>
                                                    <tr>
														<th>ID</th>
												        <th>Názov</th>
												        <th>Pridal</th>
												        <th>Začiatok vysielania</th>
												        <th>Koniec vysielania</th>
                                                        <th>Doba trvania (v sekundách)</th>
								                        <th>Dátum pridania</th>
                                                        <th>Stav vysielania</th>
														  
                                                    </tr>
                                                </thead>
                                                <tbody>
												<?php
                                                $page = (int) (!isset($parameter[2]) ? 1 : $parameter[2]);
                                                $limit = 10;
                                                $url="?authorName=".$_GET['authorName']."&authorSurname=".$_GET['authorSurname']."&osobne_cislo=".$_GET['osobne_cislo']."&status=".$_GET['status']."&sorting=".$_GET['sorting']."&srt=".$_GET['srt']."&modul=spravovat-prispevky/zaznamy/";
                                                $startpoint = ($page * $limit) - $limit;
                                                $c=$connect;    
                                            
												$searchQuery="SELECT * FROM slides ";          
                                            
                                                if(!empty($possiblesAuthors)){
                                                    $indexOfAuthor=0;
                                                    $authors="('";
                                                    while($indexOfAuthor<count($possiblesAuthors)){
                                                        $authors.=$possiblesAuthors[$indexOfAuthor]."'";
                                                        if($indexOfAuthor<(count($possiblesAuthors)-1)){
                                                            $authors.=",'";
                                                        }
                                                        $indexOfAuthor++;
                                                    }
                                                    $authors.=")";
                                                    
                                                    $conditions[]="fromUser IN ".$authors;
                                                }
                                            
                                                if($_GET['status']=="ukoncene"){
                                                    $conditions[]="toDate < CURDATE()";
                                                    $conditions[]="toDate IS NOT NULL";
                                                }elseif($_GET['status']=="pripravene"){
                                                    $conditions[]="fromDate > CURDATE()";
                                                }elseif($_GET['status']=="aktualne"){
                                                    $conditions[]="fromDate <= CURDATE()";
                                                    $conditions[]="(toDate >= CURDATE() OR toDate IS NULL)";
                                                }
                                            
                                                if(!empty($_GET['osobne_cislo'])){
                                                    $conditions[]="fromUser LIKE '%".$_GET['osobne_cislo']."%'";
                                                }
                                            
                                                $sql=$searchQuery;  
                                                $sqlForPaging="";
                                            
                                                if(count($conditions)>0){
                                                    $sql.="WHERE ".implode(' AND ',$conditions);
                                                    $sqlForPaging.="WHERE ".implode(' AND ',$conditions); 
                                                }
                                            
                                                if(!empty($_GET['sorting'])){
                                                    $sql.="ORDER BY ".$_GET['sorting'];
                                                    $sqlForPaging.="ORDER BY ".$_GET['sorting'];
                                                }
                                            
                                                if($_GET['srt']=='zostupne'){
                                                    $sql.=" DESC";
                                                    $sqlForPaging.=" DESC";
                                                }
                                                
                                                $sql.="LIMIT $startpoint, $limit";     
                                            
												$apply_zaznamy=mysqli_query($connect,$sql);
												while($result_zaznamy=mysqli_fetch_array($apply_zaznamy)){
                                                $query_author="SELECT meno, priezvisko FROM employees WHERE osobne_cislo = '".$result_zaznamy['fromUser']."'";
												$apply_author=mysqli_query($connectToMembers,$query_author);
                                                $result_author=mysqli_fetch_array($apply_author);
                                                ?>
												<tr>
                                                        
														<td> <?php echo $result_zaznamy['ID']; ?></td>
														<td> <?php echo extractNameOfImage($result_zaznamy['path']); ?></td>
														<td> <?php echo $result_author['meno']." ".$result_author['priezvisko']; ?></td>
														
														<form method="post" id="sampleForm" class="sampleForm">
														    <input type="hidden" name="id_prispevku" value="<?php echo $result_zaznamy['ID'] ?>">
														    <td>
                                                               <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                                   <input type="date" name="fromDate" value="<?php echo $result_zaznamy['fromDate']; ?>" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#updateModal">
                                                               <?php }else{ echo $result_zaznamy['fromDate'];} ?>
                                                            </td>
                                                    	    <td>
                                                                <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                    	            <input type="date" name="toDate" value="<?php echo $result_zaznamy['toDate']; ?>" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#update">
                                                    	        <?php }else{ echo $result_zaznamy['fromDate'];} ?>
                                                    	    </td>
													    
													    <td> 
                                                        <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                        <select name="duration" class="form-control" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#updateModal">
                                                           <option value="<?php echo $result_zaznamy['duration']/1000 ?>"><?php echo $result_zaznamy['duration']/1000 ?></option>
                                                            <?php 
                                                                $seconds=5;
                                                                for($seconds;$seconds<61;$seconds+=5){
                                                            ?>
                                                            <option value="<?php echo $seconds; ?>" ><?php echo $seconds?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <?php }else{ echo $result_zaznamy['duration'];} ?>
                                                        </td>
                                                        </form>
                                                        <td> <?php echo $result_zaznamy['uploadDate'] ?> </td>
                                                        <td> <?php 
                                                                if($result_zaznamy['toDate']<date("Y-m-d") && $result_zaznamy['toDate']!=null){
                                                                    echo '<i style="color:red;font-size:15px;font-weight: bold;">
                                                                    Ukončené </i> ';
                                                                }elseif($result_zaznamy['fromDate']>date("Y-m-d")){
                                                                    echo '<i style="color:blue;font-size:15px;font-weight: bold;">
                                                                    Pripravené </i> ';
                                                                }else{
                                                                    echo '<i style="color:#32CD32;font-size:15px;font-weight: bold;">
                                                                    Aktuálne </i> ';
                                                                }
                                                            ?></td>
                                                        <td><?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']){ ?>
                                                            <form method="post"> 
                                                                
                                                                <input type="hidden" name="id_prispevku" value="<?php echo $result_zaznamy['ID'] ?>">
                                                                <a type="button" class="btn"  title="Vymazať príspevok" data-toggle="modal" data-target="#deleteModal" name="forDelete"   onclick="location.href='index.php?modul=spravovat-prispevky/zaznamy&id_prispevku=<?php echo $result_zaznamy['ID'] ?>';" ><i class="fa fa-trash"></i></a>
                                                                
                                                            
                                                            <?php } 
                                                            $query="SELECT path FROM slides WHERE ID = '".$result_zaznamy['ID']."'";
                                                            $apply=mysqli_query($connect,$query);
                                                            $result=mysqli_fetch_array($apply);
                                                            $file=$result['path'];?>
                                                              
                                                             
                                                                    <a href="<?php echo $file; ?>" download class="btn" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                                    <a href="<?php echo $file; ?>" class="btn" title="Náhľad" target="_blank"><i class="fa fa-desktop" aria-hidden="true"></i></a>
                                                                </form>
                                                        </td>
                                                       
                                                    </tr>
												<?php } ?>	
													
                                                </tbody>
                                            </table>
											<?php	echo "<center>".pagination_search("slides ".$sqlForPaging,$limit,$page,$url,$c)."</center>"; ?>
															
                                        </div>
								
                                       
									
                                    </div>
                                    <?php }else{ 
     /**********************************************************************************************************************************************/
     ?>
                                    
                                    
                                    <div class="portlet-body">
									                                         <div class="table-responsive">
                                            <table class="table table-bordered">
                                               <thead>
                                                    <tr>
														<th>ID</th>
												        <th>Názov</th>
												        <th>Pridal</th>
												        <th>Začiatok vysielania</th>
												        <th>Koniec vysielania</th>
                                                        <th>Doba trvania (v sekundách)</th>
								                        <th>Dátum pridania</th>
                                                        <th>Stav vysielania</th>
														  
                                                    </tr>
                                                </thead>
                                                <tbody>
												<?php
                                                $page = (int) (!isset($parameter[2]) ? 1 : $parameter[2]);
                                                $limit = 10;
                                                $url="?modul=spravovat-prispevky/zaznamy";
                                                $startpoint = ($page * $limit) - $limit;
                                                $c=$connect;
                                            
												$query_zaznamy="SELECT * FROM slides ORDER BY id DESC LIMIT $startpoint, $limit";                    
												$apply_zaznamy=mysqli_query($connect,$query_zaznamy);
												while($result_zaznamy=mysqli_fetch_array($apply_zaznamy)){
                                                $query_author="SELECT meno, priezvisko FROM employees WHERE osobne_cislo = '".$result_zaznamy['fromUser']."'";
												$apply_author=mysqli_query($connectToMembers,$query_author);
                                                $result_author=mysqli_fetch_array($apply_author);
                                                ?>
												<tr>
                                                        
														<td> <?php echo $result_zaznamy['ID']; ?></td>
														<td> <?php echo extractNameOfImage($result_zaznamy['path']); ?></td>
														<td> <?php echo $result_author['meno']." ".$result_author['priezvisko']; ?></td>
														
														<form method="post" id="sampleForm" class="sampleForm">
														    <input type="hidden" name="id_prispevku" value="<?php echo $result_zaznamy['ID'] ?>">
														    <td>
                                                               <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                                   <input type="date" name="fromDate" value="<?php echo $result_zaznamy['fromDate']; ?>" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#updateModal" min="<?php echo date('Y-m-d'); ?>" max="<?php echo $result_zaznamy['toDate'] ?>">
                                                               <?php }else{ echo $result_zaznamy['fromDate'];} ?>
                                                            </td>
                                                    	    <td>
                                                                <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                    	            <input type="date" name="toDate" value="<?php echo $result_zaznamy['toDate']; ?>" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#update" min="<?php echo $result_zaznamy['fromDate'] ?>">
                                                    	        <?php }else{ echo $result_zaznamy['fromDate'];} ?>
                                                    	    </td>
													    
													    
                                                        <td> 
                                                        <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']) {?>
                                                        <select name="duration" class="form-control" style="border-style: none;" onchange='this.form.submit()' data-toggle="modal" data-target="#updateModal">
                                                           <option value="<?php echo $result_zaznamy['duration']/1000 ?>"><?php echo $result_zaznamy['duration']/1000 ?></option>
                                                            <?php 
                                                                $seconds=5;
                                                                for($seconds;$seconds<61;$seconds+=5){
                                                            ?>
                                                            <option value="<?php echo $seconds; ?>" ><?php echo $seconds?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <?php }else{ echo $result_zaznamy['duration'];} ?>
                                                        </td>
                                                        </form>
                                                        <td> <?php echo $result_zaznamy['uploadDate'] ?> </td>
                                                        <td> <?php 
                                                                if($result_zaznamy['toDate']<date("Y-m-d") && $result_zaznamy['toDate']!=null){
                                                                    echo '<i style="color:red;font-size:15px;font-weight: bold;">
                                                                    Ukončené </i> ';
                                                                }elseif($result_zaznamy['fromDate']>date("Y-m-d")){
                                                                    echo '<i style="color:blue;font-size:15px;font-weight: bold;">
                                                                    Pripravené </i> ';
                                                                }else{
                                                                    echo '<i style="color:#32CD32;font-size:15px;font-weight: bold;">
                                                                    Aktuálne </i> ';
                                                                }
                                                            ?></td>
                                                        <td>
                                                          <form method="post"> 
                                                              <input type="hidden" name="id_prispevku" value="<?php echo $result_zaznamy['ID'] ?>">
                                                                   <?php if($user->isAdmin() || $_SESSION['user_id']==$result_zaznamy['fromUser']){ ?>
                                                                        <a type="button" class="btn"  title="Vymazať príspevok" data-toggle="modal" data-target="#deleteModal" name="forDelete"   onclick="location.href='index.php?modul=spravovat-prispevky/zaznamy&id_prispevku=<?php echo $result_zaznamy['ID'] ?>';" ><i class="fa fa-trash"></i></a>
                                                                    <?php } $query="SELECT path FROM slides WHERE ID = '".$result_zaznamy['ID']."'";
        $apply=mysqli_query($connect,$query);
        $result=mysqli_fetch_array($apply);
        $file=$result['path'];?>
                                                              
                                                             
                                                                    <a href="<?php echo $file; ?>" download class="btn" title="Download"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                                    <a href="<?php echo $file; ?>" class="btn" title="Náhľad" target="_blank"><i class="fa fa-desktop" aria-hidden="true"></i></a>
                                                            </form>
                                                        </td>
                                                       
                                                    </tr>
												<?php } ?>	
													
                                                </tbody>
                                            </table>
											<?php	echo "<center>".pagination("slides",$limit,$page,$url,$c)."</center>"; ?>
															
                                        </div>
								
                                       
									
                                    </div>
                                    <?php } ?>
                                </div>
						
 </div>
 
  <?php if(isset($_GET['id_prispevku'])){ ?>
 <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">    
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <div class="modal-body">
            Naozaj chcete odstrániť príspevok č. <?php echo $_GET['id_prispevku'] ?> zo systému?
          </div>
          <div class="modal-footer">
                <form method="post">
                    <input type="hidden" name="id_prispevku" value="<?php echo $_GET['id_prispevku'] ?>">
                    <button type="submit" name="nothing" class="btn btn-secondary" formaction="index.php?modul=spravovat-prispevky/zaznamy">Zrušiť</button>
                    <button type="submit" name="delete" class="btn btn-primary" formaction="index.php?modul=spravovat-prispevky/zaznamy">Vymazať</button>
                </form>
          </div>
    </div>
  </div>
</div>
<?php } ?>
 <?php if(isset($_POST['fromDate']) || isset($_POST['toDate']) || isset($_POST['duration'])){ ?>
 <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">    
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
          <div class="modal-body">
            Naozaj chcete upraviť príspevok č. <?php echo $_POST['id_prispevku'] ?>?
          </div>
          <div class="modal-footer">
                <form method="post">
                    <input type="hidden" name="id_prispevku" value="<?php echo $_POST['id_prispevku'] ?>">
                    <input type="hidden" name="fromDate" value="<?php echo $_POST['fromDate'] ?>">
                    <input type="hidden" name="toDate" value="<?php echo $_POST['toDate'] ?>">
                    <input type="hidden" name="duration" value="<?php echo $_POST['duration'] ?>">
                    <button type="submit" name="cancel" class="btn btn-secondary" formaction="index.php?modul=spravovat-prispevky/zaznamy">Zrušiť</button>
                    <button type="submit" name="update" class="btn btn-primary" formaction="index.php?modul=spravovat-prispevky/zaznamy">Upraviť</button>
                </form>
          </div>
    </div>
  </div>
</div>
<?php } ?>
 <script type="text/javascript">
$(document).ready(function(){
    $('.modal').modal('show');
});
</script>