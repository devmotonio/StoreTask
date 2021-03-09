<?php
include "conn.php";
$baseUrl = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"];

$minDate = mktime (0,0,0,2,1,2021);
$maxDate = time();

function getRandomSku()
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$numbers = '0123456789';
		$randChar = '';
		$randNumber = '';
		for ($i = 0; $i < 4; $i++) {
			$randChar .= $characters[rand(0, (strlen($characters)-1))];
		}
		$randNumber = $numbers[rand(0, (strlen($numbers)-1))];
		return $randChar.'-'.$randNumber;
	}

function createProduct()
	{
		global $pdo,$minDate,$maxDate;
		$sql = 'INSERT INTO `products` VALUES (NULL, :stock,:sku,:arrive_stock,:arrive_date)';
		$sth = $pdo->prepare($sql);
		return $sth->execute(array(':stock' => rand(50,500),':sku' => getRandomSku(),':arrive_stock' => rand(10,300),':arrive_date' => date("Y-m-d",rand($minDate,$maxDate))));
	}
	
function createOrder()
	{
		global $pdo,$minDate,$maxDate;
		$sql = 'INSERT INTO `orders` VALUES (NULL,:date)';
		$sth = $pdo->prepare($sql);
		return $sth->execute(array(':date' => date("Y-m-d H:i:s",rand($minDate,$maxDate))));

	}
	
function addProductOrder($orderId,$product_id)
	{	
		global $pdo;
		
		$sql = 'INSERT INTO `orderitems` VALUES (NULL,:order_id,:product_id,:qty)';
		$sth = $pdo->prepare($sql);
		$sth->execute(array(':order_id' => $orderId,':product_id' => $product_id,':qty' => rand(1,30)));
	}

function updateStock($productId)	
	{
		global $pdo;
		$sql = 'UPDATE `products` SET stock = :stock WHERE id = :id';
		$sth = $pdo->prepare($sql);
		$sth->execute(array(':stock' => rand(50,500),':id' => $productId));
	}

function updateStockAll()
	{
		global $pdo;
		$sql = 'SELECT id FROM `products`';
		$query = $pdo->query($sql);

		foreach($query as $row)
		{
			updateStock($row["id"]);
		}
	}

function generateRandomOrders($amount)
	{
		global $pdo;
		$count = 0;
		
		$sql = 'SELECT COUNT(`id`) as `count` FROM `products`';
		$query = $pdo->query($sql);
		$amount = $query->fetch()["count"] < $amount ? $query->fetch()["count"] : $amount;

		while($count < $amount)
		{
			if(createOrder())
			{
				$orderId = $pdo->lastInsertId();
				$sql = 'SELECT id FROM `products` ORDER BY RAND() LIMIT 10';
				$query = $pdo->query($sql);

				foreach($query as $row)
				{
					addProductOrder($orderId,$row["id"]);
				}
				$count++;
			}
		}
	}

function generateRandomProducts($amount)
	{
		global $pdo;
		$count = 0;
		
		while($count < $amount)
		{
			$count += createProduct() ? 1 : 0;
		}
	}
	
function resetDataBase()
	{
		global $pdo;
		
		$sql = 'DELETE FROM `orderitems`';
		$sth = $pdo->prepare($sql);
		
		if($sth->execute())
		{
			$sql = 'DELETE FROM `orders`';
			$sth = $pdo->prepare($sql);
			$sth->execute();
			
			$sql = 'DELETE FROM `products`';
			$sth = $pdo->prepare($sql);
			$sth->execute();
		}
	}

if (isset($_GET["action"]) && $_GET["action"] == "generateRandomProducts")
{
	generateRandomProducts(10);
	header('Location: '.$baseUrl);
	exit;
}

if (isset($_GET["action"]) && $_GET["action"] == "generateRandomOrders")
{
	generateRandomOrders(10);
	header('Location: '.$baseUrl);
	exit;
}

if (isset($_GET["action"]) && $_GET["action"] == "generateRandomStock")
{
	updateStockAll();
	header('Location: '.$baseUrl);
	exit;
}

if (isset($_GET["action"]) && $_GET["action"] == "reset")
{
	resetDataBase();
	header('Location: '.$baseUrl);
	exit;
}

if (isset($_POST["selectProducts"]))
{
	updateStock($_POST["selectProducts"]);
	header('Location: '.$baseUrl);
	exit;
}
?>