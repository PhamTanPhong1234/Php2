<?php
trait CartModel
{
	public function cartAdd($id)
	{
		if (isset($_SESSION['cart'][$id])) {
			//nếu đã có sp trong giỏ hàng thì số lượng lên 1
			$_SESSION['cart'][$id]['number']++;
		} else {
			//lấy thông tin sản phẩm từ CSDL và lưu vào giỏ hàng
			//$product = db::get_one("select * from products where id=$id");
			//---
			//PDO
			$conn = Connection::getInstance();
			$query = $conn->prepare("select * from products where id=:id");
			$query->execute(array("id" => $id));
			$query->setFetchMode(PDO::FETCH_OBJ);
			$product = $query->fetch();
			//---

			$_SESSION['cart'][$id] = array(
				'id' => $id,
				'name' => $product->name,
				'photo' => $product->photo,
				'number' => 1,
				'price' => $product->price
			);
		}
	}
	/**
	 * Cập nhật số lượng sản phẩm
	 * @param int
	 * @param int
	 */
	public function cartUpdate($id, $number)
	{
		if ($number == 0) {
			//xóa sp ra khỏi giỏ hàng
			unset($_SESSION['cart'][$id]);
		} else {
			$_SESSION['cart'][$id]['number'] = $number;
		}
	}
	/**
	 * Xóa sản phẩm ra khỏi giỏ hàng
	 * @param int
	 */
	public function cartDelete($id)
	{
		unset($_SESSION['cart'][$id]);
	}
	/**
	 * Tổng giá trị giỏ hàng
	 */
	public function cartTotal()
	{
		$total = 0;
		foreach ($_SESSION['cart'] as $product) {
			$total += $product['price'] * $product['number'];
		}
		return $total;
	}
	/**
	 * Số sản phẩm có trong giỏ hàng
	 */
	public function cartNumber()
	{
		$number = 0;
		foreach ($_SESSION['cart'] as $product) {
			$number += $product['number'];
		}
		return $number;
	}
	/**
	 * Danh sách sản phẩm trong giỏ hàng
	 */
	public function cartList()
	{
		return $_SESSION['cart'];
	}
	/**
	 * Xóa giỏ hàng
	 */
	public function cartDestroy()
	{
		$_SESSION['cart'] = array();
	}
	//=============
	//checkout
	public function cartCheckOut()
	{
		//---
		$conn = Connection::getInstance();
		//lay id vua moi insert
		$customer_id = $_SESSION["customer_id"];
		//echo $_SESSION["customer_id"];
		//---
		//---
		//insert ban ghi vao orders, lay order_id vua moi insert
		$query = $conn->prepare("insert into orders set customer_id=:customer_id, create_at=now()");
		$query->execute(array("customer_id" => $customer_id));
		//lay id vua moi insert
		$order_id = $conn->lastInsertId();
		//---
		//duyet cac ban ghi trong session array de insert vao orderdetails
		foreach ($_SESSION["cart"] as $product) {
			$id = $product["id"];
			$query = $conn->prepare("insert into orderdetails set order_id=:order_id, product_id=:product_id, price=:price, number=:number");
			$query->execute(array("order_id" => $order_id, "product_id" => $product["id"], "price" => $product["price"], "number" => $product["number"]));

			$query1 = $conn->query("select * from products where id = $id");
			//tra ve mot ban ghi
			$query1 = $query1->fetch();
			if (intval($product["number"])) {
				$count = intval($query1->quantity) - intval($product["number"]);
				$query2 = $conn->query("update products set quantity = $count where id = $id");
			}
		}
		//xoa gio hang
		unset($_SESSION["cart"]);
		return $order_id;
	}

	public function modelListOrderDetails()
	{
		$id = isset($_GET["id"]) ? $_GET["id"] : 0;
		$conn = Connection::getInstance();
		$query = $conn->query("select * from orderdetails where order_id = $id");
		//tra ve mot ban ghi
		return $query->fetchAll();
	}


	public function modelGetProducts($id)
	{
		//---
		$conn = Connection::getInstance();
		$query = $conn->query("select * from products where id = $id");
		//tra ve mot ban ghi
		return $query->fetch();
		//---
	}
	//=============
}
?>