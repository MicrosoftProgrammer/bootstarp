<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if(!isSuperAdmin())
    {
        header("location:../../login.php");
    }

    if ($_REQUEST['mode']=="update")
    {
        $UserID=$_REQUEST["User"];
        $sql="select * from users where UserID=".$UserID;      

        $res=mysql_query($sql);
        $obj=mysql_fetch_object($res);

        $permissions= json_decode($obj->Permissions,TRUE);
        $updated = array();

        foreach($permissions as $permission){
            $update = $permission;
            if(in_array($permission["PageID"],$_REQUEST["fields"])){

                $update["Status"]=1;                
            }
            else{
                $update["Status"]=0;     
            }
            
            if(count($permission["SubPage"]) > 0){
                $update["SubPage"]=array();
                foreach($permission["SubPage"] as $subpermission){
                        if(in_array($subpermission["PageID"],$_REQUEST["fields"])){
                        $subpermission["Status"]= 1;
                        
                    }
                    else{
                        $subpermission["Status"]= 0;      
                    }

                    $update["SubPage"][]=$subpermission;
                }
            }                
            $updated[] =$update;
        }

        $sql="update users set Permissions='".json_encode($updated)."' where UserID=".$UserID;    
        mysql_query($sql);

        $text="Field Updated Successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
    </head>
    <body>
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <?php echo fnMobileMenu(); ?>
            <?php echo fnTopLinks(); ?>
            <?php echo fnSideBar(); ?>
        </nav>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">User Permissions</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            User Permissions
                        </div>
                        <div class="panel-body">
                            <?php if($text!=""){ ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Success! <strong><?php echo $text; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                   <form name="adminForm" method="post" action="permissions.php?mode=update" enctype="multipart/form-data">   
                                        <div class="form-group col-md-12">
                                            <label>User</label>
                                            <select class="form-control" id="User" name="User" onchange="fnSubmit();" required>
                                                <?php fnDropDown("users","Name","UserID","User"); ?>
                                            </select>                                               
                                        </div>
                                        
                                        <?php if($_REQUEST['User']!="") { 
                                        $sql= "select * from users where UserID=".$_REQUEST["User"];
                                        $res=mysql_query($sql);

                                        echo "<ul id='sortable'>";  
                                        $obj =mysql_fetch_object($res);
                                        $permissions = json_decode($obj->Permissions,TRUE);                                      
                                        for($k=0;$k<count($permissions);$k++) {                                        
                                        ?>                                        
                                        <li class="form-group col-md-12" style="list-style:none">
                                            <label class="checkbox-inline">
                                                <input name="fields[]" <?php if($obj->UserType=="1") echo "disabled"; ?> <?php if($permissions[$k]["Status"]) echo "checked"; ?> value="<?php echo $permissions[$k]['PageID']; ?>" type="checkbox"><?php echo $permissions[$k]["PageName"]; ?>
                                            </label>
                                        <?php
                                              if(count($permissions[$k]["SubPage"])>0){
                                                  echo "<ul>"; 
                                                  $SubPage = $permissions[$k]["SubPage"]; 
                                                  for($z=0;$z<count($SubPage);$z++) {  
                                                  ?>
                                            <li class="form-group col-md-3" style="list-style:none">
                                                <label class="checkbox-inline">
                                                    <input name="fields[]" <?php if($SubPage[$z]["Status"]) echo "checked"; ?> <?php if($obj->UserType=="1") echo "disabled"; ?> value="<?php echo $SubPage[$z]['PageID']; ?>" type="checkbox"><?php echo $SubPage[$z]["PageName"]; ?>
                                                </label>
                                            </li>

                                                <?php
                                                  }
                                                  echo "</ul>";  
                                              }      
                                        ?>
                                        </li>
                                        <?php } ?>
                                         <?php if($obj->UserType!=="1"){ ?>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>
                                         <?php } ?>
                                        </ul>
                                        <?php } ?>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </body>
    <?php echo fnScript(); ?>
    <script>
        function fnSubmit(){
            document.adminForm.action="permissions.php";
            document.adminForm.submit();
        }
    </script>
</html>