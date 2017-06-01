<?php
function fnMetaHeaders(){

    $html ='
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TC Management Software">
    <meta name="author" content="Rapid Thoughts">';

    return $html;
}

function fnDataTableCSS(){
    $html ='<link href="../../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">';
    return $html;
}

function fnCss(){
    if(!LoggedInUser())
    {
        header("location:../../login.php");
    }
    $html ='
    <!-- Bootstrap Core CSS -->
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn`t work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->';

    return $html;
}

function fnScript(){
    $html =' 
    <script src="../../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../../dist/js/sb-admin-2.js"></script>
    <script>
        $("#checkAll").click(function(){
            $(".check").not(this).prop("checked", this.checked);
        });
        
        function fnDelete(arg)
        {
	        if(confirm("Are you sure want to delete?"))
	        {
		        document.location.href="'.basename($_SERVER['PHP_SELF']).'?mode=del&&chkSelect[]=" + arg + "";
	        }
        }

        function fnBulk(arg) {
            if($(".check:checked").length >0){
                if(arg=="del") {
                    if(confirm("Are you sure want to delete?"))
                    {
                        document.adminForm.action="'.basename($_SERVER['PHP_SELF']).'?mode=del";
                        document.adminForm.submit();
                    }        
                }  
                if(arg=="TCDone") {
                    if(confirm("Are you sure want to update TC Status?"))
                    {
                        document.adminForm.action="'.basename($_SERVER['PHP_SELF']).'?mode=TCGiven";
                        document.adminForm.submit();
                    }        
                }                 
            } else {
                alert("Please select atleast one item");
            } 
        }
    </script>';    
    return $html;
}

function fnDataTableScript(){
    $html =' 
    <script src="../../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../../vendor/datatables-responsive/dataTables.responsive.js"></script>
        <script>
    $(document).ready(function() {
        $("#dataTables-example").DataTable({
            responsive: true,
            order: [],
            columnDefs: [ { orderable: false, targets: [0] } ]
        });
    });
    </script>
    ';

    return $html;
}


function fnGraphCSS(){
    $html ='<link href="../../vendor/morrisjs/morris.css" rel="stylesheet">';
    return $html;
}

function fnGraphScript(){
    $html ='  <script src="../../vendor/raphael/raphael.min.js"></script>
    <script src="../../vendor/morrisjs/morris.min.js"></script>';
    return $html;
}

function fnDatePickerCSS(){
    $html ='<link href="../../vendor/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">';
    return $html;
}

function fnDatePickerScript(){
    $html ='  <script src="../../vendor/moment/moment.min.js"></script>
    <script src="../../vendor/datepicker/bootstrap-datetimepicker.min.js"></script>';
    return $html;
}

function fnDropDown($TableName,$ColumnName,$ColumnID,$VariableName)
{
    echo '<option value="" >Select</option>';
    $sql = "select * from ".strtolower($TableName)." where Deleted=0";        
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        while($obj=mysql_fetch_object($res))
        {
            if($_REQUEST[$VariableName]==$obj->$ColumnID)
            {
                echo '<option value="'.$obj->$ColumnID.'" selected="selected">'.$obj->$ColumnName.'</option>';
            }
            else
            {
                echo '<option value="'.$obj->$ColumnID.'">'.$obj->$ColumnName.'</option>';
            }
        }
    }
}

function fnGetFilter($FieldName,$FieldKey,$CategoryID)
{
    $html= '<option value="" >Select</option>';
    $sql = "select * from products where CategoryID=".$CategoryID;
    $sql.= " and Deleted=0 order by ProductID";     
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        $filter= array();
        while($obj=mysql_fetch_object($res))
        {
             $data = json_decode($obj->Fields, TRUE);
             if($data[$FieldName]!="" && !in_array($data[$FieldName],$filter)) {
                array_push($filter,$data[$FieldName]);
                if($_REQUEST[$FieldKey]==$data[$FieldName])
                {
                    $html.= '<option value="'.$data[$FieldName].'" selected="selected">'.$data[$FieldName].'</option>';
                }
                else
                {
                    $html.= '<option value="'.$data[$FieldName].'">'.$data[$FieldName].'</option>';
                }
             }
        }
    }

    return $html;
}

function fnFieldDropDown($FieldMappingID,$VariableName)
{
    $html= '<option value="" >Select</option>';
    $sql = "select * from productfieldvalue where Deleted=0 and FieldMappingID=".$FieldMappingID;        
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        while($obj=mysql_fetch_object($res))
        {
            if($_REQUEST[$VariableName]==$obj->ProductFieldValue)
            {
                $html.= '<option value="'.$obj->ProductFieldValue.'" selected="selected">'.$obj->ProductFieldValue.'</option>';
            }
            else
            {
                $html.= '<option value="'.$obj->ProductFieldValue.'">'.$obj->ProductFieldValue.'</option>';
            }
        }
    }

    return $html;
}

function GetData($TableName,$ColumnID,$QueryID,$ReturnName)
{
    $sql = "select * from ".strtolower($TableName)." where ".$ColumnID."=".$QueryID;
    $res=mysql_query($sql);
    $obj=mysql_fetch_object($res);
    return $obj->$ReturnName;
}

function LoggedInUser()
{
    if($_SESSION["UserID"]=="")
    {
        return false;
    }
    else
    {
        return true;
    }
}

function isSuperAdmin(){
    if($_SESSION["UserType"]=="1")
    {
        return true;
    }
    else
    {
        return false;
    }   
}

function slugify($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
   $string = str_replace('-', '', $string);

   return $string;
}

function add_quotes($str) {
    return sprintf("'%s'", $str);
}

function post_img($fileName,$tempFile,$targetFolder)
{	
    if ($fileName!="")
	{
		if(!(is_dir($targetFolder)))
			mkdir($targetFolder);
		$counter=0;
		$NewFileName=$fileName;
		$NewFileName=str_replace(",","-",$NewFileName);
		$NewFileName=str_replace(" ","_",$NewFileName);	
		if(file_exists($targetFolder."/".$NewFileName))
		{
			do
			{ 
				$counter=$counter+1;
				$NewFileName=$counter."".$NewFileName;
			}
			while(file_exists($targetFolder."/".$NewFileName));
		}
		copy($tempFile, $targetFolder."/".$NewFileName);	
		return $NewFileName;
	}
}

function DateConverter($var){
    $date = str_replace('/', '-', $var);
    echo date('m-d-Y', strtotime($date));
}
?>