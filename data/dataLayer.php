<?php

    # Establishing the connection to the Database
    function connect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Disenazo";

        $connection = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($connection->connect_error)
        {
            return null;
        }
        else
        {
            return $connection;
        }
    }

    # Callback error messages
	function errors($type)
	{
		$header = "HTTP/1.1 ";

		switch($type)
		{
			case 306:	$header .= "306 Wrong Credentials";
						break;
			case 400:	$header .= "400 User Not Found";
						break;
			case 404:	$header .= "404 Request Not Found";
						break;
			case 409:	$header .= "409 Your action was not completed correctly, please try again later";
						break;
			case 412:	$header .= "412 Username already in use";
						break;
			case 417:	$header .= "417 No content set in the cookie/session";
						break;
			case 500:	$header .= "500 Bad connection to Database";
						break;
			default:	$header .= "404 Request Not Found";
		}

		header($header);
		return array('status' => 'ERROR', 'code' => $type);
	}

    # Query to retrieve a user data
    function validateUserCredentials($userName)
    {
        # Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM User WHERE userName = '$userName'";
			$result = $conn->query($sql);

			# The current user exists
			if ($result->num_rows > 0)
			{
				while($row = $result->fetch_assoc())
		    	{
					$conn->close();
					return array("status" => "COMPLETE", "fName" => $row['fName'], "lName" => $row['lName'], "password" => $row['passwrd']);
				}
			}
			else
			{
				# The user doesn't exists in the Database
				$conn->close();
				return errors(400);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to find out if the user already exist in the Database
    function verifyUser($userName)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT * FROM User WHERE userName = '$userName'";
			$result = $conn->query($sql);

			if ($result->num_rows > 0)
			{
				# The current user already exists
				$conn->close();
				return errors(412);
			}
			else
			{
				$conn->close();
				return array("status" => "COMPLETE");
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to insert a new user to the Database
    function registerNewUser($userFirstName, $userLastName, $userName, $email, $userPassword)
    {
    	# Open and validate the Database connection
    	$conn = connect();

        if ($conn != null)
        {
        	$sql = "INSERT INTO User(fName, lName, userName, email, passwrd) VALUES ('$userFirstName', '$userLastName', '$userName', '$email', '$userPassword')";
			if (mysqli_query($conn, $sql))
	    	{
	    		$conn->close();
			    return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
        }
        else
        {
        	# Connection to Database was not successful
        	$conn->close();
        	return errors(500);
        }
    }

    # Query to get all designs ordered by date
    # It doesn't need anything from the application layer
    function getNewestDesignsDB()
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM Design ORDER BY uploadDate DESC";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                $data = array();
                while($row = $result->fetch_assoc())
                {
                    array_push($data,array('designId' => $row['designId'], 'designName' => $row['designName']));
                }

                $response = array('message' => 'OK', 'data' => $data);
                $conn->close();
                return $response;
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    # Query to get the 5 most viewed designs in the week
    # It doesn't need anything from the application layer
    function mostPopularInTheWeekDB()
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM Design WHERE uploadDate > DATE_SUB(curdate(), INTERVAL 1 WEEK) ORDER BY views DESC LIMIT 4";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                $data = array();
                while($row = $result->fetch_assoc())
                {
                    array_push($data,array('designId' => $row['designId'], 'designName' => $row['designName']));
                }

                $response = array('message' => 'OK', 'data' => $data);
                $conn->close();
                return $response;
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    # Query to get all the info about a design
    # Needs the designId from the Application layer
    function getCompleteDesignDB($designId)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM Design WHERE designId = '$designId'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $conn->close();
                    $data[]=array_map('utf8_encode', $row);
                    return $response = array('message' => 'OK', 'data' => $data);
                }
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    # Query to get all the info about the user
    # Needs the userName from the Application layer
    function getUserInfoDB($userName)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM User WHERE userName = '$userName'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $conn->close();
                    $data[]=array_map('utf8_encode', $row);
                    return $response = array('message' => 'OK', 'data' => $data);
                }
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    # Query to get all the info about the user
    # Needs the userName from the Application layer
    function getUserDesignsDB($userName)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM Design WHERE userName = '$userName'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                $data = array();
                while($row = $result->fetch_assoc())
                {
                    array_push($data,array('designId' => $row['designId'], 'designName' => $row['designName']));
                }

                $response = array('message' => 'OK', 'data' => $data);
                $conn->close();
                return $response;
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }


    # Query to get all the info about a product
    # Needs the designId from the Application layer
    function getCompleteProductDB($productName)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT * FROM Product WHERE productName = '$productName'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $conn->close();
                    $data[]=array_map('utf8_encode', $row);
                    return $response = array('message' => 'OK', 'data' => $data);
                }
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    #Query to get the price of a product
    #Need the product name
    function getProductPriceDB($productName)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT price FROM Product WHERE productName = '$productName'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    return $row['price'];
                }
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    #Query to get the price of a design
    #Need the design id
    function getDesignPriceDB($designId)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT pricePercent FROM Design WHERE designId = '$designId'";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    return $row['pricePercent'];
                }
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

    #Query to get the items in the user cart
	function getCartDB($username)
	{
		$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT Cart.*, Design.designName FROM Cart INNER JOIN Design ON Cart.design=Design.designId WHERE Cart.userName='$username' AND status='P' ORDER BY Cart.orderId ASC";

        	$result = $conn->query($sql);

        	if ($result->num_rows > 0)
			{
				$data = array();
				while($row = $result->fetch_assoc())
		    	{
		    		array_push($data, $row);
		    	}

		    	$response = array('message' => 'OK', 'data' => $data);
		    	$conn->close();
		    	return $response;
			}
			else
			{
				return array('message' => 'NONE');
			}
        }
        else
        {
        	$conn->close();
        	return errors(500);
        }
	}

    #Query to get the user order history
	function getOrderHistoryDB($username)
	{
		$conn = connect();

        if ($conn != null)
        {
        	$sql = "SELECT Cart.*, Design.designName FROM Cart INNER JOIN Design ON Cart.design=Design.designId WHERE Cart.userName='$username' AND status='B' ORDER BY Cart.orderId DESC";

        	$result = $conn->query($sql);

        	if ($result->num_rows > 0)
			{
				$data = array();
				while($row = $result->fetch_assoc())
		    	{
		    		array_push($data, $row);
		    	}

		    	$response = array('message' => 'OK', 'data' => $data);
		    	$conn->close();
		    	return $response;
			}
			else
			{
				return array('message' => 'NONE');
			}
        }
        else
        {
        	$conn->close();
        	return errors(500);
        }
	}

    #Query to add Items to the Cart
    # Needs all the columns of the cart from the front-end that includes:
    # username, designId, productId, color, size, unitPrice (productPrice+designPrice),
    # quantity and the total price of the order
	function addItemsToCartDB($userName, $design, $product, $color, $size, $unitPrice, $quantity, $totalPrice)
	{
		$conn = connect();
		if($conn != null)
		{
			$sql = "INSERT INTO Cart(userName,design,product,color,size,unitPrice,quantity,totalPrice,status) VALUES ('$userName','$design','$product','$color','$size','$unitPrice','$quantity','$totalPrice','P')";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
		}
		else
		{
			$conn->close();
			return errors(500);
		}
	}

    #Query to modify user data
    function setUserInfoDB($userName, $fName, $lName, $email, $city, $address, $aboutme)
	{
		$conn = connect();
		if($conn != null)
		{
            echo $userName;
            echo $fName;
            echo $lName;
            echo $email;
            echo $city;
            echo $address;
            echo $aboutme;            


			$sql = "UPDATE User SET fName='$fName', lName='$lName', email='$email', city='$city', address='$address', aboutMe='$aboutme' WHERE userName='$userName'";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
		}
		else
		{
			$conn->close();
			return errors(500);
		}
	}

    # Query to delete a single order from the Cart in the Database
    # Needs the orderId from the Application layer
    function deleteItemFromCartDB($orderId)
	{
		$conn = connect();
		if($conn != null)
		{
			$sql = "DELETE FROM Cart Where orderId = $orderId";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
		}
		else
		{
			$conn->close();
			return errors(500);
		}
	}

    # Query to buy a single order from the Cart in the Database
    # Needs the orderId from the Application layer
    function buyCartDB($orderId)
	{
		$conn = connect();
		if($conn != null)
		{
			$sql = "UPDATE Cart SET status = 'B' WHERE orderId = '$orderId'";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE");
			}
			else
			{
				$conn->close();
				return errors(409);
			}
		}
		else
		{
			$conn->close();
			return errors(500);
		}
	}

    # Query to upload the info of an image to the Database
    # Needs the NOT NULL columns of the Design Table which includes:
    # designId, userName, designName, description, price
    function uploadDesignDB($designId, $userName, $designName, $description, $price)
	{
		$conn = connect();
		if($conn != null)
		{
			$sql = "INSERT INTO Design(designId, userName, designName, description, pricePercent) VALUES ('$designId', '$userName', '$designName', '$description', '$price')";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE", "designId" => $designId);
			}
			else
			{
                # Action not completed correctly
				$conn->close();
				return errors(409);
			}
		}
		else
		{
            # Bad connection to the Database
			$conn->close();
			return errors(500);
		}
	}

    # Query to add a comment of an specific design to the Database
    # Needs the username, designId and comment from the Applicarion layer
    function commentDesignDB($userName, $designId, $comment)
	{
		$conn = connect();
		if($conn != null)
		{
			$sql = "INSERT INTO Comment(comment, userName, design) VALUES ('$comment', '$userName', '$designId')";

			if(mysqli_query($conn, $sql))
			{
                $conn->close();
                return array("status" => "COMPLETE");
			}
			else
			{
                # Action not completed correctly
				$conn->close();
				return errors(409);
			}
		}
		else
		{
            # Bad connection to the Database
			$conn->close();
			return errors(500);
		}
	}

    # Query to get the comments of an specific design ordered by date
    # Return an array with all the comments
    function getCommentsOfDesignDB($designId)
    {
        $conn = connect();

        if($conn != null)
        {
            $sql = "SELECT Comment.*, User.fName,User.lName FROM Comment INNER JOIN User WHERE Comment.userName = User.userName AND design = '$designId' ORDER BY commentDate DESC";
            $result = $conn->query($sql);

            if($result->num_rows > 0)
            {
                $data = array();
                while($row = $result->fetch_assoc())
                {
                    array_push($data,array_map('utf8_encode', $row));
                }

                $response = array('message' => 'OK', 'data' => $data);
                $conn->close();
                return $response;
            }
            else
            {
                return array('message' => 'NONE');
            }
        }
        else
        {
            $conn->close();
            return errors(500);
        }
    }

?>
