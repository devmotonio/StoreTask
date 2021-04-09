<?php
include "functions.php";

$sql = 'SELECT * FROM `products` ORDER BY sku';
$queryProducts = $pdo->query($sql);
?>
<!doctype html>
<html lang="en">
  <head>
	<title>Products</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Products Page">
	<meta name="author" content="Everton Lima">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="bootstrap.min.css">

  </head>
  <body>
<div class="container-fluid">
	<div class="row">
		<div class="col-9">
		  <table class="table table-bordered">
		  <tr><th>SKU</th><th>Stock</th><th>Stock to arrive</th><th>Arrive date</th><th>Products Sold</th><th>Average in all orders</th><th>Average in orders with the product</th></tr>
			<?php
				$sql = 'SELECT `products`.*,`t_product_sum`.`product_sum` as `products_sold`,((`product_sum`/`order_sum`)*100) as `avg_in_all_orders`,((`product_sum`/`order_product_sum`)*100) as `avg_in_orders_by_product`
		FROM `products`
		INNER JOIN (SELECT SUM(`qty`) as `order_sum` FROM `orderitems`) as `t_aux`
		LEFT JOIN (SELECT `product_id`,SUM(`qty`) as `product_sum` FROM `orderitems` GROUP BY `product_id`) as `t_product_sum` on `products`.`id` = `t_product_sum`.`product_id`
		LEFT JOIN (SELECT product_id,SUM(`ti_group`.`order_qty`) as `order_product_sum` FROM `orderitems` 
		INNER JOIN (SELECT `order_id` as `in_order_id`,sum(qty) as `order_qty` FROM `orderitems` GROUP BY `order_id`) as `ti_group` on `order_id` = `in_order_id`
		GROUP BY `product_id`) as `t_count_by_order` on `products`.`id` = `t_count_by_order`.`product_id`
		GROUP BY `products`.`id`
		ORDER BY `products_sold` DESC';
				$query = $pdo->query($sql);
				
				foreach($query as $row):
					?>
					<tr><td><?=$row["sku"]?></td><td><?=$row["stock"]?></td><td><?=$row["arrive_stock"]?></td><td><?=$row["arrive_date"]?></td><td><?=$row["products_sold"]?></td><td><?=$row["avg_in_all_orders"]?></td><td><?=$row["avg_in_orders_by_product"]?></td></tr>
				<?php endforeach; ?>
		  </table>
		</div>
		<div class="col-3">
			<form action="/" method="POST">
			<div class="card">
				<div class="card-header">Generate Data</div>
				<div class="card-body">
					<p><a class="btn btn-primary" href="/?action=generateRandomProducts" role="button">Generate products</a></p>
					<p><a class="btn btn-primary" href="/?action=generateRandomOrders" role="button">Generate orders</a></p>
					<p><a class="btn btn-primary" href="/?action=generateRandomStock" role="button">Generate stock to all products</a></p>
				
					<h5 class="card-title">Generate stock to a product</h5>
			
					<div class="form-group">
					<select class="form-control" id="selectProducts" name="selectProducts">
						<option value=0>Select product</option>
						<?php
						foreach ($queryProducts as $product):
						?>
							<option value=<?=$product["id"]?>><?=$product["sku"]?></option>
						<?php endforeach; ?>
					</select>
					</div>
					<button type="submit" class="btn btn-primary mb-3">Generate stock</button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="jquery-3.3.1.slim.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
  </body>
</html>