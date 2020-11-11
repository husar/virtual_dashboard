<div class="page-content">
<?php
    include "functions.php";
    
    insertContribution();
    
?>
 <div class="portlet box blue">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-calendar"></i> Pridať záznam</div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="Zbaliť/Rozbaliť" title=""> </a>
                                            
                                        </div>
                                    </div>
                                    <div class="portlet-body">
									
								
                                       <form class="form-horizontal" role="form" method="POST" action="" enctype="multipart/form-data">
                                            <div class="form-body">
                                                <div class="form-group">
                                                <label class="col-md-3 control-label"></label>
                                                    <div class="col-md-9">
                                                    <input type="checkbox" id="period" name="period" value="1" onclick="showCalendar()" <?php echo $_POST['period']==1?"checked":""?>>
                                                    <label for="period">Vysielať len určité obdobie</label>
													</div>	
                                                </div>
                                               <div id="calendar" style="display:none;">
												<div  class="form-group" >
                                                    <label class="col-md-3 control-label">Začať vysielať od</label>
                                                    <div class="col-md-9">
													
													<input class="form-control" size="16" type="date" name="dateFrom" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $_POST['dateFrom']; ?>" max="<?php echo $_POST['toDate']; ?>">
                                                        
													</div>
														
                                                </div>
                                                
												<div class="form-group">
                                                    <label class="col-md-3 control-label">Ukončiť vysielanie</label>
                                                    <div class="col-md-9">
													
													<input class="form-control" size="16" type="date" name="toDate" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $_POST['toDate']; ?>" min="<?php echo $_POST['fromDate'] ?>">
                                                        
													</div>
														
                                                </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Trvanie vysielania v sekundách (max: 60s)</label>
                                                    <div class="col-md-2">
													
													<select name="duration" class="form-control">
                                                            <?php 
                                                                $seconds=5;
                                                                for($seconds;$seconds<61;$seconds+=5){
                                                            ?>
                                                            <option value="<?php echo $seconds ?>" <?php echo ($_POST['duration'] == $seconds)? 'selected':'' ?>><?php echo $seconds ?>s</option>
                                                            <?php } ?>
                                                    </select>
                                                        
													</div>
														
                                                </div>
												<div class="form-group">
                                                    <label class="col-md-3 control-label">Vyber súbor (JPG, JPEG, PNG, GIF)</label>
                                                    <div class="col-md-9">
													<br>
													<input type="file" name="image" required/>
                                                        
													</div>
														
                                                </div>
												
												
												
                                               
                                            </div>
                                            <div class="form-actions right1">
                                                
                                                <button type="submit" class="btn blue" name="insert">Zaznamenať</button>
                                            </div>
                                        </form>
									
                                    </div>
                                </div>
						
 </div>
<script>
    function showCalendar() {
      var checkBox = document.getElementById("period");
      var text = document.getElementById("calendar");
      if (checkBox.checked == true){
        text.style.display = "block";
      } else {
         text.style.display = "none";
      }
    }
    showCalendar();
</script>