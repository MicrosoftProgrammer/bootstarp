<?php
function fnSideBar(){
    $html ='
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                        <form name="searchForm" action="" method="post">
                            <div class="input-group custom-search-form">
                                <input type="text" name="Keyword" required class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="../dashboard/index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-cogs fa-fw"></i> Configuration<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../productfield/viewproductfield.php"><i class="fa fa-bars fa-fw"></i> Product Fields</a>
                                </li>
                                <li>
                                    <a href="../productfieldtype/viewproductfieldtype.php"><i class="fa fa-exchange fa-fw"></i> Product Field Types</a>
                                </li>
                                <li>
                                    <a href="../productfieldvalue/viewproductfieldvalue.php"><i class="fa fa-flag fa-fw"></i> Product Field Type Values</a>
                                </li>
                                <li>
                                    <a href="../mapping/fieldmapping.php"><i class="fa fa-map fa-fw"></i> Category Field Mapping</a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-shopping-cart fa-fw"></i> Products<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../category/viewcategories.php"><i class="fa fa-cog fa-fw"></i> Categories</a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa fa-list fa-fw"></i> Products
                                    <span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li>
                                            <a href="morris.html"><i class="fa fa-tag fa-fw"></i> Add Product</a>
                                        </li>
                                        <li>
                                            <a href="#"><i class="fa fa-tags fa-fw"></i> Add Bulk</a>
                                                
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>  
                        <li>
                            <a href="#"><i class="fa fa-users fa-fw"></i> Users<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="../user/viewusers.php"><i class="fa fa-user fa-fw"></i>User</a>
                                </li>
                                <li>
                                    <a href="morris.html"><i class="fa fa-sitemap fa-fw"></i>Permissions</a>
                                </li>
                            </ul>
                        </li>  
                        <li>
                            <a href="#"><i class="fa fa-files-o fa-fw"></i> Reports<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="../reports/report.php?type=TC" class=""><i class="fa fa-file-o fa-fw">&nbsp;</i>Stock Report</a></li>
                                <li><a href="../reports/report.php?type=TCA"><i class="fa fa-file fa-fw">&nbsp;</i>Invoice Generator</a></li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>                                                                    
                    </ul>
                </div>
            </div>';

    return $html;
}

function fnTopLinks(){
    $html ='<ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="../settings/settings.php"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
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
                <a class="navbar-brand" href="#">'.$_SESSION["CompanyName"].'</a>
            </div>';

    return $html;
}
?>