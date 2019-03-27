<?php
include '../../ASEngine/AS.php';
include '../common/config_db.php';
require_once '../common/output_functions_structure.php';

if (!$login->isLoggedIn()) {
    header('Location: ../../login.php');
}
$user = new ASUser(ASSession::get('user_id'));
$userInfo = $user->getInfo();
/* Get User Information */
$loggedInUser = ASSession::get('user_id');

if ($loggedInUser == '') {
    ?>
	<script language="javascript">
	window.location.href = "https://iercdevsrv.tyndall.ie/prism/login.php"
	</script>
	<?php
}

if(!empty($_GET['m'])) {
    $m1 = $_GET['m'];

    $m2 = substr($m1, 0, strpos($m1, "_"));
    $act_id = substr($m2, 10); // Remove nonsense string from m

    $u1 = strstr($m1, '_');
    $userId = str_replace('_', '', $u1);

    if($userId != $loggedInUser) {
        ?>
        <script language="javascript">
            window.location.href = "https://iercdevsrv.tyndall.ie/iea/development/login/home/development/logout.php"
        </script>
        <?php
    }

} else {
    $m = 0;
}

/* Output the page header from the output_functions_structure.php file */
output_header('PRiSM | Project', 'PRiSM | PRiSM');

// Get user details using the $loggedInUser
try {
    $pdoQueryIndexDetails = "SELECT * FROM as_users WHERE user_id = {$loggedInUser}";

    $pdoResultIndexDetails = $db->prepare($pdoQueryIndexDetails);

    $pdoExecIndexDetails = $pdoResultIndexDetails->execute();

    foreach ($pdoResultIndexDetails as $rowIndexDetails) {
        $username = $rowIndexDetails['username'];

        $arr = explode('.', $username, 2);
        $firstname = ucwords(strtolower($arr[0]));
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

// Get user details using the $loggedInUser
try {
    $project_acronym = "";
    $project_id = "";    
    $task_title = "";    
    $task_id = "";
    $wp_title = "";    
    $wp_id = "";


    $act_status = "";
    $act_title = ""; 
    $act_desc = "";
    $act_assigned = "";
    $act_priority = "";
    $act_progress = "";
    $act_start = "";
    $act_end = "";

    //$pdoQueryIndexDetails = "SELECT ierc_projects.ierc_project_acronym, ierc_projects_tasks.ierc_task_title, ierc_projects_work_packages.* FROM ierc_projects_work_packages LEFT JOIN ierc_projects_tasks ON ierc_projects_work_packages.ierc_task_id = ierc_projects_tasks.ierc_task_id LEFT JOIN ierc_projects ON ierc_projects_work_packages.ierc_project_id = ierc_projects.ierc_project_id WHERE ierc_projects_work_packages.ierc_wp_id = {$wp_id}";
    $pdoQueryIndexDetails = "SELECT ierc_projects.ierc_project_acronym, ierc_projects_tasks.ierc_task_title, ierc_projects_work_packages.ierc_wp_title, ierc_projects_activities.* FROM ierc_projects_activities LEFT JOIN ierc_projects_work_packages ON ierc_projects_activities.ierc_wp_id = ierc_projects_work_packages.ierc_wp_id LEFT JOIN ierc_projects_tasks ON ierc_projects_activities.ierc_task_id = ierc_projects_tasks.ierc_task_id LEFT JOIN ierc_projects ON ierc_projects_activities.ierc_project_id = ierc_projects.ierc_project_id WHERE ierc_projects_activities.ierc_activity_id = {$act_id}"; 
    $pdoResultIndexDetails = $db->prepare($pdoQueryIndexDetails);

    $pdoExecIndexDetails = $pdoResultIndexDetails->execute();

    foreach ($pdoResultIndexDetails as $rowIndexDetails) {
        $project_acronym = $rowIndexDetails['ierc_project_acronym'];
        $project_id = $rowIndexDetails['ierc_project_id'];
        $task_title = $rowIndexDetails['ierc_task_title'];
        $task_id = $rowIndexDetails['ierc_task_id'];
        $wp_title = $rowIndexDetails['ierc_wp_title'];  
        $wp_id = $rowIndexDetails['ierc_wp_id'];        
        $act_title = $rowIndexDetails['ierc_activity_title'];     
        $act_desc = $rowIndexDetails['ierc_activity_description'];
        $act_status = $rowIndexDetails['ierc_activity_status'];
        $act_assigned = $rowIndexDetails['ierc_activity_assigned_to'];
        $act_priority = $rowIndexDetails['ierc_activity_priority'];
        $act_progress = $rowIndexDetails['ierc_activity_progress'];       
        $act_start = $rowIndexDetails['ierc_activity_start'];
        $act_end = $rowIndexDetails['ierc_activity_end'];
      
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

?>

<!-- Contents -->
<!-- To use contents simply copy the line text below and ctrl+f to find it in the script -->
<!-- Overview Start -->
<!-- PM Start -->
<!-- Finance Start -->

<body>
  <!--<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
  <script src="https://www.amcharts.com/lib/3/serial.js"></script>
  <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
-->

  <script src="../common/assets/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
  <script src="../common/assets/amcharts/amcharts/serial.js" type="text/javascript"></script>
  <script src="../common/assets/amcharts/amcharts/plugins/export/export.min.js"></script>


  <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
  <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

	<!-- start: HEADER -->
	<div class="navbar navbar-inverse navbar-fixed-top">
		<?php echo output_top_navigation($loggedInUser, $firstname); ?>
	</div>
	<!-- end: HEADER -->
	<!-- start: MAIN CONTAINER -->
	<div class="main-container">
		<div class="navbar-content">
			<!-- start: SIDEBAR -->
			<div class="main-navigation navbar-collapse collapse">
				<!-- start: MAIN MENU TOGGLER BUTTON -->
				<div class="navigation-toggler">
					<i class="clip-chevron-left"></i>
					<i class="clip-chevron-right"></i>
				</div>
				<!-- end: MAIN MENU TOGGLER BUTTON -->
				<!-- start: MAIN NAVIGATION MENU -->
				<?php echo output_main_navigation($loggedInUser); ?>
				<!-- end: MAIN NAVIGATION MENU -->
			</div>
			<!-- end: SIDEBAR -->
		</div>

		<!-- start: PAGE -->
		<div class="main-content">

			<!-- end: SPANEL CONFIGURATION MODAL FORM -->
			<div class="container">
				<!-- start: PAGE HEADER -->
				<div class="row">
					<div class="col-sm-12">
                        <?php 
                            $m2 = generateRandomString();
                            $mProj = $m2.$project_id.'_'.$loggedInUser;

                            $m3 = generateRandomString();
                            $mTask = $m3.$task_id.'_'.$loggedInUser;

                            $m4 = generateRandomString();
                            $mWP = $m4.$wp_id.'_'.$loggedInUser;
                        ?>
						<!-- start: PAGE TITLE & BREADCRUMB -->
						<ol class="breadcrumb">
                            <li>
                              <i class="clip-home-3"></i>
                              <a href="index.php">
                                Home
                              </a>
                            </li>
                            <li>								
                              <a href="projects.php">
                                                  Projects 
                              </a>
                            </li>
                            <li>								
                              <a href="project_dashboard.php?m=<?php echo $mProj; ?>">
                                                  <?php echo $project_acronym; ?>
                              </a>
                            </li>                            
                            <li>								
                              <a href="wp_dashboard.php?m=<?php echo $mWP; ?>">
                                                  <?php echo $wp_title; ?>
                              </a>
                            </li>
                            <li>								
                              <a href="task_dashboard.php?m=<?php echo $mTask; ?>">
                                                  <?php echo $task_title; ?>
                              </a>
                            </li>
							              <li class="active">                            
                                <?php echo $act_title; ?>
                            </li>
                            
							<li class="search-box">
								<form class="sidebar-search">
									<div class="form-group">
										<input type="text" placeholder="Start Searching...">
										<button class="submit">
											<i class="clip-search-3"></i>
										</button>
									</div>
								</form>
							</li>
						</ol>
						<div class="page-header">

							<h1>Activity Dashboard - <?php echo $act_title.$act_assigned; ?></h1>

							<div class="row">
								<div class="col-md-12">

									<div class="tabbable">
										<ul id="myTabDashboard" class="nav nav-pills tab-padding tab-space-3" style="border: none;">
											<li class="active">
												<a href="#dashboard_tab_pm" data-toggle="tab">
													Dashboard
												</a>
											</li>

                     
                          <li>
                            <a href="#notes_tab" data-toggle="tab">
                              Activity Notes
                            </a>
                          </li>
                                                
                          <li>
                            <a href="#act_options" data-toggle="tab">
                              Options
                            </a>
                          </li>                                       
                        
										</ul>
										<br />
										<div class="tab-content">

                                        <div id="dashboard_result"></div>
											
											<div class="tab-pane active" id="dashboard_tab_pm">

												<div class="row">
													<div class="col-md-12">
														<!-- start: DYNAMIC TABLE PANEL -->
														<div  class="panel panel-default">
															<div class="panel-heading panel-heading-default">
																<i class="clip-star-4"></i> <?php echo $act_title; ?> Overview
																<div class="panel-tools">
																	<a class="btn btn-xs btn-link panel-collapse collapses" href="#"> </a>
																	<a class="btn btn-xs btn-link panel-config" href="#panel-config" data-toggle="modal"> <i class="fa fa-wrench"></i> </a>
																	<a class="btn btn-xs btn-link panel-refresh" href="#"> <i class="fa fa-refresh"></i> </a>
																	<a class="btn btn-xs btn-link panel-expand" href="#"> <i class="clip-expand"></i> </a>
																	<a class="btn btn-xs btn-link panel-close" href="#"> <i class="fa fa-times"></i> </a>
																</div>
															</div>
															<div class="panel-body">
                                                                <div id="displayActMessage"></div>
                                                                <form role="form" id="formUpdateAct" name="formUpdateAct" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
                                                                    <input disabled type="hidden" name="openedActId" id="openedActId" value="<?php echo $act_id; ?>" class="form-control"><br />
                                                                    <input disabled type="hidden" name="loggedInUser" id="loggedInUser" value="<?php echo $loggedInUser; ?>" class="form-control"><br />
                                                                    <div class="row">
                                                                    <div class="col-sm-6">

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Project <span class="symbol required"></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <select class="form-control" id="updateActProject" name="updateActProject" disabled>		
                                                                                <option value="<?php echo $project_id; ?>"><?php echo $project_acronym; ?></option>																
                                                                            </select>
                                                                            </div>
                                                                        </div>   
                                                                        
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Task <span class="symbol required"></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <select class="form-control" id="updateActTask" name="updateActTask" disabled>		
                                                                                <option value="<?php echo $task_id; ?>"><?php echo $task_title; ?></option>																
                                                                            </select>
                                                                            </div>
                                                                        </div> 

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Work Package <span class="symbol required"></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <select class="form-control" id="updateActWP" name="updateActWP" disabled>		
                                                                                <option value="<?php echo $wp_id; ?>"><?php echo $wp_title; ?></option>																
                                                                            </select>
                                                                            </div>
                                                                        </div> 
                                                                        
                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Title <span class="symbol required"></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="updateActTitle" name="updateActTitle" value="<?php echo $act_title; ?>">
                                                                            </div>
                                                                        </div>
                                                                        

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Description <span class=""></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <textarea rows="5" class="form-control" id="updateActDescription" name="updateActDescription"><?php echo $act_desc; ?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-6">                                                                    

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                                Assigned to
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                                <select class="form-control" id="updateActAssigned" name="updateActAssigned">
                                                                                    <option value="0">Select Staff name</option>

                                                                                    <?php
                                                                                      // Get a list of Staff
                                                                                      try {

                                                                                        //$pdoQueryStaff = "SELECT user_id, username FROM as_users WHERE confirmed='Y' ";
                                                                                        $pdoQueryStaff = "SELECT user_id, CONCAT(ierc_prism_users.ierc_prism_user_name_first, ' ',ierc_prism_users.ierc_prism_user_name_last) as fullname FROM ierc_prism_users WHERE ierc_prism_users.delete_date is NULL AND user_id <> 1 "; 

                                                                                      

                                                                                        $pdoResultStaff = $db->prepare($pdoQueryStaff);

                                                                                        $pdoExecStaff = $pdoResultStaff->execute();

                                                                                        foreach($pdoResultStaff as $rowStaff)
                                                                                        {
                                                                                          $ieaStaffID =  $rowStaff['user_id'];
                                                                                          $ieaStaffName =  $rowStaff['fullname'];

                                                                                          if ($act_assigned != $ieaStaffID){
                                                                                              ?>
                                                                                              <option value="<?php echo $ieaStaffID; ?>"><?php echo $ieaStaffName; ?></option>
                                                                                              <?php
                                                                                          } else{
                                                                                              ?>
                                                                                              <option value="<?php echo $ieaStaffID; ?>" selected><?php echo $ieaStaffName; ?></option>
                                                                                              <?php
                                                                                          }

                                                                                         
                                                                                        }
                                                                                      } catch(PDOException $e) {
                                                                                        echo $e->getMessage();
                                                                                      }
                                                                                      ?>

                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Status <span class=""></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <select class="form-control" id="updateActStatus" name="updateActStatus">
                                                                                <option selected="selected" value="0" >Select Status</option>
                                                                                <?php
                                                                                                // Get Status Terms 
                                                                                                try {
                                                                                                    $pdoQueryStatus = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='status'  ORDER BY ierc_project_term_priority ASC";

                                                                                                    $pdoResultStatus = $db->prepare($pdoQueryStatus);

                                                                                                    $pdoExecStatus = $pdoResultStatus->execute();
                                                                                                    
                                                                                                    if ($pdoExecStatus) {
                                                                                                        if ($pdoResultStatus->rowCount() > 0) {
                                                                                                            foreach ($pdoResultStatus as $rowStatus) {
                                                                                                                $statusId = $rowStatus['ierc_project_term_id'];
                                                                                                                $status = $rowStatus['ierc_project_term']; 

                                                                                                                if ($act_status != $statusId){
                                                                                                                    ?>
                                                                                                                    <option value="<?php echo $statusId; ?>"><?php echo $status; ?></option>
                                                                                                                    <?php
                                                                                                                } else{
                                                                                                                    ?>
                                                                                                                    <option value="<?php echo $statusId; ?>" selected><?php echo $status; ?></option>
                                                                                                                    <?php
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo 'No status terms found!';
                                                                                                        }
                                                                                                    }
                                                                                                } catch (PDOException $e) {
                                                                                                    echo $e->getMessage();
                                                                                                }
                                                                                                ?>
                                                                            </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Priority <span class=""></span>
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <select class="form-control" id="updateActPriority" name="updateActPriority">
                                                                                <option selected="selected" value="0" >Select Priority</option>
                                                                                <?php
                                                                                                // Get Status Terms
                                                                                                try {
                                                                                                    $pdoQueryPriority = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='priority'  ORDER BY ierc_project_term_priority ASC";

                                                                                                    $pdoResultPriority = $db->prepare($pdoQueryPriority);

                                                                                                    $pdoExecPriority = $pdoResultPriority->execute();

                                                                                                    if ($pdoExecPriority) {
                                                                                                        if ($pdoResultPriority->rowCount() > 0) {
                                                                                                            foreach ($pdoResultPriority as $rowPriority) {
                                                                                                                $priorityId = $rowPriority['ierc_project_term_id'];
                                                                                                                $priority_status = $rowPriority['ierc_project_term'];

                                                                                                                if ($act_priority != $priorityId){
                                                                                                                    ?>
                                                                                                                     <option value="<?php echo $priorityId; ?>"><?php echo $priority_status; ?></option>
                                                                                                                    <?php
                                                                                                                } else{
                                                                                                                    ?>
                                                                                                                     <option value="<?php echo $priorityId; ?>" selected><?php echo $priority_status; ?></option>
                                                                                                                    <?php
                                                                                                                }
                                                                                                            }
                                                                                                        } else {
                                                                                                            echo 'No priority terms found!';
                                                                                                        }
                                                                                                    }
                                                                                                } catch (PDOException $e) {
                                                                                                    echo $e->getMessage();
                                                                                                }
                                                                                                ?>
                                                                            </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Progress (%)
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <input type="text" class="form-control" id="updateActProgress" name="updateActProgress" value="<?php echo $act_progress; ?>">
                                                                            </div>
                                                                        </div>  

                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            Start Date
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <div class="input-group">
                                                                                <input id="updateActStart"  name="updateActStart"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" value="<?php echo $act_start; ?>">
                                                                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                                            </div>
                                                                            </div>
                                                                        </div>


                                                                        <div class="form-group">
                                                                            <label class="col-sm-4 control-label">
                                                                            End Date
                                                                            </label>
                                                                            <div class="col-sm-8">
                                                                            <div class="input-group">
                                                                                <input id="updateActEnd"  name="updateActEnd"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker" value="<?php echo $act_end; ?>">
                                                                                <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                                                                            </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    </div>

                                                                    </form>
                                                                    <div class="row">
                                                                    <div class="col-md-10"></div>
                                                                        <div class="col-md-2">
                                                                            <button id="updateAct" data-dismiss="modal" class="btn btn-success" onclick="actUpdate();">
                                                                                <i class="fa fa-check" ></i> Update
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                
															</div>
														</div>
													</div>
												</div>

												<div class="row">
													<div class="col-md-12">
														<!-- start: DYNAMIC TABLE PANEL -->
														<div  class="panel panel-default">
															<div class="panel-heading panel-heading-default">
																<i class="clip-star-4"></i>  <?php echo $act_title; ?> - Activity Checklist Items
																<div class="panel-tools">
																	<a class="btn btn-xs btn-link panel-collapse collapses" href="#"> </a>
																	<a class="btn btn-xs btn-link panel-config" href="#panel-config" data-toggle="modal"> <i class="fa fa-wrench"></i> </a>
																	<a class="btn btn-xs btn-link panel-refresh" href="#"> <i class="fa fa-refresh"></i> </a>
																	<a class="btn btn-xs btn-link panel-expand" href="#"> <i class="clip-expand"></i> </a>
																	<a class="btn btn-xs btn-link panel-close" href="#"> <i class="fa fa-times"></i> </a>
																</div>
															</div>
															<div class="panel-body">
                                                                <div class="table-responsive">
                                                                    <table id="check_table" class="table table-striped table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="col-to-export" style="width:5%;">ID #</th>
                                                                    
                                                                    <th class="col-to-export" style="width:25%;">Checklist Item</th>
                                                                                <th class="col-to-export" style="width:10%;">Assigned to</th>
                                                                    <th class="col-to-export" style="width:5%;">Status</th>
                                                                                <th class="col-to-export" style="width:7%;">Start</th>
                                                                                <th class="col-to-export" style="width:7%;">End</th>								
                                                                                <th class="col-to-export" style="width:6%;">Priority</th>
                                                                                <th class="col-to-export" style="width:5%;">Progress (%)</th>
                                                                                <th class="col-to-export" style="width:5%;">View</th>                                                                                
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="check_table_body">
                                                                            <?php
                                                                            
                                                                                $checkId = 0;
                                                                                $projectTitle = '';
                                                                                $taskTitle = '';
                                                                                $wpTitle = '';
                                                                                $actTitle = '';
                                                                                $checkTitle = '';
                                                                                $checkStatus = '';
                                                                                $checkStart = '';
                                                                                $checkEnd = '';
                                                                                $checkPriority = '';
                                                                                $checkProgress = 0;
                                                                                $checkAssigned = ''; 
                                                                                // ierc_project_id 	ierc_project_title 	ierc_project_status 	ierc_project_priority 	ierc_project_progress 	assigned_to 	ierc_project_start 	ierc_project_end 	ierc_project_description
                                                                                try {
                                                                                    // Select the rows in which "id" is 3                                   
                                                                                    //$sql = "SELECT as_users.username as act_assigned, terms_status.ierc_project_term as act_status, terms_priority.ierc_project_term as act_priority, ierc_projects_activities.* FROM ierc_projects_activities LEFT JOIN as_users ON ierc_projects_activities.assigned_to = as_users.user_id LEFT JOIN ierc_projects_terms terms_status ON ierc_projects_activities.ierc_activity_status = terms_status.ierc_project_term_id LEFT JOIN ierc_projects_terms terms_priority ON ierc_projects_activities.ierc_activity_priority = terms_priority.ierc_project_term_id WHERE ierc_projects_activities.ierc_wp_id = {$wp_id} AND ierc_projects_activities.deleted_date IS NULL ORDER BY ierc_projects_activities.ierc_activity_id ";  
                                                                                    $sql = "SELECT as_users.username as check_assigned, terms_status.ierc_project_term as check_status, terms_priority.ierc_project_term as check_priority, ierc_projects_activity_checklists.* FROM ierc_projects_activity_checklists LEFT JOIN as_users ON ierc_projects_activity_checklists.assigned_to = as_users.user_id LEFT JOIN ierc_projects_terms terms_status ON ierc_projects_activity_checklists.ierc_activity_checklist_status = terms_status.ierc_project_term_id LEFT JOIN ierc_projects_terms terms_priority ON ierc_projects_activity_checklists.ierc_activity_checklist_priority = terms_priority.ierc_project_term_id WHERE ierc_projects_activity_checklists.ierc_activity_id = {$act_id}  AND ierc_projects_activity_checklists.deleted_date IS NULL ORDER BY ierc_projects_activity_checklists.ierc_activity_checklist_id";
                                                                                    
                                                                                    //$result = $db->query($sql, PDO::FETCH_OBJ);        // Apply query() with a fetch-mode

                                                                                                                                                                    
                                                                                    $pdoResultIndexDetails = $db->prepare($sql);

                                                                                    $pdoExecIndexDetails = $pdoResultIndexDetails->execute();
                                                                                    
                                                                                    // Parse the result set
                                                                                    /*while ($row = $result->fetch()) {*/
                                                                                    foreach ($pdoResultIndexDetails as $row) {                                                                                        
                                                                                      
                                                                                        $checkId = $row['ierc_activity_checklist_id'];                                                                                       
                                                                                        $checkTitle = $row['ierc_activity_checklist_title'];
                                                                                        $checkStatus = $row['check_status'];
                                                                                        $checkAssigned = $row['check_assigned'];
                                                                                        $checkStart = $row['ierc_activity_checklist_start'];
                                                                                        $checkEnd = $row['ierc_activity_checklist_end'];
                                                                                        $checkPriority = $row['check_priority'];
                                                                                        $checkProgress = $row['ierc_activity_checklist_progress'];
                                                                                        
                                                                                        // Genrate a random string when sending via URL
                                                                                        $m1 = generateRandomString();
                                                                                        $m = $m1.$checkId.'_'.$loggedInUser;

                                                                                        echo "<tr id=\"rowId_{$checkId}\">";
                                                                                        echo "<td>{$checkId}</td>";                                                                                     
                                                                                        echo "<td>{$checkTitle}</td>";
                                                                                        echo "<td>{$checkAssigned}</td>";
                                                                                        echo "<td>{$checkStatus}</td>";
                                                                                        echo "<td>{$checkStart}</td>";
                                                                                        echo "<td>{$checkEnd}</td>";
                                                                                        echo "<td>{$checkPriority}</td>";
                                                                                        echo "<td>{$checkProgress}</td>";                                       
                                                                                        // echo "<td>{$iercPRiSMProjectDescription}</td>";
                                                                                        echo "<td><a class=\"btn btn-purple btn\" href=\"checklist_dashboard.php?m={$m}\" target=\"_BLANK\"><i class=\"clip clip-eye\"></i></a></td>";
                                                                                        echo '</tr>';
                                                                                    }
                                                                                } catch (PDOException $e) {
                                                                                    echo $e->getMessage();
                                                                                }

                                                                        ?>
                                                                        </tbody>
                                                                    </table>
                                                                    </div>                                                                  
                                
															</div>
														</div>
													</div>
													
													</div>
												</div>
											<!-- Dashboard End -->                                            
											                    

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>

				</div>
				<!-- end: PAGE TITLE & BREADCRUMB -->
			</div>
		</div>
		<!-- end: PAGE HEADER -->

		<!-- start: PAGE CONTENT -->
		<div class="row">

		</div>
		<!-- end: PAGE CONTENT-->
	</div>
</div>
<!-- end: PAGE -->
</div>
<!-- end: MAIN CONTAINER -->

<!-- start: FOOTER -->
<div class="footer clearfix">
	<div class="footer-inner">
		<script>
		document.write(new Date().getFullYear())
		</script> &copy; IERC
	</div>
	<div class="footer-items">
		<span class="go-top"><i class="clip-chevron-up"></i></span>
	</div>
</div>
<!-- end: FOOTER -->

	<style>
	.modalCSS {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%) !important;
	}

	.modalCSS .modal-header {
		padding:9px 15px;
		border-bottom:1px solid #eee;
		background-color: green;
		color: #FFF;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
	</style>

	<!-- start: VIEW PROJECTS MODAL -->
	<div id="viewProjectsModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="1700" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">All IERC Projects</h4>
		</div>
		<div class="modal-body">
			<br />
				<div class="table-responsive">

					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th class="col-to-export" style="width:5%;">ID #</th>
								<th class="col-to-export" style="width:30%;">Title</th>
								<th class="col-to-export" style="width:16%;">Lead</th>
								<th class="col-to-export" style="width:7%;">Start</th>
								<th class="col-to-export" style="width:7%;">End</th>
								<th class="col-to-export" style="width:5%;">Acroynym</th>
								<th class="col-to-export" style="width:5%;">Code</th>
								<th class="col-to-export" style="width:5%;">Status</th>
								<th class="col-to-export" style="width:5%;">Priority</th>
								<th class="col-to-export" style="width:5%;">Progress</th>
								<th class="col-to-export" style="width:5%;">View</th>
								<th disabled class="col-to-export" style="width:5%;">Edit</th>
							</tr>
						</thead>
						<tbody>
							<?php

                                // create a unique identifier for the ieaRegistryDataId
                                function generateRandomString($length = 10)
                                {
                                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                                    $charactersLength = strlen($characters);
                                    $randomString = '';
                                    for ($i = 0; $i < $length; ++$i) {
                                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                                    }

                                    return $randomString;
                                }
                                // ierc_project_id 	ierc_project_title 	ierc_project_status 	ierc_project_priority 	ierc_project_progress 	assigned_to 	ierc_project_start 	ierc_project_end 	ierc_project_description
                                try {
                                    // Select the rows in which "id" is 3
                                    $sql = 'SELECT * FROM ierc_projects WHERE deleted_date IS NULL';
                                    $result = $db->query($sql, PDO::FETCH_OBJ);        // Apply query() with a fetch-mode

                                    // Parse the result set
                                    while ($row = $result->fetch()) {
                                        $iercPRiSMProjectId = 0;
                                        $iercPRiSMProjectTitle = '';
                                        $iercPRiSMProjectAcronym = '';
                                        $iercPRiSMProjectCode = '';
                                        $ieaRegistryDataDescription = '';
                                        $iercPRiSMProjectStatusOutput = '';

                                        $iercPRiSMProjectId = $row->ierc_project_id;
                                        $iercPRiSMProjectTitle = $row->ierc_project_title;
                                        $iercPRiSMProjectStatus = $row->ierc_project_status;
                                        $iercPRiSMProjectPriority = $row->ierc_project_priority;
                                        $iercPRiSMProjectProgress = $row->ierc_project_progress;
                                        $iercPRiSMProjectLead = $row->assigned_to;
                                        $iercPRiSMProjectStart = $row->ierc_project_start;
                                        $iercPRiSMProjectEnd = $row->ierc_project_end;
                                        $iercPRiSMProjectAcronym = $row->ierc_project_acronym;
                                        $iercPRiSMProjectCode = $row->ierc_project_code;
                                        $iercPRiSMProjectDescription = $row->ierc_project_description;

                                        if ($iercPRiSMProjectStatus == 'Active') {
                                            $iercPRiSMProjectStatusOutput = '<a class=\"btn btn-success btn\" href=\"\"><i class=\"clip clip-checkmark-2\"></i></a>';
                                        } elseif ($iercPRiSMProjectStatus == 'In Active') {
                                            $iercPRiSMProjectStatusOutput = '<a class=\"btn btn-danger btn\" href=\"\"><i class=\"clip clip-close-2\"></i></a>';
                                        } else {
                                            $iercPRiSMProjectStatusOutput = '<a class=\"btn btn-info btn\" href=\"\"><i class=\"clip clip-close-2\"></i></a>';
                                        }

                                        // Genrate a random string when sending via URL
                                        $m1 = generateRandomString();
                                        $m = $m1.$iercPRiSMProjectId.'_'.$loggedInUser;

                                        echo "<tr id=\"rowId_{$iercPRiSMProjectId}\">";
                                        echo "<td>{$iercPRiSMProjectId}</td>";
                                        echo "<td>{$iercPRiSMProjectTitle}</td>";
                                        echo "<td>{$iercPRiSMProjectLead}</td>";
                                        echo "<td>{$iercPRiSMProjectStart}</td>";
                                        echo "<td>{$iercPRiSMProjectEnd}</td>";
                                        echo "<td>{$iercPRiSMProjectAcronym}</td>";
                                        echo "<td>{$iercPRiSMProjectCode}</td>";
                                        echo "<td>{$iercPRiSMProjectStatusOutput}</td>";
                                        echo "<td>{$iercPRiSMProjectPriority}</td>";
                                        echo "<td>{$iercPRiSMProjectProgress}</td>";
                                        // echo "<td>{$iercPRiSMProjectDescription}</td>";
                                        echo "<td><a class=\"btn btn-purple btn\" href=\"admin_projects_prism.php?m={$m}\" target=\"_BLANK\"><i class=\"clip clip-eye\"></i></a></td>";
                                        echo "<td><a class=\"btn btn-primary btn\" href=\"admin_projects_edit.php?m={$m}\" target=\"_BLANK\"><i class=\"fa fa-edit\"></a></td>";
                                        echo '</tr>';
                                    }
                                } catch (PDOException $e) {
                                    echo $e->getMessage();
                                }

                        ?>
						</tbody>
					</table>
					</div>
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-10"></div>
				<div class="col-sm-2">
					<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
				</div>
			</div>
		</div>
	</div>
	<!-- end: USERS ASSIGN MODAL -->

	<!-- start: NEW PROJECTS MODAL -->
	<div id="newProjectsModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="600" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Assign Partner to Project</h4>
		</div>
		<div class="modal-body">
		<script>
			// JavaScript Document
			function submitAddNewLampFitting() {
				// Get the values from the form and store as variables
				var uId = document.getElementById("uId").value;
				var lightingLampFittingName = document.getElementById("lightingLampFittingName1").value;
				var lightingLampFittingDescription = document.getElementById("lightingLampFittingDescription1").value;

				// Returns successful data submission message when the entered information is stored in database.
				var dataString = 'uId=' + uId + '& lightingLampFittingName=' + lightingLampFittingName + '& lightingLampFittingDescription=' + lightingLampFittingDescription;

				// Simple check to ensure the person has put in the required data - if not give them a notification
				if (lightingLampFittingName == '') {
					$("#resultNewLampFitting").html('<div class="alert alert-warning fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-exclamation-triangle"></i>You have not entered a resultNewLampCategory Name!</div>');
				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "emv_lighting_new_lamp_fitting_upload.php",
						data: dataString,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultNewLampFitting").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new lamp type! ' + data + ' </div>');
							document.getElementById("formNewProject").reset();
						}
						, error: function () {
							$("#resultNewProject").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Sorry your new lamp type was not added! ' + data + '</div>');
						}
					});
				}
				return false;
			}

			function checkEntryTitle(uId, titleValue){

				var dataStringTitle = 'uId=' + uId + '& titleValue=' + titleValue;

				if (titleValue == '') {

				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "ajax_check_title.php",
						data: dataStringTitle,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultCheckEntry").html(data);
						}
						, error: function () {
							$("#resultCheckEntry").html(data);
						}
					});
				}
				return false;
			}

			function checkEntryAcronym(val){
				alert(val);
			}

			function checkEntryCode(val){
				alert(val);
			}
			</script>


			<div id="resultNewProject"></div>
			<div id="resultCheckEntry"></div>

			<form role="form" id="formNewProject" name="formNewProject" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Title <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectTitle" name="newProjectTitle" onChange="checkEntryTitle(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Acronym <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectAcronym" name="newProjectAcronym" onChange="checkEntryAcronym(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Code <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectCode" name="newProjectCode" placeholder="3 Letter Code - EMV, ABC, TUV" onChange="checkEntryCode(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Description <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<textarea rows="5" class="form-control" id="newProjectDescription" name="newProjectDescription"></textarea>
					</div>
				</div>
			</form>
			<!-- end: FORM WIZARD PANEL -->
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-6">
					<select class="form-control" id="buttonOptionsPPAssign" name="buttonOptionsPPAssign">
						<option selected="selected" value="1" >Create and Close</option>
						<option value="2" >Create and Add More</option>
					</select>
				</div>
				<div id="defaultPPAssign" style="display: block;">
					<div class="col-sm-2">
						<a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-block" href="#" onclick="submitAssignPartnerProject();" title="Assign a Partner to a Project" >Create</a>
					</div>
				</div>
				<div id="submitPPAssign" style="display: none;">
					<div class="col-sm-2"></div>
					<div class="col-sm-2">
						<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end: NEW PROJECTS MODAL -->


  	<!-- start: VIEW Tasks MODAL -->
	<div id="viewTasksModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="1700" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Project Tasks</h4>

      <!--<div class="form-group">-->
              <div class="col-sm-3">
                <select class="form-control" id="taskProject" name="taskProject" onchange="getTaskDetails(this,'Task')">
                  <option value="0">All Projects</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryProject = "SELECT ierc_project_id, ierc_project_acronym FROM ierc_projects";

                    $pdoResultProject = $db->prepare($pdoQueryProject);

                    $pdoExecProject = $pdoResultProject->execute();

                    foreach($pdoResultProject as $rowProject)
                    {
                      $ProjectID =  $rowProject['ierc_project_id'];
                      $ProjectName =  $rowProject['ierc_project_acronym'];
                      ?>
                        <option value="<?php echo $ProjectID; ?>"><?php echo $ProjectName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
           <!-- </div>-->
		</div>
		<div class="modal-body">
			<br />
				<div class="table-responsive">

					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th class="col-to-export" style="width:5%;">ID #</th>
                <th class="col-to-export" style="width:15%;">Project Title</th>
                <th class="col-to-export" style="width:25%;">Task Title</th>
								<th class="col-to-export" style="width:10%;">Assigned to</th>
                <th class="col-to-export" style="width:5%;">Status</th>
								<th class="col-to-export" style="width:5%;">Start</th>
								<th class="col-to-export" style="width:5%;">End</th>								
								<th class="col-to-export" style="width:5%;">Priority</th>
								<th class="col-to-export" style="width:5%;">Progress (%)</th>
								<th class="col-to-export" style="width:5%;">View</th>
								<th disabled class="col-to-export" style="width:5%;">Edit</th>
							</tr>
						</thead>
						<tbody id="task_table_body">
							<?php
                               
                                $taskId = 0;
                                $projectTitle = '';
                                $taskTitle = '';
                                $taskStatus = '';
                                $taskStart = '';
                                $taskEnd = '';
                                $taskPriority = '';
                                $taskProgress = 0;
                                $taskAssigned = ''; 
                                // ierc_project_id 	ierc_project_title 	ierc_project_status 	ierc_project_priority 	ierc_project_progress 	assigned_to 	ierc_project_start 	ierc_project_end 	ierc_project_description
                                try {
                                    // Select the rows in which "id" is 3                                   
                                    $sql = 'SELECT ierc_projects.ierc_project_acronym, as_users.username, terms_status.ierc_project_term as task_status, terms_priority.ierc_project_term as task_priority, ierc_projects_tasks.* FROM ierc_projects_tasks LEFT JOIN ierc_projects ON ierc_projects_tasks.ierc_project_id = ierc_projects.ierc_project_id LEFT JOIN as_users ON ierc_projects_tasks.ierc_task_assigned_to = as_users.user_id LEFT JOIN ierc_projects_terms terms_status ON ierc_projects_tasks.ierc_task_status = terms_status.ierc_project_term_id LEFT JOIN ierc_projects_terms terms_priority ON ierc_projects_tasks.ierc_task_priority = terms_priority.ierc_project_term_id WHERE ierc_projects_tasks.deleted_date IS NULL ORDER BY ierc_projects_tasks.ierc_project_id ';
                                    $result = $db->query($sql, PDO::FETCH_OBJ);        // Apply query() with a fetch-mode

                                    // Parse the result set
                                    while ($row = $result->fetch()) {
                                        

                                        $taskId = $row->ierc_task_id;
                                        $projectTitle = $row->ierc_project_acronym;
                                        $taskTitle = $row->ierc_task_title;
                                        $taskStatus = $row->task_status;
                                        $taskStart = $row->ierc_task_start;
                                        $taskEnd = $row->ierc_task_end;
                                        $taskPriority = $row->task_priority;
                                        $taskProgress = $row->ierc_task_progress;
                                        $taskAssigned = $row->username;
                                        /*
                                        if ($taskStatus == 'Active') {
                                            $taskStatusOutput = '<a class=\"btn btn-success btn\" href=\"\"><i class=\"clip clip-checkmark-2\"></i></a>';
                                        } elseif ($taskStatus == 'In Active') {
                                            $taskStatusOutput = '<a class=\"btn btn-danger btn\" href=\"\"><i class=\"clip clip-close-2\"></i></a>';
                                        } else {
                                            $taskStatusOutput = '<a class=\"btn btn-info btn\" href=\"\"><i class=\"clip clip-close-2\"></i></a>';
                                        }
                                        */

                                        // Genrate a random string when sending via URL
                                        $m1 = generateRandomString();
                                        $m = $m1.$iercPRiSMProjectId.'_'.$loggedInUser;

                                        echo "<tr id=\"rowId_{$taskId}\">";
                                        echo "<td>{$taskId}</td>";
                                        echo "<td>{$projectTitle}</td>";
                                        echo "<td>{$taskTitle}</td>";
                                        echo "<td>{$taskAssigned}</td>";
                                        echo "<td>{$taskStatus}</td>";
                                        echo "<td>{$taskStart}</td>";
                                        echo "<td>{$taskEnd}</td>";
                                        echo "<td>{$taskPriority}</td>";
                                        echo "<td>{$taskProgress}</td>";                                       
                                        // echo "<td>{$iercPRiSMProjectDescription}</td>";
                                        echo "<td><a class=\"btn btn-purple btn\" href=\"admin_projects_prism.php?m={$m}\" target=\"_BLANK\"><i class=\"clip clip-eye\"></i></a></td>";
                                        echo "<td><a class=\"btn btn-primary btn\" href=\"admin_projects_edit.php?m={$m}\" target=\"_BLANK\"><i class=\"fa fa-edit\"></a></td>";
                                        echo '</tr>';
                                    }
                                } catch (PDOException $e) {
                                    echo $e->getMessage();
                                }

                        ?>
						</tbody>
					</table>
					</div>
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-10"></div>
				<div class="col-sm-2">
					<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
				</div>
			</div>
		</div>
	</div>
	<!-- end: View Tasks MODAL -->    

  <!-- start: NEW Tasks MODAL -->
  <div id="newTasksModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="800" style="display: none;">
    <div class="modal-header modalStyleHeader">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
      <h4 class="modal-title">Create New Task</h4>
    </div>
    <div class="modal-body">
    <script>
        // JavaScript Document
  			function submitCreateTask() {
          //alert("submitCreateTask");
  				// Get the values from the form and store as variables
  				var uId = <?php echo $loggedInUser; ?>;
          var newTaskProject = document.getElementById("taskProject").value;
          var newTaskTitle = document.getElementById("newTaskTitle").value;
  				var newTaskDescription = document.getElementById("newTaskDescription").value;
          var newTaskStatus = document.getElementById("taskStatus").value;
          var taskPriority = document.getElementById("taskPriority").value;
          var taskProgress = document.getElementById("taskProgress").value;
          var taskAssigned = document.getElementById("taskAssigned").value;

  				// Returns successful data submission message when the entered information is stored in database.
  				var dataString = 'uId=' + uId + '& newTaskProject=' + newTaskProject + '& newTaskTitle=' + newTaskTitle + '& newTaskDescription=' + newTaskDescription + '& newTaskStatus=' + newTaskStatus + '& taskPriority=' + taskPriority + '& taskProgress=' + taskProgress + '& taskAssigned=' + taskAssigned;

          //alert(dataString);
  				// AJAX code to submit form.
          if (newTaskTitle != ''){
  					$.ajax({
  						type: "POST",
  						url: "ajax_new_task.php",
  						data: dataString,
  						cache: false, // If successfully inserted then notify the user / else notify them of the error
  						success: function (data) {
                //alert(data);
                //If Create & Close is selected
                if (document.getElementById("buttonOptionsTaskCreate").value == 1){
                  $('#newTasksModal').modal('hide');
                  $("#dashboard_result").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new task! - ' + data + ' </div>');
                  $("#dashboard_result").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
    							document.getElementById("formNewTask").reset();
                }else{  // If add more is slected
                  $("#resultNewTask").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new task! - ' + data + ' </div>');
                  $("#resultNewTask").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');

                  document.getElementById("formNewTask").reset();
                  document.getElementById("taskProject").value = newTaskProject;

                }

  						}
  						, error: function () {
                alert("Error: "+data);
  						}
  					});
  				}
  				return false;
  			}


    </script>


      <div id="resultNewTask"></div>
      <div id="resultCheckEntry"></div>

      <form role="form" id="formNewTask" name="formNewTask" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Project
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="taskProject" name="taskProject">
                  <option value="0">Select Project</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryProject = "SELECT ierc_project_id, ierc_project_acronym FROM ierc_projects";

                    $pdoResultProject = $db->prepare($pdoQueryProject);

                    $pdoExecProject = $pdoResultProject->execute();

                    foreach($pdoResultProject as $rowProject)
                    {
                      $ProjectID =  $rowProject['ierc_project_id'];
                      $ProjectName =  $rowProject['ierc_project_acronym'];
                      ?>
                        <option value="<?php echo $ProjectID; ?>"><?php echo $ProjectName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Title <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newTaskTitle" name="newTaskTitle">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Description <span class=""></span>
              </label>
              <div class="col-sm-8">
                <textarea rows="7" class="form-control" id="newTaskDescription" name="newTaskDescription"></textarea>
              </div>
            </div>
          </div>

        <div class="col-sm-6">
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Status <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="taskStatus" name="taskStatus">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryStatus = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='status'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultStatus = $db->prepare($pdoQueryStatus);

                                        $pdoExecStatus = $pdoResultStatus->execute();

                                        if ($pdoExecStatus) {
                                            if ($pdoResultStatus->rowCount() > 0) {
                                                foreach ($pdoResultStatus as $rowStatus) {
                                                    $statusId = $rowStatus['ierc_project_term_id'];
                                                    $status = $rowStatus['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $statusId; ?>"><?php echo $status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No status terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Priority <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="taskPriority" name="taskPriority">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryPriority = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='priority'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultPriority = $db->prepare($pdoQueryPriority);

                                        $pdoExecPriority = $pdoResultPriority->execute();

                                        if ($pdoExecPriority) {
                                            if ($pdoResultPriority->rowCount() > 0) {
                                                foreach ($pdoResultPriority as $rowPriority) {
                                                    $priorityId = $rowPriority['ierc_project_term_id'];
                                                    $priority_status = $rowPriority['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $priorityId; ?>"><?php echo $priority_status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No priority terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Progress (%)
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="taskProgress" name="taskProgress">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Assigned to
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="taskAssigned" name="taskAssigned">
                  <option value="0">Select Staff name</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryStaff = "SELECT user_id, username FROM as_users WHERE confirmed='Y' ";

                    $pdoResultStaff = $db->prepare($pdoQueryStaff);

                    $pdoExecStaff = $pdoResultStaff->execute();

                    foreach($pdoResultStaff as $rowStaff)
                    {
                      $ieaStaffID =  $rowStaff['user_id'];
                      $ieaStaffName =  $rowStaff['username'];
                      ?>
                        <option value="<?php echo $ieaStaffID; ?>"><?php echo $ieaStaffName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Start Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="taskStart"  name="taskStart"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                  <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>


            <div class="form-group">
              <label class="col-sm-4 control-label">
                End Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                    <input id="taskEnd"  name="taskEnd"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                    <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>
          </div>
        </div>

      </form>
      <!-- end: FORM WIZARD PANEL -->
      <br />
    </div>
    <div class="modal-footer">
      <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
          <select class="form-control" id="buttonOptionsTaskCreate" name="buttonOptionsTaskCreate">
            <option selected="selected" value="1" >Create and Close</option>
            <option value="2" >Create and Add More</option>
          </select>
        </div>
        <div id="defaultPPAssign" style="display: block;">
          <div class="col-sm-2">
            <a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-success btn-block" href="#" onclick="submitCreateTask();" title="Create a New Task" >Create</a>
          </div>
        </div>
        <div id="submitPPAssign" style="display: none;">
          <div class="col-sm-2"></div>
          <div class="col-sm-2">
            <a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end: NEW Tasks MODAL -->

  <!-- start: NEW Work Packages MODAL class="modal fade modal-lg" -->
  <div id="newWPModal" class="modal fade modal800" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="800" style="display: none;">
    <div class="modal-header modalStyleHeader">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
      <h4 class="modal-title">Create New Work Package</h4>
    </div>
    <div class="modal-body">
    <script>
        // JavaScript Document
  			function submitCreateWP() {
  				// Get the values from the form and store as variables
  				var uId = <?php echo $loggedInUser; ?>;
          var newWpProject = document.getElementById("newWpProject").value;
          var newWpTask = document.getElementById("newWpTask").value;
          var newWpTitle = document.getElementById("newWpTitle").value;
  				var newWpDescription = document.getElementById("newWpDescription").value;
          var newWpStatus = document.getElementById("newWpStatus").value;
          var newWpPriority = document.getElementById("newWpPriority").value;
          var newWpProgress = document.getElementById("newWpProgress").value;
          var newWpAssigned = document.getElementById("newWpAssigned").value;
          var newWpStart = document.getElementById("newWpStart").value;
          var newWpEnd = document.getElementById("newWpEnd").value;

  				// Returns successful data submission message when the entered information is stored in database.
  				var dataString = 'uId=' + uId + '& newWpProject=' + newWpProject + '& newWpTask=' + newWpTask + '& newWpTitle=' + newWpTitle + '& newWpDescription=' + newWpDescription + '& newWpStatus=' + newWpStatus + '& newWpPriority=' + newWpPriority + '& newWpProgress=' + newWpProgress + '& newWpAssigned=' + newWpAssigned + '& newWpStart=' + newWpStart + '& newWpEnd=' + newWpEnd;

          //alert(dataString);
  				// AJAX code to submit form.
          if (newTaskTitle != ''){
  					$.ajax({
  						type: "POST",
  						url: "ajax_new_wp.php",
  						data: dataString,
  						cache: false, // If successfully inserted then notify the user / else notify them of the error
  						success: function (data) {
                alert(data);
                //If Create & Close is selected
                if (document.getElementById("buttonOptionsWPCreate").value == 1){
                  document.getElementById("formNewWP").reset();
                  $('#newWPModal').modal('hide');
                  $("#dashboard_result").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new work package! - ' + data + ' </div>');
                  $("#dashboard_result").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
    							document.getElementById("formNewWP").reset();
                }else{  // If add more is slected
                  $("#resultNewWP").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new work package! - ' + data + ' </div>');
                  $("#resultNewWP").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');

                  document.getElementById("formNewWP").reset();
                  document.getElementById("newWpProject").value = newWpProject;
                  document.getElementById("newWpTask").value = newWpTask;

                }

  						}
  						, error: function () {
                alert("Error: "+data);
  						}
  					});
  				}
  				return false;
  			}


    </script>


      <div id="resultNewWP"></div>
      <div id="resultCheckEntry"></div>

      <form role="form" id="formNewWP" name="formNewWP" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
              <label class="col-sm-3 control-label">
                Project
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newWpProject" name="newWpProject" onchange="getTasks(this,'WorkPackage')">
                  <option value="0">Select Project</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryProject = "SELECT ierc_project_id, ierc_project_acronym FROM ierc_projects";

                    $pdoResultProject = $db->prepare($pdoQueryProject);

                    $pdoExecProject = $pdoResultProject->execute();

                    foreach($pdoResultProject as $rowProject)
                    {
                      $ProjectID =  $rowProject['ierc_project_id'];
                      $ProjectName =  $rowProject['ierc_project_acronym'];
                      ?>
                        <option value="<?php echo $ProjectID; ?>"><?php echo $ProjectName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label">
                Task
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newWpTask" name="newWpTask">
                  <option value="0">Select Project First</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-3 control-label">
                Title <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newWpTitle" name="newWpTitle">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-3 control-label">
                Description <span class=""></span>
              </label>
              <div class="col-sm-8">
                <textarea rows="5" class="form-control" id="newWpDescription" name="newWpDescription"></textarea>
              </div>
            </div>
        </div>

        <div class="col-sm-6">

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Status <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newWpStatus" name="newWpStatus">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryStatus = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='status'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultStatus = $db->prepare($pdoQueryStatus);

                                        $pdoExecStatus = $pdoResultStatus->execute();

                                        if ($pdoExecStatus) {
                                            if ($pdoResultStatus->rowCount() > 0) {
                                                foreach ($pdoResultStatus as $rowStatus) {
                                                    $statusId = $rowStatus['ierc_project_term_id'];
                                                    $status = $rowStatus['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $statusId; ?>"><?php echo $status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No status terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Priority <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newWpPriority" name="newWpPriority">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryPriority = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='priority'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultPriority = $db->prepare($pdoQueryPriority);

                                        $pdoExecPriority = $pdoResultPriority->execute();

                                        if ($pdoExecPriority) {
                                            if ($pdoResultPriority->rowCount() > 0) {
                                                foreach ($pdoResultPriority as $rowPriority) {
                                                    $priorityId = $rowPriority['ierc_project_term_id'];
                                                    $priority_status = $rowPriority['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $priorityId; ?>"><?php echo $priority_status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No priority terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Progress (%)
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newWpProgress" name="newWpProgress">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Assigned to
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newWpAssigned" name="newWpAssigned">
                  <option value="0">Select Staff name</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryStaff = "SELECT user_id, username FROM as_users WHERE confirmed='Y' ";

                    $pdoResultStaff = $db->prepare($pdoQueryStaff);

                    $pdoExecStaff = $pdoResultStaff->execute();

                    foreach($pdoResultStaff as $rowStaff)
                    {
                      $ieaStaffID =  $rowStaff['user_id'];
                      $ieaStaffName =  $rowStaff['username'];
                      ?>
                        <option value="<?php echo $ieaStaffID; ?>"><?php echo $ieaStaffName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Start Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="newWpStart"  name="newWpStart"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                  <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>


            <div class="form-group">
              <label class="col-sm-4 control-label">
                End Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                    <input id="newWpEnd"  name="newWpEnd"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                    <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>
        </div>
      </div>

      </form>
      <!-- end: FORM WIZARD PANEL -->
      <br />
    </div>
    <div class="modal-footer">
      <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
          <select class="form-control" id="buttonOptionsWPCreate" name="buttonOptionsWPCreate">
            <option selected="selected" value="1" >Create and Close</option>
            <option value="2" >Create and Add More</option>
          </select>
        </div>
        <div id="defaultPPAssign" style="display: block;">
          <div class="col-sm-2">
            <a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-success btn-block" href="#" onclick="submitCreateWP();" title="Create a New Work Package" >Create</a>
          </div>
        </div>
        <div id="submitPPAssign" style="display: none;">
          <div class="col-sm-2"></div>
          <div class="col-sm-2">
            <a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end: NEW Work Packages MODAL -->

  <!-- start: NEW Activities MODAL -->
  <div id="newActModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="800" style="display: none;">
    <div class="modal-header modalStyleHeader">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
      <h4 class="modal-title">Create New Activity</h4>
    </div>
    <div class="modal-body">
    <script>
        // JavaScript Document
  			function submitCreateAct() {
          alert("submitCreateAct");
  				// Get the values from the form and store as variables
  				var uId = <?php echo $loggedInUser; ?>;
          var newActProject = document.getElementById("newActProject").value;
          var newActTask = document.getElementById("newActTask").value;
          var newActWp = document.getElementById("newActWp").value;
          var newActTitle = document.getElementById("newActTitle").value;
  				var newActDescription = document.getElementById("newActDescription").value;

          var newActStatus = document.getElementById("newActStatus").value;
          var newActPriority = document.getElementById("newActPriority").value;
          var newActProgress = document.getElementById("newActProgress").value;
          var newActAssigned = document.getElementById("newActAssigned").value;
          var newActStart = document.getElementById("newActStart").value;
          var newActEnd = document.getElementById("newActEnd").value;

  				// Returns successful data submission message when the entered information is stored in database.
  				var dataString = 'uId=' + uId + '& newActProject=' + newActProject + '& newActTask=' + newActTask + '& newActWp=' + newActWp + '& newActTitle=' + newActTitle + '& newActDescription=' + newActDescription + '& newActStatus=' + newActStatus + '& newActPriority=' + newActPriority + '& newActProgress=' + newActProgress + '& newActAssigned=' + newActAssigned + '& newActStart=' + newActStart + '& newActEnd=' + newActEnd;

          alert(dataString);
  				// AJAX code to submit form.
          if (newTaskTitle != ''){
  					$.ajax({
  						type: "POST",
  						url: "ajax_new_activity.php",
  						data: dataString,
  						cache: false, // If successfully inserted then notify the user / else notify them of the error
  						success: function (data) {
                alert("Success222"+data);
                //If Create & Close is selected
                if (document.getElementById("buttonOptionsActCreate").value == 1){
                  document.getElementById("formNewAct").reset();
                  $('#newActModal').modal('hide');
                  $("#dashboard_result").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new activity! - ' + data + ' </div>');
                  $("#dashboard_result").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
    							document.getElementById("formNewAct").reset();
                }else{  // If add more is slected
                  $("#resultNewAct").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new activity! - ' + data + ' </div>');
                  $("#resultNewAct").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');

                  document.getElementById("formNewAct").reset();
                  document.getElementById("newActProject").value = newActProject;
                  document.getElementById("newActTask").value = newActTask;
                  document.getElementById("newActWp").value = newActWp;
                }
                alert("finish"+ newActStatus);

  						}
  						, error: function () {
                alert("Error: "+data);
  						}
  					});
            //update the postits
            /*
            var dataStringPostIt = 'uId=' + uId + '& projectID=' + newActProject + '& statusID=' + newActStatus;
            alert("dataStringPostIt ="+dataStringPostIt)
            $.ajax({
  						type: "POST",
  						url: "ajax_get_postits.php",
  						data: dataStringPostIt,
  						cache: false, // If successfully inserted then notify the user / else notify them of the error
  						success: function (data) {
                alert("Success"+data);
              }
  						, error: function () {
                alert("Error: "+data);
  						}
              */
  				}
            return false;
          }
  				
  			


    </script>


      <div id="resultNewAct"></div>

      <form role="form" id="formNewAct" name="formNewAct" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
        <div class="row">
        <div class="col-sm-6">

            <div class="form-group">
                <label class="col-sm-4 control-label">
                    Project <span class="symbol required"></span>
                </label>
                <div class="col-sm-8">
                    <select class="form-control" id="newActProject" name="newActProject" onchange="getTasks(this,'Activity')" disabled>
                        <option value="<?php echo $project_id; ?>" selected><?php echo $project_acronym; ?></option>                    
                    </select>
                </div>
                </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Task <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newActTask" name="newActTask" onchange="getWorkPackages(this,'Activity')">
                    <option value="0">Select Task</option>

                    <?php
                    // Get a list of Staff
                    try {

                      $pdoQueryTask = "SELECT ierc_task_id, ierc_task_title FROM ierc_projects_tasks WHERE ierc_project_id ='{$project_id}'";
                      $pdoResultTask = $db->prepare($pdoQueryTask);
                      $pdoExecTask = $pdoResultTask->execute();

                      foreach($pdoResultTask as $taskRow){
                          $taskId = $taskRow['ierc_task_id'];
                          $taskTitle =  $taskRow['ierc_task_title'];
                          
                          echo '<option value="'.$taskId.'">'.$taskTitle.'</option>';
                        }
                      
                    } catch(PDOException $e) {
                      echo $e->getMessage();
                    }
                    ?>

                  </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Work Package <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select id="newActWp" class="form-control" name="newActWp">
                  <option value="0">Select Task First</option>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Title <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newActTitle" name="newActTitle">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Description <span class=""></span>
              </label>
              <div class="col-sm-8">
                <textarea rows="5" class="form-control" id="newActDescription" name="newActDescription"></textarea>
              </div>
            </div>
        </div>

        <div class="col-sm-6">

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Status <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newActStatus" name="newActStatus">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryStatus = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='status'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultStatus = $db->prepare($pdoQueryStatus);

                                        $pdoExecStatus = $pdoResultStatus->execute();

                                        if ($pdoExecStatus) {
                                            if ($pdoResultStatus->rowCount() > 0) {
                                                foreach ($pdoResultStatus as $rowStatus) {
                                                    $statusId = $rowStatus['ierc_project_term_id'];
                                                    $status = $rowStatus['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $statusId; ?>"><?php echo $status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No status terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Priority <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newActPriority" name="newActPriority">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryPriority = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='priority'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultPriority = $db->prepare($pdoQueryPriority);

                                        $pdoExecPriority = $pdoResultPriority->execute();

                                        if ($pdoExecPriority) {
                                            if ($pdoResultPriority->rowCount() > 0) {
                                                foreach ($pdoResultPriority as $rowPriority) {
                                                    $priorityId = $rowPriority['ierc_project_term_id'];
                                                    $priority_status = $rowPriority['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $priorityId; ?>"><?php echo $priority_status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No priority terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Progress (%)
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newActProgress" name="newActProgress">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Assigned to
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newActAssigned" name="newActAssigned">
                  <option value="0">Select Staff name</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryStaff = "SELECT user_id, username FROM as_users WHERE confirmed='Y' ";

                    $pdoResultStaff = $db->prepare($pdoQueryStaff);

                    $pdoExecStaff = $pdoResultStaff->execute();

                    foreach($pdoResultStaff as $rowStaff)
                    {
                      $ieaStaffID =  $rowStaff['user_id'];
                      $ieaStaffName =  $rowStaff['username'];
                      ?>
                        <option value="<?php echo $ieaStaffID; ?>"><?php echo $ieaStaffName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Start Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="newActStart"  name="newActStart"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                  <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>


            <div class="form-group">
              <label class="col-sm-4 control-label">
                End Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                    <input id="newActEnd"  name="newActEnd"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                    <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>
        </div>
      </div>

      </form>
      <!-- end: FORM WIZARD PANEL -->
      <br />
    </div>
    <div class="modal-footer">
      <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
          <select class="form-control" id="buttonOptionsActCreate" name="buttonOptionsActCreate">
            <option selected="selected" value="1" >Create and Close</option>
            <option value="2" >Create and Add More</option>
          </select>
        </div>
        <div id="defaultPPAssign" style="display: block;">
          <div class="col-sm-2">
            <a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-success btn-block" href="#" onclick="alert("here"); submitCreateAct();" title="Create a New Activity" >Create</a>
          </div>
        </div>
        <div id="submitPPAssign" style="display: none;">
          <div class="col-sm-2"></div>
          <div class="col-sm-2">
            <a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end: NEW Activities MODAL -->

  <!-- start: NEW Activity Checklist MODAL -->
  <div id="newChecklistModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="800" style="display: none;">
    <div class="modal-header modalStyleHeader">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
      <h4 class="modal-title">Create New Checklist Item</h4>
    </div>
    <div class="modal-body">
    <script>
        // JavaScript Document
  			function submitCreateCheck() {

  				// Get the values from the form and store as variables
  				var uId = <?php echo $loggedInUser; ?>;
          var newCheckProject = document.getElementById("newCheckProject").value;
          var newCheckTask = document.getElementById("newCheckTask").value;
          var newCheckWp = document.getElementById("newCheckWp").value;
          var newCheckAct = document.getElementById("newCheckAct").value;
          var newCheckTitle = document.getElementById("newCheckTitle").value;
  				var newCheckDescription = document.getElementById("newCheckDescription").value;

          var newCheckStatus = document.getElementById("newCheckStatus").value;
          var newCheckPriority = document.getElementById("newCheckPriority").value;
          var newCheckProgress = document.getElementById("newCheckProgress").value;
          var newCheckAssigned = document.getElementById("newCheckAssigned").value;
          var newCheckStart = document.getElementById("newCheckStart").value;
          var newCheckEnd = document.getElementById("newCheckEnd").value;
  				// Returns successful data submission message when the entered information is stored in database.
  	       var dataString = 'uId=' + uId + '& newCheckProject=' + newCheckProject + '& newCheckTask=' + newCheckTask + '& newCheckWp=' + newCheckWp + '& newCheckAct=' + newCheckAct + '& newCheckTitle=' + newCheckTitle + '& newCheckDescription=' + newCheckDescription + '& newCheckStatus=' + newCheckStatus + '& newCheckPriority=' + newCheckPriority + '& newCheckProgress=' + newCheckProgress + '& newCheckAssigned=' + newCheckAssigned + '& newCheckStart=' + newCheckStart + '& newCheckEnd=' + newCheckEnd;


          //alert(dataString);
  				// AJAX code to submit form.
          if (newCheckTitle != ''){
  					$.ajax({
  						type: "POST",
  						url: "ajax_new_checklist.php",
  						data: dataString,
  						cache: false, // If successfully inserted then notify the user / else notify them of the error
  						success: function (data) {
                alert(data);
                //If Create & Close is selected
                if (document.getElementById("buttonOptionsCheckCreate").value == 1){
                  document.getElementById("formNewCheck").reset();
                  $('#newChecklistModal').modal('hide');
                  $("#dashboard_result").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new checklist item! - ' + data + ' </div>');
                  $("#dashboard_result").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
                }else{  // If add more is slected
                  $("#resultNewCheck").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new checklist item! - ' + data + ' </div>');
                  $("#resultNewCheck").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');

                  document.getElementById("formNewCheck").reset();
                  document.getElementById("newCheckProject").value = newCheckProject;
                  document.getElementById("newCheckTask").value = newCheckTask;
                  document.getElementById("newCheckWp").value = newCheckWp;
                  document.getElementById("newCheckAct").value = newCheckAct;
                }

  						}
  						, error: function () {
                alert("Error: "+data);
  						}
  					});
  				}
  				return false;
  			}


    </script>


      <div id="resultNewCheck"></div>

      <form role="form" id="formNewCheck" name="formNewCheck" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
        <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
              <label class="col-sm-4 control-label">
                Project <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckProject" name="newCheckProject" onchange="getTasks(this,'Checklist')" required>
                  <option value="0">Select Project</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryProject = "SELECT ierc_project_id, ierc_project_acronym FROM ierc_projects";

                    $pdoResultProject = $db->prepare($pdoQueryProject);

                    $pdoExecProject = $pdoResultProject->execute();

                    foreach($pdoResultProject as $rowProject)
                    {
                      $ProjectID =  $rowProject['ierc_project_id'];
                      $ProjectName =  $rowProject['ierc_project_acronym'];
                      ?>
                        <option value="<?php echo $ProjectID; ?>"><?php echo $ProjectName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Task <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckTask" name="newActTask" onchange="getWorkPackages(this,'Checklist')" required>
                  <option value="0">Select Project First</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Work Package <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckWp" name="newCheckWp" onchange="getActivities(this,'Checklist')" required>
                  <option value="0">Select Task First</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Activity <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckAct" name="newCheckAct" required>
                  <option value="0">Select Work Package First</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Title <span class="symbol required"></span>
              </label>
              <div class="col-sm-8">
                <input type="text" name="name" minlength="2" class="form-control" id="newCheckTitle" name="newCheckTitle" required>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Description <span class=""></span>
              </label>
              <div class="col-sm-8">
                <textarea rows="5" class="form-control" id="newCheckDescription" name="newCheckDescription"></textarea>
              </div>
            </div>
        </div>

        <div class="col-sm-6">

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Status <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckStatus" name="newCheckStatus">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryStatus = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='status'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultStatus = $db->prepare($pdoQueryStatus);

                                        $pdoExecStatus = $pdoResultStatus->execute();

                                        if ($pdoExecStatus) {
                                            if ($pdoResultStatus->rowCount() > 0) {
                                                foreach ($pdoResultStatus as $rowStatus) {
                                                    $statusId = $rowStatus['ierc_project_term_id'];
                                                    $status = $rowStatus['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $statusId; ?>"><?php echo $status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No status terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Priority <span class=""></span>
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckPriority" name="newCheckPriority">
                  <option selected="selected" value="0" >Select Status</option>
                  <?php
                                    // Get Status Terms
                                    try {
                                        $pdoQueryPriority = "SELECT * FROM ierc_projects_terms WHERE ierc_project_term_identifier ='priority'  ORDER BY ierc_project_term_priority ASC";

                                        $pdoResultPriority = $db->prepare($pdoQueryPriority);

                                        $pdoExecPriority = $pdoResultPriority->execute();

                                        if ($pdoExecPriority) {
                                            if ($pdoResultPriority->rowCount() > 0) {
                                                foreach ($pdoResultPriority as $rowPriority) {
                                                    $priorityId = $rowPriority['ierc_project_term_id'];
                                                    $priority_status = $rowPriority['ierc_project_term'];

                                                    ?>
                                                      <option value="<?php echo $priorityId; ?>"><?php echo $priority_status; ?></option>
                                                    <?php
                                                }
                                            } else {
                                                echo 'No priority terms found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Progress (%)
              </label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="newCheckProgress" name="newCheckProgress">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Assigned to
              </label>
              <div class="col-sm-8">
                <select class="form-control" id="newCheckAssigned" name="newCheckAssigned">
                  <option value="0">Select Staff name</option>

                  <?php
                  // Get a list of Staff
                  try {

                    $pdoQueryStaff = "SELECT user_id, username FROM as_users WHERE confirmed='Y' ";

                    $pdoResultStaff = $db->prepare($pdoQueryStaff);

                    $pdoExecStaff = $pdoResultStaff->execute();

                    foreach($pdoResultStaff as $rowStaff)
                    {
                      $ieaStaffID =  $rowStaff['user_id'];
                      $ieaStaffName =  $rowStaff['username'];
                      ?>
                        <option value="<?php echo $ieaStaffID; ?>"><?php echo $ieaStaffName; ?></option>
                      <?php
                    }
                  } catch(PDOException $e) {
                    echo $e->getMessage();
                  }
                  ?>

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label">
                Start Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                  <input id="newCheckStart"  name="newCheckStart"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                  <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>


            <div class="form-group">
              <label class="col-sm-4 control-label">
                End Date
              </label>
              <div class="col-sm-8">
                <div class="input-group">
                    <input id="newCheckEnd"  name="newCheckEnd"  type="text" data-date-format="dd-mm-yyyy" data-date-viewmode="years" class="form-control date-picker">
                    <span class="input-group-addon"> <i class="fa fa-calendar"></i> </span>
                </div>
              </div>
            </div>
        </div>
      </div>

      </form>
      <!-- end: FORM WIZARD PANEL -->
      <br />
    </div>
    <div class="modal-footer">
      <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-6">
          <select class="form-control" id="buttonOptionsCheckCreate" name="buttonOptionsCheckCreate">
            <option selected="selected" value="1" >Create and Close</option>
            <option value="2" >Create and Add More</option>
          </select>
        </div>
        <div id="defaultPPAssign" style="display: block;">
          <div class="col-sm-2">
            <a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
          </div>
          <div class="col-sm-2">
            <a class="btn btn-success btn-block" href="#" onclick="submitCreateCheck();" title="Create a New Checklist Item" >Create</a>
          </div>
        </div>
        <div id="submitPPAssign" style="display: none;">
          <div class="col-sm-2"></div>
          <div class="col-sm-2">
            <a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- end: NEW Activity Checklist MODAL -->

	<!-- start: VIEW USERS MODAL -->
	<div id="viewUsersModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="600" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Assign Partner to Project</h4>
		</div>
		<div class="modal-body">
			<br />

			<script>
				// JavaScript Document
				function submitAssignPartnerProject() {

					// Get the values from the form and store as variables
					var uId = document.getElementById("loggedInUserPPAssign").value;
					var projectSelect = document.getElementById("projectSelect").value;
					var partnerSelect = document.getElementById("partnerSelect").value;

					// Returns successful data submission message when the entered information is stored in database.
					var dataStringAssignPartnerProject = 'uId=' + uId
																								+ '& projectSelect=' + projectSelect
																									+ '& partnerSelect=' + partnerSelect;

					// Simple check to ensure the person has put in the required data - if not give them a notification
					if (projectSelect == 0 || partnerSelect == 0) {
						$("#displayResultPartnerProjectAssign").html('<div class="alert alert-warning fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-exclamation-triangle"></i>You have empty fields!</div>');
						$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
					}
					else {
						// AJAX code to submit form.
						$.ajax({
							type: "POST",
							url: "ajax_process_assign_partner_project.php",
							data: dataStringAssignPartnerProject,
							cache: false, // If successfully inserted then notify the user / else notify them of the error
							success: function (data) {

								// Reset Form
								document.getElementById("formAssignPartnerProject").reset();

								var modalOptionPPAssign = document.getElementById("buttonOptionsPPAssign").value;

								if (modalOptionPPAssign == 1) {
									$('#partnerAssignModal').modal('hide');
									$("#displayResult").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully assigned a Partner to a Project!</div>');
									$("#displayResult").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
								} else {
									$("#displayResultPartnerProjectAssign").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully assigned a Partner to a Project!</div>');
									$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
								}
								// var defaultPPAssignDiv = document.getElementById("defaultPPAssign");
								// var submitPPAssignDiv = document.getElementById("submitPPAssign");
								// defaultPPAssignDiv.style.display = "none";
								// submitPPAssignDiv.style.display = "block";
							}
							, error: function () {
								$("#displayResultPartnerProjectAssign").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Sorry your Partner was not assigned to a Project! Error:' + data + '</div>');
								$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
							}
						});
					}
					return false;
				}
			</script>

			<div id="displayResultPartnerProjectAssign"></div>

			<form role="form" id="formAssignPartnerProject" name="formAssignPartnerProject" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input disabled type="hidden" name="loggedInUserPPAssign" id="loggedInUserPPAssign" value="<?php echo $loggedInUser; ?>" class="form-control"><br />

				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Project <span class="symbol required"></span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" id="projectSelect" name="projectSelect">
									<option selected="selected" value="0" >Select Project</option>
									<?php
                                    // Get Users
                                    try {
                                        $pdoQueryProjects = 'SELECT * FROM ierc_projects  WHERE delete_date IS NULL ORDER BY ierc_project_title ASC';

                                        $pdoResultProjects = $db->prepare($pdoQueryProjects);

                                        $pdoExecProjects = $pdoResultProjects->execute();

                                        if ($pdoExecProjects) {
                                            if ($pdoResultProjects->rowCount() > 0) {
                                                foreach ($pdoResultProjects as $rowProjects) {
                                                    $projectId = $rowProjects['ierc_project_id'];
                                                    $projectTitle = $rowProjects['ierc_project_title'];
                                                    echo "<option value=\"{$projectId}\" >{$projectTitle}</option>";
                                                }
                                            } else {
                                                echo 'No projects found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Partner <span class="symbol required"></span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" id="partnerSelect" name="partnerSelect">
									<option selected="selected" value="0" >Select Partner</option>
									<?php
                                    // Get Users
                                    try {
                                        $pdoQueryPartners = 'SELECT * FROM ierc_partners WHERE delete_date IS NULL ORDER BY ierc_partner_name ASC';

                                        $pdoResultPartners = $db->prepare($pdoQueryPartners);

                                        $pdoExecPartners = $pdoResultPartners->execute();

                                        if ($pdoExecPartners) {
                                            if ($pdoResultPartners->rowCount() > 0) {
                                                foreach ($pdoResultPartners as $rowPartners) {
                                                    $partnerId = $rowPartners['ierc_partner_id'];
                                                    $partnerName = $rowPartners['ierc_partner_name'];
                                                    echo "<option value=\"{$partnerId}\" >{$partnerName}</option>";
                                                }
                                            } else {
                                                echo 'No partners found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</form>
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-6">
					<select class="form-control" id="buttonOptionsPPAssign" name="buttonOptionsPPAssign">
						<option selected="selected" value="1" >Create and Close</option>
						<option value="2" >Create and Add More</option>
					</select>
				</div>
				<div id="defaultPPAssign" style="display: block;">
					<div class="col-sm-2">
						<a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-block" href="#" onclick="submitAssignPartnerProject();" title="Assign a Partner to a Project" >Create</a>
					</div>
				</div>
				<div id="submitPPAssign" style="display: none;">
					<div class="col-sm-2"></div>
					<div class="col-sm-2">
						<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end: VIEW USERS MODAL -->

	<!-- start: NEW USERS MODAL -->
	<div id="newUsersModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="600" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Assign Partner to Project</h4>
		</div>
		<div class="modal-body">
		<script>
			// JavaScript Document
			function submitAddNewLampFitting() {
				// Get the values from the form and store as variables
				var uId = document.getElementById("uId").value;
				var lightingLampFittingName = document.getElementById("lightingLampFittingName1").value;
				var lightingLampFittingDescription = document.getElementById("lightingLampFittingDescription1").value;

				// Returns successful data submission message when the entered information is stored in database.
				var dataString = 'uId=' + uId + '& lightingLampFittingName=' + lightingLampFittingName + '& lightingLampFittingDescription=' + lightingLampFittingDescription;

				// Simple check to ensure the person has put in the required data - if not give them a notification
				if (lightingLampFittingName == '') {
					$("#resultNewLampFitting").html('<div class="alert alert-warning fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-exclamation-triangle"></i>You have not entered a resultNewLampCategory Name!</div>');
				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "emv_lighting_new_lamp_fitting_upload.php",
						data: dataString,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultNewLampFitting").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new lamp type! ' + data + ' </div>');
							document.getElementById("formNewProject").reset();
						}
						, error: function () {
							$("#resultNewProject").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Sorry your new lamp type was not added! ' + data + '</div>');
						}
					});
				}
				return false;
			}

			function checkEntryTitle(uId, titleValue){

				var dataStringTitle = 'uId=' + uId + '& titleValue=' + titleValue;

				if (titleValue == '') {

				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "ajax_check_title.php",
						data: dataStringTitle,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultCheckEntry").html(data);
						}
						, error: function () {
							$("#resultCheckEntry").html(data);
						}
					});
				}
				return false;
			}

			function checkEntryAcronym(val){
				alert(val);
			}

			function checkEntryCode(val){
				alert(val);
			}

      function checkInt(value) {
        var x = parseFloat(value);
        return !isNaN(value) && (x | 0) === x;
      }

			</script>


			<div id="resultNewProject"></div>
			<div id="resultCheckEntry"></div>

			<form role="form" id="formNewProject" name="formNewProject" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Title <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectTitle" name="newProjectTitle" onChange="checkEntryTitle(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Acronym <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectAcronym" name="newProjectAcronym" onChange="checkEntryAcronym(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Code <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectCode" name="newProjectCode" placeholder="3 Letter Code - EMV, ABC, TUV" onChange="checkEntryCode(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Description <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<textarea rows="5" class="form-control" id="newProjectDescription" name="newProjectDescription"></textarea>
					</div>
				</div>
			</form>
			<!-- end: FORM WIZARD PANEL -->
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-6">
					<select class="form-control" id="buttonOptionsPPAssign" name="buttonOptionsPPAssign">
						<option selected="selected" value="1" >Create and Close</option>
						<option value="2" >Create and Add More</option>
					</select>
				</div>
				<div id="defaultPPAssign" style="display: block;">
					<div class="col-sm-2">
						<a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-block" href="#" onclick="submitAssignPartnerProject();" title="Assign a Partner to a Project" >Create</a>
					</div>
				</div>
				<div id="submitPPAssign" style="display: none;">
					<div class="col-sm-2"></div>
					<div class="col-sm-2">
						<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end: NEW USERS MODAL -->

	<!-- start: VIEW PARTNERS MODAL -->
	<div id="viewPartnersModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="600" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Assign Partner to Project</h4>
		</div>
		<div class="modal-body">
			<br />

			<script>
				// JavaScript Document
				function submitAssignPartnerProject() {

					// Get the values from the form and store as variables
					var uId = document.getElementById("loggedInUserPPAssign").value;
					var projectSelect = document.getElementById("projectSelect").value;
					var partnerSelect = document.getElementById("partnerSelect").value;

					// Returns successful data submission message when the entered information is stored in database.
					var dataStringAssignPartnerProject = 'uId=' + uId
																								+ '& projectSelect=' + projectSelect
																									+ '& partnerSelect=' + partnerSelect;

					// Simple check to ensure the person has put in the required data - if not give them a notification
					if (projectSelect == 0 || partnerSelect == 0) {
						$("#displayResultPartnerProjectAssign").html('<div class="alert alert-warning fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-exclamation-triangle"></i>You have empty fields!</div>');
						$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
					}
					else {
						// AJAX code to submit form.
						$.ajax({
							type: "POST",
							url: "ajax_process_assign_partner_project.php",
							data: dataStringAssignPartnerProject,
							cache: false, // If successfully inserted then notify the user / else notify them of the error
							success: function (data) {

								// Reset Form
								document.getElementById("formAssignPartnerProject").reset();

								var modalOptionPPAssign = document.getElementById("buttonOptionsPPAssign").value;

								if (modalOptionPPAssign == 1) {
									$('#partnerAssignModal').modal('hide');
									$("#displayResult").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully assigned a Partner to a Project!</div>');
									$("#displayResult").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
								} else {
									$("#displayResultPartnerProjectAssign").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully assigned a Partner to a Project!</div>');
									$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
								}
								// var defaultPPAssignDiv = document.getElementById("defaultPPAssign");
								// var submitPPAssignDiv = document.getElementById("submitPPAssign");
								// defaultPPAssignDiv.style.display = "none";
								// submitPPAssignDiv.style.display = "block";
							}
							, error: function () {
								$("#displayResultPartnerProjectAssign").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Sorry your Partner was not assigned to a Project! Error:' + data + '</div>');
								$("#displayResultPartnerProjectAssign").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
							}
						});
					}
					return false;
				}
			</script>

			<div id="displayResultPartnerProjectAssign"></div>

			<form role="form" id="formAssignPartnerProject" name="formAssignPartnerProject" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input disabled type="hidden" name="loggedInUserPPAssign" id="loggedInUserPPAssign" value="<?php echo $loggedInUser; ?>" class="form-control"><br />

				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Project <span class="symbol required"></span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" id="projectSelect" name="projectSelect">
									<option selected="selected" value="0" >Select Project</option>
									<?php
                                    // Get Users
                                    try {
                                        $pdoQueryProjects = 'SELECT * FROM ierc_projects  WHERE delete_date IS NULL ORDER BY ierc_project_title ASC';

                                        $pdoResultProjects = $db->prepare($pdoQueryProjects);

                                        $pdoExecProjects = $pdoResultProjects->execute();

                                        if ($pdoExecProjects) {
                                            if ($pdoResultProjects->rowCount() > 0) {
                                                foreach ($pdoResultProjects as $rowProjects) {
                                                    $projectId = $rowProjects['ierc_project_id'];
                                                    $projectTitle = $rowProjects['ierc_project_title'];
                                                    echo "<option value=\"{$projectId}\" >{$projectTitle}</option>";
                                                }
                                            } else {
                                                echo 'No projects found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Partner <span class="symbol required"></span>
							</label>
							<div class="col-sm-7">
								<select class="form-control" id="partnerSelect" name="partnerSelect">
									<option selected="selected" value="0" >Select Partner</option>
									<?php
                                    // Get Users
                                    try {
                                        $pdoQueryPartners = 'SELECT * FROM ierc_partners WHERE delete_date IS NULL ORDER BY ierc_partner_name ASC';

                                        $pdoResultPartners = $db->prepare($pdoQueryPartners);

                                        $pdoExecPartners = $pdoResultPartners->execute();

                                        if ($pdoExecPartners) {
                                            if ($pdoResultPartners->rowCount() > 0) {
                                                foreach ($pdoResultPartners as $rowPartners) {
                                                    $partnerId = $rowPartners['ierc_partner_id'];
                                                    $partnerName = $rowPartners['ierc_partner_name'];
                                                    echo "<option value=\"{$partnerId}\" >{$partnerName}</option>";
                                                }
                                            } else {
                                                echo 'No partners found!';
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo $e->getMessage();
                                    }
                                    ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</form>
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-6">
					<select class="form-control" id="buttonOptionsPPAssign" name="buttonOptionsPPAssign">
						<option selected="selected" value="1" >Create and Close</option>
						<option value="2" >Create and Add More</option>
					</select>
				</div>
				<div id="defaultPPAssign" style="display: block;">
					<div class="col-sm-2">
						<a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-block" href="#" onclick="submitAssignPartnerProject();" title="Assign a Partner to a Project" >Create</a>
					</div>
				</div>
				<div id="submitPPAssign" style="display: none;">
					<div class="col-sm-2"></div>
					<div class="col-sm-2">
						<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end: VIEW PARTNERS MODAL -->

	<!-- start: NEW PARTNERS MODAL -->
	<div id="newPartnersModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="600" style="display: none;">
		<div class="modal-header modalStyleHeader">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="color: red;">&times;</button>
			<h4 class="modal-title">Assign Partner to Project</h4>
		</div>
		<div class="modal-body">
		<script>
			// JavaScript Document
			function submitAddNewLampFitting() {
				// Get the values from the form and store as variables
				var uId = document.getElementById("uId").value;
				var lightingLampFittingName = document.getElementById("lightingLampFittingName1").value;
				var lightingLampFittingDescription = document.getElementById("lightingLampFittingDescription1").value;

				// Returns successful data submission message when the entered information is stored in database.
				var dataString = 'uId=' + uId + '& lightingLampFittingName=' + lightingLampFittingName + '& lightingLampFittingDescription=' + lightingLampFittingDescription;

				// Simple check to ensure the person has put in the required data - if not give them a notification
				if (lightingLampFittingName == '') {
					$("#resultNewLampFitting").html('<div class="alert alert-warning fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-exclamation-triangle"></i>You have not entered a resultNewLampCategory Name!</div>');
				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "emv_lighting_new_lamp_fitting_upload.php",
						data: dataString,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultNewLampFitting").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Completed! </strong> You successfully added a new lamp type! ' + data + ' </div>');
							document.getElementById("formNewProject").reset();
						}
						, error: function () {
							$("#resultNewProject").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Sorry your new lamp type was not added! ' + data + '</div>');
						}
					});
				}
				return false;
			}

			function checkEntryTitle(uId, titleValue){

				var dataStringTitle = 'uId=' + uId + '& titleValue=' + titleValue;

				if (titleValue == '') {

				}
				else {
					// AJAX code to submit form.
					$.ajax({
						type: "POST",
						url: "ajax_check_title.php",
						data: dataStringTitle,
						cache: false, // If successfully inserted then notify the user / else notify them of the error
						success: function (data) {
							$("#resultCheckEntry").html(data);
						}
						, error: function () {
							$("#resultCheckEntry").html(data);
						}
					});
				}
				return false;
			}

			function checkEntryAcronym(val){
				alert(val);
			}

			function checkEntryCode(val){
				alert(val);
			}
			</script>


			<div id="resultNewProject"></div>
			<div id="resultCheckEntry"></div>

			<form role="form" id="formNewProject" name="formNewProject" method="post" enctype="multipart/form-data" class="smart-wizard form-horizontal" >
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Title <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectTitle" name="newProjectTitle" onChange="checkEntryTitle(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Acronym <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectAcronym" name="newProjectAcronym" onChange="checkEntryAcronym(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Code <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<input type="text" class="form-control" id="newProjectCode" name="newProjectCode" placeholder="3 Letter Code - EMV, ABC, TUV" onChange="checkEntryCode(<?php echo $loggedInUser; ?>, this.value);">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
						Description <span class="symbol required"></span>
					</label>
					<div class="col-sm-7">
						<textarea rows="5" class="form-control" id="newProjectDescription" name="newProjectDescription"></textarea>
					</div>
				</div>
			</form>
			<!-- end: FORM WIZARD PANEL -->
			<br />
		</div>
		<div class="modal-footer">
			<div class="row">
				<div class="col-sm-2"></div>
				<div class="col-sm-6">
					<select class="form-control" id="buttonOptionsPPAssign" name="buttonOptionsPPAssign">
						<option selected="selected" value="1" >Create and Close</option>
						<option value="2" >Create and Add More</option>
					</select>
				</div>
				<div id="defaultPPAssign" style="display: block;">
					<div class="col-sm-2">
						<a class="btn btn-default btn-block" data-dismiss="modal"> Cancel </a>
					</div>
					<div class="col-sm-2">
						<a class="btn btn-success btn-block" href="#" onclick="submitAssignPartnerProject();" title="Assign a Partner to a Project" >Create</a>
					</div>
				</div>
				<div id="submitPPAssign" style="display: none;">
					<div class="col-sm-2"></div>
					<div class="col-sm-2">
						<a class="btn btn-danger btn-block"  data-dismiss="modal">Close</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end: USERS ASSIGN MODAL -->


	<!-- start: MAIN JAVASCRIPTS -->
    <!--[if lt IE 9]>
        <script src="../common/bower_components/respond/dest/respond.min.js"></script>
        <script src="../common/bower_components/Flot/excanvas.min.js"></script>
        <script src="../common/bower_components/jquery-1.x/dist/jquery.min.js"></script>
        <![endif]-->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="../common/bower_components/jquery/dist/jquery.min.js"></script>
    <!--<![endif]-->
    <script type="text/javascript" src="../common/bower_components/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="../common/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../common/bower_components/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script type="text/javascript" src="../common/bower_components/blockUI/jquery.blockUI.js"></script>
    <script type="text/javascript" src="../common/bower_components/iCheck/icheck.min.js"></script>
    <script type="text/javascript" src="../common/bower_components/perfect-scrollbar/js/min/perfect-scrollbar.jquery.min.js"></script>
    <script type="text/javascript" src="../common/bower_components/jquery.cookie/jquery.cookie.js"></script>
    <script type="text/javascript" src="../common/bower_components/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript" src="../common/assets/js/min/main.min.js"></script>
    <script src="../common/bower_components/Flot/jquery.flot.js"></script>
    <script src="../common/bower_components/Flot/jquery.flot.pie.js"></script>
    <script src="../common/bower_components/Flot/jquery.flot.resize.js"></script>
    <script src="../common/assets/plugin/jquery.sparkline.min.js"></script>
    <script src="../common/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
    <script src="../common/bower_components/jqueryui-touch-punch/jquery.ui.touch-punch.min.js"></script>
    <script src="../common/bower_components/moment/min/moment.min.js"></script>
    <script src="../common/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="../common/assets/js/min/index.min.js"></script>
		<script src="../common/assets/js/tokenize2.js"></script>
		<script src="../common/bower_components/bootstrap-fileinput/js/plugins/canvas-to-blob.min.js"></script>
    <script src="../common/bower_components/bootstrap-fileinput/js/fileinput.min.js"></script>
  <!--  <script src="../common/bower_components/jquery-validation/dist/jquery.validate.min.js"></script>-->
    <script src="../common/bower_components/jquery-validation/dist/jquery.validate.js"></script>
    <script src="../common/bower_components/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
    <script src="../common/assets/js/min/form-wizard.min.js"></script>
		<script src="../common/bower_components/bootstrap-modal/js/bootstrap-modal.js"></script>
    <script src="../common/bower_components/bootstrap-modal/js/bootstrap-modalmanager.js"></script>
    <script src="../common/assets/js/min/ui-modals.min.js"></script>
    <script src="../common/bower_components/bootstrap-maxlength/src/bootstrap-maxlength.js"></script>
    <script src="../common/bower_components/autosize/dist/autosize.min.js"></script>
    <script src="../common/bower_components/select2/dist/js/select2.min.js"></script>
    <script src="../common/bower_components/jquery.maskedinput/dist/jquery.maskedinput.min.js"></script>
    <script src="../common/bower_components/jquery-maskmoney/dist/jquery.maskMoney.min.js"></script>
    <script src="../common/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="../common/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="../common/bower_components/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <script src="../common/bower_components/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
    <script src="../common/bower_components/jquery.tagsinput/src/jquery.tagsinput.js"></script>
    <script src="../common/assets/js/min/form-elements.min.js"></script>
		<script src="../common/bower_components/moment/min/moment.min.js"></script>
    <script src="../common/bower_components/summernote/dist/summernote.min.js"></script>
    <script src="../common/bower_components/ckeditor/ckeditor.js"></script>
    <script src="../common/bower_components/ckeditor/adapters/jquery.js"></script>
		<script src="../common/assets/js/jquery.ui.widget.js"></script>
		<script src="../common/assets/js/jquery.iframe-transport.js"></script>
		<script src="../common/assets/js/jquery.fileupload.js"></script>
		<script src="jquery.multiselect.js"></script>

    <script>

    // Get the tasks associated with a project
    function getTasks(id,level){

      // Selecting the element where the tasks will be placed.
        if (level == "Activity"){
          var taskElement = $("#newActTask");
        }else if (level == "WorkPackage"){
          var taskElement = $("#newWpTask");
        }else if (level == "Checklist"){
        var taskElement = $("#newCheckTask");
      }

        $("#newActWp").html('<option value="0">Select Task First</option>');
        $("#newActWp").attr('disabled',true);
        $("#newCheckWp").html('<option value="0">Select Task First</option>');
        $("#newCheckWp").attr('disabled',true);

      	var dataString = 'projectID='+ id.value;

        $.ajax({
          type: "POST",
          url: "ajax_get_tasks.php",
          data: dataString,
          cache: false, // If successfully inserted then notify the user / else notify them of the error
          success: function (data) {
            //alert("Success:"+data)
            if (data!=""){
              data = '<option value="0">Select Task</option>'+data;
              taskElement.html(data);
              taskElement.attr('disabled',false);
            }
            else{
              taskElement.html('<option value="0">Select Project First</option>');
              taskElement.attr('disabled',true);
            }
          }
          , error: function () {
            alert("Error:"+data);
          }
        });

    }

    // Get the WorkPackages associated with a task
    function getWorkPackages(id,level){      
      // Selecting the element where the tasks will be placed.
      if (level == "Activity"){
          var wpElement = $("#newActWp");
        }else if (level == "Checklist"){
          var wpElement = $("#newCheckWp");
      }
      
      	var dataString = 'taskID='+ id.value;
     
        $.ajax({
          type: "POST",
          url: "ajax_get_workpackages.php",
          data: dataString,
          cache: false, // If successfully inserted then notify the user / else notify them of the error
          success: function (data) {
            //alert("Success:"+data)
            if (data!=""){
              data = '<option value="0">Select Work Package</option>'+data;
              wpElement.html(data);              
              wpElement.attr('disabled',false);             
            }
            else{
              wpElement.html('<option value="0">Select Task First</option>');
              wpElement.attr('disabled',true);
            }           
          }
          , error: function () {
            alert("Error:"+data);
          }
        });

    }

    // Get the Activities associated with a workpackage
    function getActivities(id,level){

      // Selecting the element where the tasks will be placed.
        if (level == "Checklist"){
          var actElement = $("#newCheckAct");
        }

      	var dataString = 'workpackageID='+ id.value;

        $.ajax({
          type: "POST",
          url: "ajax_get_activities.php",
          data: dataString,
          cache: false, // If successfully inserted then notify the user / else notify them of the error
          success: function (data) {
            //alert("Success:"+data)
            if (data!=""){
              data = '<option value="0">Select Activity</option>'+data;
              actElement.html(data);
              actElement.attr('disabled',false);
            }
            else{
              actElement.html('<option value="0">Select Work Package First</option>');
              actElement.attr('disabled',true);
            }
          }
          , error: function () {
            alert("Error:"+data);
          }
        });

    }


// Get the task details associated with a project
function getTaskDetails(id,level){  

  var uId = <?php echo $loggedInUser; ?>;
  // Selecting the element where the tasks will be placed.
    if (level == "Activity"){
      var taskElement = $("#newActTask");
    }else if (level == "WorkPackage"){
      var taskElement = $("#newWpTask");
    }else if (level == "Checklist"){
    var taskElement = $("#newCheckTask");
  }
  
  var dataString = 'uId=' + uId + '&projectID='+ id.value;
  //alert("getTaskDetails2 -"+dataString);
  $.ajax({
    type: "POST",
    url: "ajax_get_task_details.php",
    data: dataString,
    cache: false, // If successfully inserted then notify the user / else notify them of the error
    success: function (data) {
      //alert("Success:"+data)
      
       $("#task_table_body").html(data);
    }
    , error: function () {
      alert("Error:"+data);
    }
  });

}
        
    var dataString = 'projectID='+ <?php echo $project_id; ?>;
    // Get the Project Details   
    $.ajax({
      type: "POST",
      url: "get_overview_projects.php",
      data: dataString,
      cache: false, // If successfully inserted then notify the user / else notify them of the error
      success: function (data) {          
        //alert(data);
       
        var projectsData = JSON.parse(data);

        var chart = AmCharts.makeChart("overview_chart", {
        	"type": "serial",
             "theme": "light",
        	"categoryField": "acronym",
          "legend": {
              "autoMargins": false,
              "borderAlpha": 0.2,
              "equalWidths": false,
              "horizontalGap": 10,
              "markerSize": 10,
              "useGraphSettings": true,
              "valueAlign": "left",
              "valueWidth": 0
          },
        	"rotate": false,
        	"startDuration": 1,
        	"categoryAxis": {
        		"gridPosition": "start",
        		"position": "left"
        	},
        	"trendLines": [],
        	"graphs": [
                {
        			"balloonText": "To Do :[[value]]",
        			"fillAlphas": 0.8,
        			"id": "AmGraph-2",
        			"lineAlpha": 0.2,
        			"title": "To Do",
        			"type": "column",
        			"valueField": "activity_todo"
                },
                {
        			"balloonText": "In Progress :[[value]]",
        			"fillAlphas": 0.8,
        			"id": "AmGraph-3",
        			"lineAlpha": 0.2,
        			"title": "In Progress",
        			"type": "column",
        			"valueField": "activity_inprogress"
                },
                {
        			"balloonText": "Testing :[[value]]",
        			"fillAlphas": 0.8,
        			"id": "AmGraph-4",
        			"lineAlpha": 0.2,
        			"title": "Testing",
        			"type": "column",
        			"valueField": "activity_testing"
                },
                {
        			"balloonText": "Issue :[[value]]",
        			"fillAlphas": 0.8,
        			"id": "AmGraph-5",
        			"lineAlpha": 0.2,
        			"title": "Issue",
        			"type": "column",
        			"valueField": "activity_issue"
        		},
        		{
        			"balloonText": "Complete :[[value]]",
        			"fillAlphas": 0.8,
        			"id": "AmGraph-6",
        			"lineAlpha": 0.2,
        			"title": "Complete",
        			"type": "column",
        			"valueField": "activity_complete"
                }
        	],
        	"guides": [],
        	"valueAxes": [
        		{
        			"id": "ValueAxis-1",
        			"position": "top",
        			"axisAlpha": 0
        		}
        	],
        	"allLabels": [],
        	"balloon": {},
        	"titles": [],
          "dataProvider": projectsData,
            "export": {
            	"enabled": true
             }

        });

      }
      , error: function () {
        alert(data);
      }
    });


    $.ajax({

			type: "POST",
			contentType: 'application/json; charset=utf-8',
			url: "get_project_status.php",
			cache: false, // If successfully inserted then notify the user / else notify them of the error
			success: function (data) {
			//alert("Success-"+data);
			  var dataSet = JSON.parse(data);

                var options = {
                    series: {
                            pie: {
                                show: true,
                                innerRadius: 0.3,
                                radius: 1,
                                label: {
                                    show: true,
                                    radius: 0.72,
                                    formatter: function (label, series) {
                                        return '<span style="text-align:center">'+label +'<p style="text-align:center">'+ series.data[0][1]+'</p></span>';
                                    },
                                    threshold: 0.1                                    
                                }
                            }
                            },
                            grid: {
                            hoverable: true
                            },
                            tooltip: true,
                            tooltipOpts: {
                                cssClass: "flotTip",                                
                                content: "%p.0%, %s",
                                shifts: {
                                    x: 20,
                                    y: 0
                                },
                                defaultTheme: false
                            }
                            
                        };



				$.plot($("#status_chart"), dataSet, options);

			}
			, error: function () {
				alert(data);
			}
		});

         
      $.ajax({
        type: "POST",
        url: "get_project_resources.php",
        data: dataString,
        cache: false, // If successfully inserted then notify the user / else notify them of the error
        success: function (data) {
          //alert(data);
          //$("#resultCheckEntry").html(data);
          //$("#resultCheckEntry").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
          var dataSet = JSON.parse(data);

          var options = {
            series: {
                pie: {
                        show: true,
                        tilt: 1,
                        innerRadius: 0.3,
                        label: {
                                show: true
                        }
                }
            }
          };

          $.plot($("#project_resource_chart"), dataSet, options);

        }
        , error: function () {
          alert(data);
        }
      });

        /*
        * jQuery Confirmation Dialog Popup/Modal Window for Deletion approvals and other
        *  similar user actions that require user approval.  This is to replace the browsers
        *  default Dialog confirmation windows.
        *  http://tutorialzine.com/2010/12/better-confirm-box-jquery-css3/
        */ (function($) {
            $.confirm = function(params) {
                if ($('#confirmOverlay').length) {
                    // A confirm is already shown on the page:
                    return false;
                }

                var buttonHTML = '';
                $.each(params.buttons, function(name, obj) {

                    // Generating the markup for the buttons:
                    buttonHTML += '<a href="#" class="button ' + obj['class'] + '">' + name + '</a>';
                    if (!obj.action) {
                        obj.action = function() {};
                    }
                });

                var markup = ['<div id="confirmOverlay">', '<div id="confirmBox">', '<h1>', params.title, '</h1>', '<p>', params.message, '</p>', '<div id="confirmButtons">',
                buttonHTML, '</div></div></div>'].join('');

                $(markup).hide().appendTo('body').fadeIn();

                var buttons = $('#confirmBox .button'),
                    i = 0;

                $.each(params.buttons, function(name, obj) {
                    buttons.eq(i++).click(function() {

                        // Calling the action attribute when a
                        // click occurs, and hiding the confirm.

                        obj.action();
                        $.confirm.hide();
                        return false;
                    });
                });
            }
            $.confirm.hide = function() {
                $('#confirmOverlay').fadeOut(function() {
                    $(this).remove();
                });
            }
        })(jQuery);

        $(function() {
            $(".column").sortable({
                connectWith: ".column"
            });

            // Event fired when order cards has been moved to a new column
            $(".column").on("sortreceive", function(event, ui){
               
                var uId = <?php echo $loggedInUser; ?>;                
                var projectId = <?php echo $project_id; ?>;                
                var activityId = ui.item.attr('id');                
				var dataStatus = $(this).data('status-id');				

				var dataStringStatusUpdate = 'uId='+ uId + ' & activityId='+ activityId + ' & dataStatus='+ dataStatus + ' & projectId='+ projectId;
				//alert ("Sorting"+dataStringStatusUpdate);

                if (activityId == '') {

                } else {
                    // AJAX code to submit form.
                    $.ajax({
                        type: "POST",
                        url: "ajax_scrum_status_update.php",
                        data: dataStringStatusUpdate,
                        cache: false, // If successfully inserted then notify the user / else notify them of the error
                        success: function (data) {
                            //alert(data)
                            $("#displayScrumMessage").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Success! </strong> Status Updated!</div>');
                            $("#displayScrumMessage").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
                        }
                        , error: function () {
                            $("#displayScrumMessage").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong> Status Not Updated! '+ data +'</div>');
                            $("#displayScrumMessage").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
                        }
                    });
                }
                return false;
            });
        });
        			
      $("#activityUpdate").on("show", function () {
			$("body").addClass("modal-open");
			}).on("hidden", function () {
			$("body").removeClass("modal-open")
		});

      function openModalEdit (aID) {         
        var activityId = aID;       
        $('#registryRecordModal').modal('show');       
        // AJAX code to submit form.
        $.ajax({
            type: 'POST',
            url:"ajax_display_activity_details.php",
            data: 'activityId=' + activityId,
            cache:false,
            dataType: "json",
            async: false,
            beforeSend: function (xhr) {
                if (xhr && xhr.overrideMimeType) {
                xhr.overrideMimeType('application/json;charset=utf-8');
                }
            },
            success:function(result)
            {
               document.getElementById("updateActProject").options[0].value = result[0];
               document.getElementById("updateActProject").options[0].text = result[1];

               document.getElementById("updateActTask").options[0].value = result[2];
               document.getElementById("updateActTask").options[0].text = result[3];

               document.getElementById("updateActWp").options[0].value = result[4];
               document.getElementById("updateActWp").options[0].text = result[5];              
               
               $("#openedActivityId").val(result[6]);
               $("#updateActTitle").val(result[7]);
               $("#updateActDescription").val(result[8]);              
              
               if (result[9]== ""){
                  result[9] = '0';
                } 
                  $('#updateActStatus option[value='+result[9]+']').attr('selected','selected');
              
                if (result[10]== ""){
                  result[10] = '0';
                } 
                  $('#updateActPriority option[value='+result[10]+']').attr('selected','selected');
    
                  $("#updateActProgress").val(result[11]);

                  if (result[12]== ""){
                  result[12] = '0';
                } 
                  $('#updateActAssigned option[value='+result[12]+']').attr('selected','selected');
                  
                  $("#updateActStart").val(result[13]);
                  $("#updateActEnd").val(result[14]); 
             
                $('#activityUpdate').modal('show');                
            }, error: function () {
                alert("Error - "+result);
                document.getElementById("displayScrumMessage").value = 'Error - Please Try Again!';
                $('#activityUpdate').modal('show');

            }
        });
        }

       
        function taskUpdate(){
            alert("taskUpdate");
            
            var uId = document.getElementById("loggedInUser").value;
            var updateTaskId = document.getElementById("openedTaskId").value;
            var updateTaskTitle = document.getElementById("updateTaskTitle").value;
            var updateTaskColour = document.getElementById("updateTaskColour").value;
            var updateTaskDescription = document.getElementById("updateTaskDescription").value;            
            var updateTaskStatus = document.getElementById("updateTaskStatus").value;
            var updateTaskPriority = document.getElementById("updateTaskPriority").value;
            var updateTaskProgress = document.getElementById("updateTaskProgress").value;
            var updateTaskAssigned = document.getElementById("updateTaskAssigned").value;
            var updateTaskStart = document.getElementById("updateTaskStart").value;
            var updateTaskEnd = document.getElementById("updateTaskEnd").value;
           
            var dataString = 'uId='+ uId +
                                    ' & updateTaskId='+ updateTaskId +
                                    ' & updateTaskTitle='+ updateTaskTitle +
                                    ' & updateTaskDescription='+ updateTaskDescription +
                                    ' & updateTaskColour='+ updateTaskColour+
                                    ' & updateTaskStatus='+ updateTaskStatus +
                                    ' & updateTaskPriority='+ updateTaskPriority +
                                    ' & updateTaskProgress='+ updateTaskProgress +
                                    ' & updateTaskAssigned='+ updateTaskAssigned +
                                    ' & updateTaskStart='+ updateTaskStart +
                                    ' & updateTaskEnd='+ updateTaskEnd;
            alert(dataString);
            $.ajax({
                    type: "POST"
                    , url: "ajax_update_task.php"
                    , data: dataString
                    , async: false
                    , cache: false, // If successfully inserted then notify the user / else notify them of the error
                    success: function (data) {
                        alert("Success -"+data);	                       				

                        if (data != '0'){
                            $("#displayTaskMessage").html('<div class="alert alert-success fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-check-circle"></i><strong> Success! </strong> Task Item Updated - <strong>'+updateTaskTitle+'</strong></div>');
                            $("#displayTaskMessage").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
   
                        }
                        else{
                            $("#displayTaskMessage").html('<div class="alert alert-danger fade in"><button data-dismiss="alert" class="close">&times;</button><i class="fa fa-times-circle"></i><strong> Error! </strong>'+ data +'</div>');
                            $("#displayTaskMessage").fadeIn('slow').animate({opacity: 1.0}, 1500).fadeOut('slow');
                        }

                    }
                    , error: function () {
                        alert("Error -"+data);
                        $("#displayScrumMessage").html(data);
                        //$("#displayMockup").html("Error in Display Registry Mockup");
                    }
                });
        }
            

  </script>

  
  <script>
    $(document).ready(function(){

      $("#newActStart").datepicker();
      $("#newActEnd").datepicker();

      $("#taskStart").datepicker();
      $("#taskEnd").datepicker();
      $("#newWpStart").datepicker();
      $("#newWpEnd").datepicker();
      

     // $("#formNewCheck").validate();


    });    

  </script>

	</body>

	</html>
