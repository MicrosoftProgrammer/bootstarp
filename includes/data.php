<?php
    include('connection.php');

if($_REQUEST["mode"]=="sorting"){
    $orderlist = explode(',', $_POST['order']);
    $CategoryID= $_POST["category"];
    foreach ($orderlist as $k=>$order) {
        $sql="update fieldmapping set DisplayOrder='$k' where FieldMappingID=".$order;
        mysql_query($sql);
    }   
}
?>