<?php
function fnSideBar(){
    $menu = json_decode($_SESSION["Permissions"],TRUE);
    $pageName = $altpageName = basename($_SERVER['PHP_SELF']);
    $html ='
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                        <img src="../../images/'.$_SESSION["Logo"].'" alt="Logo" class="logo" />

                            <!-- /input-group -->
                        </li>';
    $altpageName = str_replace("add","",$altpageName);
    $altpageName = str_replace("edit","",$altpageName);
    $altpageName = str_replace("view","",$altpageName);
    $altpageName = str_replace("user","users",$altpageName);
    $altpageName = str_replace("category","categories",$altpageName);
    $altpageName = str_replace("product","products",$altpageName);
    $altpageName = str_replace("bulkimport","products",$altpageName);
    $altpageName = str_replace("geninvoicecreator","invoicegen",$altpageName);
    $altpageName = str_replace("creator","",$altpageName);
    
    

        foreach($menu as $menuitem){
            if($menuitem["Status"]){
                $class="";
               
                foreach($menuitem["SubPage"] as $subitem){
                    if($subitem["Page"]== $pageName || strpos($subitem["Page"] , $altpageName) || $subitem["Page"] == $altpageName ){
                        $class="active";
                    }

                }
            $html =$html.'               
                        <li class="'.$class.'">
                            <a href="'.$menuitem["Path"].$menuitem["Page"].'"><i class="fa '.$menuitem["Icon"].' fa-fw"></i> '.$menuitem["PageName"].'';
            if(count($menuitem["SubPage"])>0){
                $html =$html.'<span class="fa arrow"></span>'; 
            }

            $html =$html.'</a>';
            if(count($menuitem["SubPage"])>0){
                $html =$html.'<ul class="nav nav-second-level">';
                foreach($menuitem["SubPage"] as $subitem){
                    if($subitem["Status"]){
                          $class="";
                          if($subitem["Page"]== $pageName || strpos($subitem["Page"] , $altpageName) || $subitem["Page"] == $altpageName ){
                        $class="active";
                    }
                        $html =$html.' 
                            <li>
                                <a class="'.$class.'"  href="'.$subitem["Path"].$subitem["Page"].'">
                                    <i class="fa '.$subitem["Icon"].' fa-fw"></i> '.$subitem["PageName"].'
                                </a>
                            </li>';     
                      }                   
                } 
                $html =$html.'</ul>';
            }
            $html =$html.'</li>';
        }
        }

        $html =$html.'                                                                  
                    </ul>
                </div>
            </div>';

    return $html;
}

function fnTopLinks(){
    $html ='<ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">                
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    Welcome , '.$_SESSION["Name"].'
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                    <li><a href="../user/profile.php"><i class="fa fa-tag fa-fw"></i> My Profile</a>
                        </li>
                        ';
                if($_SESSION["UserType"]=="1"){
                  $html =$html.'<li><a href="../dashboard/settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                  ';
                }
          $html =$html.' <li class="divider"></li>
                        <li><a href="../../login.php?mode=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>';
        return $html;
}

function fnMobileMenu(){
    $html ='<div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                
                <a class="navbar-brand" href="#">                
                '.$_SESSION["CompanyName"].'</a>
            </div>';

    return $html;
}

function fnGetPermissions($UserID){
    $sql = "select * from users where Deleted=0 and UserType!=1 and UserID=".$UserID;
    $sql.= " order by userID";
    $res=mysql_query($sql);
    $obj = mysql_fetch_object($res);

    $html= "<ul id='sortable' style='padding-left:0;display:inline-block'>";  
    $permissions = json_decode($obj->Permissions,TRUE);                                      
    for($k=0;$k<count($permissions);$k++) {    
        $checked= "";    
        if($permissions[$k]["Status"])  $checked= "checked";                                
            $html= $html.                                      
            "<li class='form-group col-md-12' style='list-style:none'>
                <label class='checkbox-inline'>
                    <input name='fields[]' disabled ".$checked." type='checkbox'/>".$permissions[$k]['PageName']."
                </label>";
                    if(count($permissions[$k]["SubPage"])>0){
                        $html= $html.  "<ul style='padding-left:10px'>"; 
                        $SubPage = $permissions[$k]["SubPage"]; 
                        for($z=0;$z<count($SubPage);$z++) {
                        $checked= "";
                            if($SubPage[$z]["Status"]) $checked= "checked";   
                    $html= $html. 
                "<li class='form-group col-md-12' style='list-style:none'>
                    <label class='checkbox-inline'>
                        <input name='fields[]' disabled ".$checked." type='checkbox'/>".$SubPage[$z]['PageName']."
                    </label>
                </li>";
                }
                $html= $html. "</ul>";
            }
            $html= $html. "</li>";
    } 

    $html= $html. "</ul>";
    return $html;
}

function fnGetLogs($LogID){
    $sql = "select * from userlog where LogID=".$LogID;
    $res=mysql_query($sql);
    $html ='<div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> User Logs
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="list-group">
                    <ul>';
    while($obj = mysql_fetch_object($res)){
         $html =$html.$obj->UserAction;
    }
                    
   $html =$html.'</ul>                           
                    </div>
                    <!-- /.list-group -->
                </div>
                <!-- /.panel-body -->
        </div>';



    return $html;
}
?>