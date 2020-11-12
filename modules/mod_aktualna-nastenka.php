    
  
   
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet">
    
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  
      
  

 <div class="page-content">

 <div class="portlet box blue">
                                   
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-desktop" aria-hidden="true"></i>Aktuálna nástenka</div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="Zbaliť/Rozbaliť" title=""> </a>
                                            
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                    <?php
                                        $query_zaznamy="SELECT COUNT(ID) as pocet_aktivnych FROM slides WHERE (fromDate IS NULL AND toDate IS NULL) OR (fromDate<=CURDATE() AND toDate IS NULL) OR (fromDate IS NULL AND toDate>=CURDATE()) OR (fromDate<=CURDATE() AND toDate>=CURDATE())";
								        $apply_zaznamy=mysqli_query($connect,$query_zaznamy);
								        $result_zaznamy=mysqli_fetch_array($apply_zaznamy);
                                        $pocetAktivnych=$result_zaznamy['pocet_aktivnych'];
                                        
                                        if($pocetAktivnych>0){ 
                                            $numberOfContribution=1;
                                            ?>
                                            <section class="slide_show">
                                              <div id="home" class="slider">
                                                 <div id="main_slider" class="carousel slide" data-ride="carousel">
                                                    <!-- Indicators -->
                                                        <ol class="carousel-indicators">
                                                          <li data-target="#main_slider" data-slide-to="0" class="active" style=" background:black;"></li>
                                                          <?php while($numberOfContribution<$pocetAktivnych){?>
                                                          <li data-target="#main_slider" data-slide-to="<?php echo $numberOfContribution; ?>" style=" background:black;"></li>
                                                          <?php   $numberOfContribution++; }?>
                                                        </ol>
                                                        <div class="carousel-inner">
                                                           <?php 
                                                                $query_zaznamy="SELECT * FROM slides WHERE (fromDate IS NULL AND toDate IS NULL) OR (fromDate<=CURDATE() AND toDate IS NULL) OR (fromDate IS NULL AND toDate>=CURDATE()) OR (fromDate<=CURDATE() AND toDate>=CURDATE())";
								                                $apply_zaznamy=mysqli_query($connect,$query_zaznamy); 
                                                                $result_zaznamy=mysqli_fetch_array($apply_zaznamy);
                                                            ?>
                                                           <div class="carousel-item active" data-interval="<?php echo $result_zaznamy['duration'] ?>" >
                                                              <img class="d-block w-100" src="<?php echo $result_zaznamy['path']; ?>" alt="slider_img" style="width:1500px; height:800px; margin-left:2% !important;">
                                                           </div>
                                                           <?php 
                                                            while($result_zaznamy=mysqli_fetch_array($apply_zaznamy)){ 
                                                            ?>
                                                           <div class="carousel-item" data-interval="<?php echo $result_zaznamy['duration'] ?>">
                                                              <img class="d-block w-100" src="<?php echo $result_zaznamy['path']; ?>" alt="slider_img" style="width:1500px; height:800px; margin-left:2% !important;">
                                                           </div>
                                                           <?php } ?>
                                                        </div>
                                                        <a class="carousel-control-prev" href="#main_slider" role="button" data-slide="prev">
                                                            <img src="imgs/left.png" alt="#" />
                                                        </a>
                                                        <a class="carousel-control-next" href="#main_slider" role="button" data-slide="next">
                                                            <img src="imgs/right.png" alt="#" />
                                                        </a>
                                                    </div>
                                                 </div>
                                          </section>
                                       <?php }else{
                                        ?>
                                            <div class="container">
                                              <div id="myCarousel" class="carousel slide" data-ride="carousel">
                                                
                                                    <!-- Wrapper for slides -->
                                                <div class="carousel-inner">
                                                  <div class="item active">
                                                    <img src="slides/Default.png" alt="MKEM" style="width:100%; margin:auto;">
                                                  </div>
                                                </div>

                                              </div>
                                            </div>
                  					    <?php }?>
                                    </div>
                                </div>
                               
						
 </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/script.js"></script>
 