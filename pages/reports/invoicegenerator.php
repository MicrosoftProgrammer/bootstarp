<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if($_REQUEST["TransactionID"]==""){
        header("location:invoice.php");
    }

    $sql ="select * from producttransactions where TransactionID=".$_REQUEST["TransactionID"];
    $res = mysql_query($sql);
    $obj= mysql_fetch_object($res);

    

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
    </head>
    <body style="background-color:white">
        <div class="container" style="width:700px;">
            <table class="table">
                <tr>
                    <td colspan="3">
                        <img style="height:100px;"  src='../../images/<?php echo $_SESSION["Logo"] ?>' alt="Logo" class="img-responsive" />
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <strong class="text-center">Invoice</strong>
                    </td>
                </tr>  
                  <tr>
                    <td align="center">
                    </td>
                </tr>           
                <tr>
                    <td style="border: 1px solid #ddd;"  colspan="2">
                        <?php echo $_SESSION["Address"]; ?><br/>
                        <b>TEL :</b><?php echo $_SESSION["ContactNo"]; ?><br/>
                        <b>FAX :</b> <?php echo $_SESSION["Fax"]; ?><br/>
                        <br/>
                        <b>ATTN :</b> <?php echo $_SESSION["Name"]; ?>
                    </td>
                    <td style="border: 1px solid #ddd;" >
                        <b>Invoice No :</b><?php echo $obj->InvoiceNo; ?><br/>
                        <b>Date       :</b> <?php echo ConvertToCustomDate($obj->PurchaseDate); ?><br/>
                        <b>Job Ref    :</b> <?php echo $obj->JobRef; ?><br/>
                        <b>LPO Ref    :</b> <?php echo $obj->LPORef; ?><br/>
                        <b>Quota Ref    :</b> <?php echo $obj->QuotaRef;  ?>
                    </td>
                </tr>
                  <tr>
                    <td align="center">
                    </td>
                </tr> 
                <tr>
                    <th style="border: 1px solid #ddd;" width="20%">S.No</th>
                    <th style="border: 1px solid #ddd;" width="50%">Charge Details</th>
                    <th style="border: 1px solid #ddd;" width="30%">Amount (<?php echo $_SESSION["CurrencyType"]; ?>)</th>
                </tr>
                <tr>
                    <td style="border: 1px solid #ddd;">1</td>
                    <td style="border: 1px solid #ddd;"><?php echo $obj->ChargeDetails; ?>
                    <br>
                     <br>
                      <br>
                                       </td>
                    <td style="border: 1px solid #ddd;"><?php echo ConvertToRupees($obj->PurchaseValue); ?></td>
                </tr>
                
                <tr>
                    <td colspan="2" style="border: 1px solid #ddd;">
                        Total : <strong><?php echo convert_number_to_words($obj->PurchaseValue); ?></strong> Only
                    </td>
                     <td style="border: 1px solid #ddd;"><strong><?php echo ConvertToRupees($obj->PurchaseValue); ?></strong></td>
                </tr> 
                <tr>
                    <td style="border: 1px solid #ddd;" colspan="2"><b><?php echo $_SESSION["CompanyName"]; ?></b>
                    <br>
                    <br>
                                        <br>
                                        
                    <br>
                    Authorised Signatory
                    </td>
                    <td style="border: 1px solid #ddd;">
                            <b>Account No :</b><?php echo $_SESSION["AccountNo"]; ?><br/>
                            <b>Bank       :</b> <?php echo $_SESSION["Bank"]; ?><br/>
                            <b>Address   :</b> <?php echo $_SESSION["BankAddress"]; ?><br/>
                            <b>Swift Code    :</b> <?php echo $_SESSION["SwiftCode"]; ?><br/>
                            <b>IBAN    :</b> <?php echo $_SESSION["IBAN"];  ?>
                    </td>
                </tr>                                
            </table>                    
        </div>
    </body>
    <script>
        window.print();
    </script>
</html>