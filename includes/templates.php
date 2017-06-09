<?php
function fnSideBar(){
    $menu = json_decode($_SESSION["Permissions"],TRUE);

    $html ='
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                        <img src="../../images/'.$_SESSION["Logo"].'" alt="Logo" class="img-responsive" />

                            <!-- /input-group -->
                        </li>';

        foreach($menu as $menuitem){
            if($menuitem["Status"]){
            $html =$html.'               
                        <li>
                            <a href="'.$menuitem["Path"].$menuitem["Page"].'"><i class="fa '.$menuitem["Icon"].' fa-fw"></i> '.$menuitem["PageName"].'';
            if(count($menuitem["SubPage"])>0){
                $html =$html.'<span class="fa arrow"></span>'; 
            }

            $html =$html.'</a>';
            if(count($menuitem["SubPage"])>0){
                $html =$html.'<ul class="nav nav-second-level">';
                foreach($menuitem["SubPage"] as $subitem){
                    if($subitem["Status"]){
                        $html =$html.' 
                            <li>
                                <a href="'.$subitem["Path"].$subitem["Page"].'">
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
?>