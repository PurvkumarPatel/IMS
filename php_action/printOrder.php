<?php    

require_once 'core.php';

$orderId = $_POST['orderId'];

$sql = "SELECT order_date, client_name, client_contact, sub_total, vat, total_amount, discount, grand_total, paid, due, payment_place,gstn FROM orders WHERE order_id = $orderId";

$orderResult = $connect->query($sql);
$orderData = $orderResult->fetch_array();

$orderDate = $orderData[0];
$clientName = $orderData[1];
$clientContact = $orderData[2]; 
$subTotal = $orderData[3];
$vat = $orderData[4];
$totalAmount = $orderData[5]; 
$discount = $orderData[6];
$grandTotal = $orderData[7];
$paid = $orderData[8];
$due = $orderData[9];
$payment_place = $orderData[10];
$gstn = $orderData[11] * (5/18);


$orderItemSql = "SELECT order_item.product_id, order_item.rate, order_item.quantity, order_item.total,
product.product_name FROM order_item
   INNER JOIN product ON order_item.product_id = product.product_id 
 WHERE order_item.order_id = $orderId";
$orderItemResult = $connect->query($orderItemSql);

$table = '<style>
    body {
        font-family: Arial, sans-serif;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }
    .invoice-table th, .invoice-table td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }
    .invoice-table th {
        background-color: #f2f2f2;
    }
    .invoice-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .invoice-header h1 {
        color: #333;
        margin: 5px 0;
    }
</style>
<div class="invoice-header">
    <h3>TAX INVOICE</h3>
    <h1>Harihar Textile</h1>
    <p>GROUND FLOOR FIRST FLOOR.PLOT NO.A1/39,BHAGWATI IND. ESTATE, │
    │ BHESTAN,SURAT. Gujarat 24</p>
    <p>Contact: 1234567890 | Email: company@iiitsurat.ac.in</p>
</div>
<table class="invoice-table">
    <thead>
        <tr>
            <th colspan="2">Bill To:</th>
            <th colspan="3">Invoice Details</th>
        </tr>
        <tr>
            <th>Name</th>
            <th>Contact</th>
            <th>Invoice Number</th>
            <th>Invoice Date</th>
            <th>GSTIN</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>'.$clientName.'</td>
            <td>'.$clientContact.'</td>
            <td>INV-'.$orderId.'</td>
            <td>'.$orderDate.'</td>
            <td>'.$gstn.'</td>
        </tr>
    </tbody>
</table>
<table class="invoice-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Rate (Rs.)</th>
            <th>Total (Rs.)</th>
        </tr>
    </thead>
    <tbody>';
$x = 1;
$cgst = 0;
$igst = 0;
if ($payment_place == 2) {
    $igst = $subTotal * 5 / 100;
    $total = $subTotal + $igst;
} else {
    $cgst = $subTotal * 2.5 / 100;
    $total = $subTotal + 2 * $cgst;
}
while ($row = $orderItemResult->fetch_array()) {       
    $table .= '<tr>
            <td>'.$x.'</td>
            <td>'.$row[4].'</td>
            <td>'.$row[2].'</td>
            <td>'.$row[1].'</td>
            <td>'.$row[3].'</td>
        </tr>';
    $x++;
}
$table .= '</tbody>
</table>
<table class="invoice-table">
    <tfoot>
        <tr>
            <td colspan="4">Subtotal</td>
            <td>'.$subTotal.'</td>
        </tr>
        <tr>
        <td colspan="4">SGST (2.5%)</td>
        <td>'.$cgst.'</td>
        </tr>
        <tr>
            <td colspan="4">CGST (2.5%)</td>
            <td>'.$cgst.'</td>
        </tr>
        <tr>
            <td colspan="4">IGST (5%)</td>
            <td>'.$igst.'</td>
        </tr>
        <tr>
            <td colspan="4">Total</td>
            <td>'.$total.'</td>
        </tr>
    </tfoot>
</table>';

$connect->close();

echo $table;
?>
