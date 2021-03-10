 <?php
 error_reporting(E_ALL | E_STRICT);
 define('DEBUG', false);
 include($_SERVER['DOCUMENT_ROOT'].'/../config.php');
 ini_set('display_errors', 1);

 $conn = dbConnection();




// call export function
exportMysqlToCsv('CuentasPorCobrar.csv');
 //echo "hello";

// export csv
function exportMysqlToCsv($filename )
{

 $conn = dbConnection();

// Check connection
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql_query = "SELECT * from ps_ec_reliquat where tracking_number = ''";

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
