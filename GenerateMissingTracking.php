 <?php
 error_reporting(E_ALL | E_STRICT);
 define('DEBUG', false);
 include($_SERVER['DOCUMENT_ROOT'].'./config.php');
 ini_set('display_errors', 1);

 $conn = dbConnection();




// call export function
exportMysqlToCsv('TrackingFaltantes.csv');
 //echo "hello";

// export csv
function exportMysqlToCsv($filename )
{

 $conn = dbConnection();

// Check connection
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_query = "SELECT date_add, ps_ec_reliquat.id_reliquat,m_forwards.ref as consolidated, ps_order_detail.id_order, product_mpn, product_supplier_reference,product_name, quantity,CONCAT('https://www.rgdist.net/admin697r94ej9/index.php/sell/orders/',ps_ec_reliquat.id_order,'/view') as order_link, CONCAT('https://www.rgdist.net/modules/ec_reliquat/generateDeliverySlip.php?token=76b8e3a56b1e4407d726a6e36145cc97&id_order=',ps_ec_reliquat.id_order,'&id_reliquat=',ps_ec_reliquat.id_reliquat) as label_link from ps_ec_reliquat LEFT join phs.add_courier on CONVERT(ps_ec_reliquat.id_reliquat,CHAR(50)) = add_courier.tracking and act_status = 1 left join phs.m_courrier_forwards on m_courrier_forwards.courrier_id = add_courier.id left join phs.m_forwards on m_forwards.id = forward_id   LEFT JOIN  ps_ec_reliquat_product on  ps_ec_reliquat.id_reliquat = ps_ec_reliquat_product.id_reliquat LEFT JOIN ps_order_detail on ps_ec_reliquat_product.id_order_detail = ps_order_detail.id_order_detail  where tracking_number = ''";

    // Gets the data from the database
$result = $conn->query($sql_query) or die($conn->error);
   // var_dump($result);
$f = fopen('php://temp', 'wt');
$first = true;
while ($row = $result->fetch_assoc()) {
    if ($first) {
        fputcsv($f, array_keys($row));
        $first = false;
    }
    fputcsv($f, $row);
    } // end while

    $conn->close();

    $size = ftell($f);
    rewind($f);

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: $size");
    // Output to browser with appropriate mime type, you choose ;)
    header("Content-type: text/x-csv");
    header("Content-type: text/csv");
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    fpassthru($f);
    exit;

}
function dbConnection(){
    $servername = "localhost";
    $username = "mramirez";
    $password = "Medellin033097..";
    $dbname = "portalv2";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    return $conn;
}


?>
