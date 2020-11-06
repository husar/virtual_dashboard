
<!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">-->
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
                                        
                                        if($pocetAktivnych>=0){ 
                                            $numberOfContribution=1;
                                            ?>
                                            <div class="container">
                                              <div id="myCarousel" class="carousel slide" data-ride="carousel" data-interval="10000">
                                                <!-- Indicators -->
                                                <ol class="carousel-indicators">
                                                  <li data-target="#myCarousel" data-slide-to="0" class="active" style="zoom: 200%; background:black;"></li>
                                                  <?php while($numberOfContribution<$pocetAktivnych){?>
                                                  <li data-target="#myCarousel" data-slide-to="<?php echo $numberOfContribution; ?>" style="zoom: 200%; background:black;"></li>
                                                  
                                        <?php   $numberOfContribution++; }?>
                                                </ol>

                                                    <!-- Wrapper for slides -->
                                                <div class="carousel-inner">
                                                 <?php 
                                                    $query_zaznamy="SELECT * FROM slides WHERE (fromDate IS NULL AND toDate IS NULL) OR (fromDate<=CURDATE() AND toDate IS NULL) OR (fromDate IS NULL AND toDate>=CURDATE()) OR (fromDate<=CURDATE() AND toDate>=CURDATE())";
								                    $apply_zaznamy=mysqli_query($connect,$query_zaznamy); 
                                                    $result_zaznamy=mysqli_fetch_array($apply_zaznamy);?>
                                                        <div class="item active">
                                                            <img src="<?php echo $result_zaznamy['path']; ?>" style="width:70%; margin:auto;">
                                                        </div>
                                                    <?php while($result_zaznamy=mysqli_fetch_array($apply_zaznamy)){ ?>
                                                  <div class="item">
                                                    <img src="<?php echo $result_zaznamy['path']; ?>" style="width:70%; margin:auto;">
                                                  </div>
                                                    <?php } ?>
      
                                                </div>

                                        <!-- Left and right controls -->

                                                <a class="left carousel-control" href="#myCarousel" data-slide="prev">
                                                  <i class="fa fa-arrow-left" aria-hidden="true" style="margin-top: 150%; zoom: 200%; color:black"></i>
                                                  <span class="sr-only">Previous</span>
                                                </a>
                                                <a class="right carousel-control" href="#myCarousel" data-slide="next">
                                                  <i class="fa fa-arrow-right" aria-hidden="true" style="margin-top: 150%; zoom: 200%; color:black"></i>
                                                  <span class="sr-only">Next</span>
                                                </a>
                                              </div>
                                            </div>
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

 