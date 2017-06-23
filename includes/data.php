<?php
    include('connection.php');
    include('templates.php');

if($_REQUEST["mode"]=="sorting"){
    $orderlist = explode(',', $_POST['order']);
    $CategoryID= $_POST["category"];
    foreach ($orderlist as $k=>$order) {
        $sql="update fieldmapping set DisplayOrder='$k' where FieldMappingID=".$order;
        mysql_query($sql);
    }   
}

if($_REQUEST["mode"]=="permissions"){
    echo fnGetPermissions($_REQUEST["UserID"]);
}

if($_REQUEST["mode"]=="log"){
    echo fnGetLogs($_REQUEST["LogID"]);
}
?>