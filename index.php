<?php
session_start();
$product_ids = array();
//check if add to cart button is clicked or not..
if (filter_input(INPUT_POST, 'add_to_cart')) {
        //check does a shopping_cart array exists or not
    if (isset($_SESSION['shopping_cart'])) {
        //keep finding the length of shopping_cart array
        $count = count($_SESSION['shopping_cart']);
        //create a sequential array which maching array keys to produce id's
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');
        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)) {
            $_SESSION['shopping_cart'][$count] = array(
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
            );
        }
        else {   //product already exists, increase the quantity
                //match array key to id of the productbeing added to the cart
            for ($i=0; $i < count($product_ids); $i++) {
                if ($product_ids[$i] == filter_input(INPUT_GET, 'id')) {
                    //add item quantity to existing product in array
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }
    }
    else {//if shopping_cart doesn't exists  then create th frist product with array key 0
        //create array using submitted form data start from key 0 and fill it with values..
        $_SESSION['shopping_cart'][0] = array(
            'id' => filter_input(INPUT_GET, 'id'),
            'name' => filter_input(INPUT_POST, 'name'),
            'price' => filter_input(INPUT_POST, 'price'),
            'quantity' => filter_input(INPUT_POST, 'quantity')
        );
    }
}
    // checking is there any products in shopping_cart or not
    if (is_array($_SESSION['shopping_cart'])) {
        //looping all items of shopping_cart
        foreach($_SESSION['shopping_cart'] as $key => $product) {
            //check delete btn is clicked or not
            if (isset($_POST['remove'])) {
                //if true uset the product which matches
                if ($product['id'] == filter_input(INPUT_GET, 'id')) {
                    unset($_SESSION['shopping_cart'][$key]);
                }
            }
        }
        $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
    }


//pre($_SESSION);

function pre($array)
{
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Example Shopping</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php
            $con = mysqli_connect("localhost", "root", "", "cart");
            $sql = "SELECT * FROM products ORDER BY id ASC";
            $result = mysqli_query($con, $sql);
            if ($result):
                if (mysqli_num_rows($result) > 0):
                    while($product = mysqli_fetch_assoc($result)):

                        ?>

                        <div class="col-sm-12 col-md-3">
                            <form action="index.php?action=add&id=<?php echo $product['id'];?>" method="post">
                                <div class="products">
                                    <img src="<?php echo "images/".$product['image'] ?>" alt="" class="image-responsive">
                                    <h4 class="text-info"><?php echo $product['name']; ?></h4>
                                    <h4>$ <?php echo $product['price']; ?></h4>
                                    <input type="text" name="quantity" value="1" style="width: 50px;" class="form-control">
                                    <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                                    <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                    <input type="submit" name="add_to_cart" style="margin-top:5px;" value="Add To Cart" class="btn btn-info">
                                </div>
                            </form>
                        </div>

                    <?php
                    endwhile;
                endif;

            endif;
           ?>
           <div style="clear:both;"></div>
           <br />
           <div class="table-responsive">
               <table class="table">
                   <tr>
                       <th colspan="5"><h3>ORDER DETAILS</h3></th>
                   </tr>
                   <tr>
                       <th width="40%">Product Name</th>
                       <th width="10%">Quantity</th>
                       <th width="20%">Price</th>
                       <th width="15%">Total</th>
                       <th width="5%">Action</th>
                   </tr>
                   <?php
                       if (!empty($_SESSION['shopping_cart'])):
                           $total = 0;
                           foreach ($_SESSION['shopping_cart'] as $key => $product):
                    ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['quantity']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
                        <td>
                            <form action="index.php?action=delete&id=<?php echo $product['id'];?>" method="post">
                                <input class="btn-danger" type="submit" name="remove" value="Remove">
                            </form>
                        </td>
                    </tr>
                    <?php
                    if(!empty($_SESSION['shopping_cart'])){
                    $total = $total + ($product['quantity'] * $product['price']);

                    }
        endforeach;
                 ?>
                 <tr>
                     <td colspan="3" align="right">Total</td>
                     <td align="right"><?php echo number_format($total, 2); ?></td>
                     <td></td>
                 </tr>


             <table>
                 <tr colspan="3">
                         <?php
                         if (isset($_SESSION['shopping_cart'])):
                         if (count($_SESSION['shopping_cart'])):
                         ?>
                         <a href="#" class="b utton">Checkout</a>
                     <?php endif; endif; ?>
                 </tr>
             </table>
         <?php endif; ?>
               </table>
           </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" ></script>
</body>
</html>
