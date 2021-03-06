<?php

    # Header required when receiving content from the ajax at the front-end
    header('Content-type: application/json');

    # Connection to the dataLayer
	require_once __DIR__ . '/dataLayer.php';

    # Execute the action that is being called in the ajax at the front-end
	$action = $_POST['action'];
	switch($action)
	{
        case 'LOGIN':	      userLogin();
						      break;
		case 'COOKIE':	      verifyCookies();
						      break;
		case 'END_SES':       endSession();
						      break;
		case 'GET_SES':	      getSession();
						      break;
		case 'REGISTER':      registerUser();
						      break;
        case 'SET_DESIGN':    setDesignSession();
                              break;
        case 'SET_PRODUCT':   setProductSession();
                              break;
        case 'NEW_DESIGNS':   getDesigns('NEW_DESIGNS');
                              break;
        case 'MOST_POP_WEEK': getDesigns('MOST_POP_WEEK');
                              break;
        case 'DESIGN':        getCompleteDesign();
                              break;
        case 'PRODUCT':       getCompleteProduct();
                              break;
        case 'GET_CART':      getUserCart();
                              break;
        case 'ADD_CART':      addItemsToCart();
                              break;
        case 'DELETE_CART':   deleteItemFromCart();
                              break;
        case 'BUY_CART':      buyCart();
                              break;
        case 'PAST_ORDERS':   getOrderHistory();
                              break;
        case 'UP_DESIGN':     uploadDesign();
                              break;
        case 'COMMENT':       commentDesign();
                              break;
        case 'GET_COMMENT':   getCommentsOfDesign();
                              break;
        case 'ADD_FILE':      addDesignFile();
                              break;
        case 'GET_USER':      getUserInfo();
                              break;
        case 'USER_DESIGNS':  getUserDesigns();
                              break;
        case 'SET_UINFO':     setUserInfo();
                              break;
	}

    # ACtion to login the current user credentials and redirect it to home.html
	function userLogin()
	{
		$userName = $_POST['userName'];
		$password = $_POST['userPassword'];
		$rememberData = $_POST['rememberData'];

		# Verify if the user currently exists in the Database
		$result = validateUserCredentials($userName);

		if ($result['status'] == 'COMPLETE')
		{
			$decryptedPassword = decryptPassword($result['password']);

			# Compare the decrypted password with the one provided by the user
		   	if ($decryptedPassword === $password)
		   	{
		    	$response = array("status" => "COMPLETE");

			    # Starting the sesion
		    	startSession($result['fName'], $result['lName'], $userName);

			    # Setting the cookies
			    if ($rememberData)
				{
					setcookie("cookieUserName", $userName);
			  	}
			    echo json_encode($response);
			}
			else
			{
				die(json_encode(errors(306)));
			}
		}
		else
		{
			die(json_encode($result));
		}
	}

    # Action to get the current cookies if they exist
	function verifyCookies()
	{
		if (isset($_COOKIE['cookieUserName']))
		{
			echo json_encode(array('cookieUserName' => $_COOKIE['cookieUserName']));
		}
		else
		{
			# Cookie not set yet
		    die(json_encode(errors(417)));
		}
	}

    # Action to register a user
	function registerUser()
	{
		$userName = $_POST['userName'];

		# Verify that the user doesn't exist in the database
		$result = verifyUser($userName);

		if ($result['status'] == 'COMPLETE')
		{
			$email = $_POST['email'];
			$userFirstName = $_POST['userFirstName'];
			$userLastName = $_POST['userLastName'];

			$userPassword = encryptPassword();

			# Make the insertion of the new user to the Database
			$result = registerNewUser($userFirstName, $userLastName, $userName, $email, $userPassword);

			# Verify that the insertion was successful
			if ($result['status'] == 'COMPLETE')
			{
				# Starting the session
				startSession($userFirstName, $userLastName, $userName);
				echo json_encode($result);
			}
			else
			{
				# Something went wrong while inserting the new user
				die(json_encode($result));
			}
		}
		else
		{
			# Username already exists
			die(json_encode($result));
		}
	}

    # Action to encrypt the password of the user
	function encryptPassword()
	{
		$userPassword = $_POST['userPassword'];

	    $key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	    $key_size =  strlen($key);

	    $plaintext = $userPassword;

	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
	    $ciphertext = $iv . $ciphertext;

	    $userPassword = base64_encode($ciphertext);

	    return $userPassword;
	}

    #Action to decrypt the password of the user
	function decryptPassword($password)
	{
		$key = pack('H*', "bcb04b7e103a05afe34763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

	    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    $ciphertext_dec = base64_decode($password);
	    $iv_dec = substr($ciphertext_dec, 0, $iv_size);
	    $ciphertext_dec = substr($ciphertext_dec, $iv_size);

	    $password = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

	   	$count = 0;
	   	$length = strlen($password);

	    for ($i = $length - 1; $i >= 0; $i --)
	    {
	    	if (ord($password{$i}) === 0)
	    	{
	    		$count ++;
	    	}
	    }

	    $password = substr($password, 0,  $length - $count);

	    return $password;
	}

    # Action to set the initial values of the session
	function startSession($fName, $lName, $username)
    {
		// Starting the session
	    session_start();
		$_SESSION['userFirstName'] = $fName;
	    $_SESSION['userLastName'] = $lName;
	    $_SESSION['userName'] = $username;
    }

    # Action to get the current session data
    function getSession()
    {
    	session_start();
    	if (isset($_SESSION['userFirstName']) && $_SESSION['userLastName'] && $_SESSION['userName'])
    	{
    		echo json_encode(array("firstName" => $_SESSION['userFirstName'], "lastName" => $_SESSION['userLastName']));
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to set a design id in the session
	function setDesignSession()
    {
        $designId = $_POST['designId'];
		// Starting the session
	    session_start();
		$_SESSION['designId'] = $designId;
    }
    # Action to set a product name in the session
	function setProductSession()
    {
        $productName = $_POST['productName'];
		// Starting the session
	    session_start();
		$_SESSION['productName'] = $productName;
    }

    # Action to get the current session data
    function getProductSession()
    {
    	session_start();
    	if (isset($_SESSION['productName']) && $_SESSION['designId'])
    	{
    		echo json_encode(array("productName" => $_SESSION['productName'], "designId" => $_SESSION['designId']));
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to end the current session data
    function endSession()
	{
		session_start();
		if (isset($_SESSION['userFirstName']) && isset($_SESSION['userLastName']) && isset($_SESSION['userName']))
		{
			unset($_SESSION['userFirstName']);
			unset($_SESSION['userLastName']);
			unset($_SESSION['userName']);

			echo json_encode(array('success' => 'Session deleted'));
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}



    # Action to get all designs from the newest to the oldest
    # It doesn't need anything from the front-end, just the action
    function getDesigns($act)
    {
        # Execute the action to get different designs
        switch($act)
    	{
            case 'NEW_DESIGNS':   $result = getNewestDesignsDB();
                                  break;
            case 'MOST_POP_WEEK': $result = mostPopularInTheWeekDB();
                                  break;
    	}

        if($result['message'] == 'OK')
        {
            echo json_encode($result);
        }
        else
        {
            if($result['message'] == 'NONE')
            {
                # Designs not found
                echo json_encode($result);
            }
            else
            {
                die(json_encode($result));
            }
        }
    }

    # Action to get the whole design info
    # Neeeds the designId from the session
    function getCompleteDesign()
    {
        session_start();
    	if (isset($_SESSION['designId']))
    	{
    		$designId = $_SESSION['designId'];

            $result = getCompleteDesignDB($designId);

            if($result['message'] == 'OK')
            {
                # All the info of the design
                echo json_encode($result);
            }
            else
            {
                if($result['message'] == 'NONE')
                {
                    # No designs with that designId
                    echo json_encode($result);
                }
                else
                {
                    # Error
                    die(json_encode($result));
                }
            }
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to get the whole user info
    function getUserInfo()
    {
        session_start();
    	if (isset($_SESSION['userName']))
    	{
            $result = getUserInfoDB($_SESSION['userName']);

            if($result['message'] == 'OK')
            {
                # All the info of the design
                echo json_encode($result);
            }
            else
            {
                if($result['message'] == 'NONE')
                {
                    # No designs with that designId
                    echo json_encode($result);
                }
                else
                {
                    # Error
                    die(json_encode($result));
                }
            }
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to get set some user info
    function setUserInfo()
    {
        session_start();
    	if (isset($_SESSION['userName']))
    	{
            $fName =  $_POST['fName'];
            $lName =  $_POST['lName'];
            $email =  $_POST['email'];
            $city =  $_POST['city'];
            $address =  $_POST['address'];
            $aboutme =  $_POST['aboutme'];


            $result = setUserInfoDB($_SESSION['userName'], $fName, $lName, $email, $city, $address, $aboutme);

            if($result['message'] == 'OK')
            {
                # All the info of the design
                echo json_encode($result);
            }
            else
            {
                # Error
                die(json_encode($result));
            }
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    # Action to get the user designs
    function getUserDesigns()
    {
        session_start();
    	if (isset($_SESSION['userName']))
    	{
            $result = getUserDesignsDB($_SESSION['userName']);

            if($result['message'] == 'OK')
            {
                # All the info of the design
                echo json_encode($result);
            }
            else
            {
                if($result['message'] == 'NONE')
                {
                    # No designs with that designId
                    echo json_encode($result);
                }
                else
                {
                    # Error
                    die(json_encode($result));
                }
            }
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }


    # Action to get the whole product info
    # Neeeds the productId from the session
    function getCompleteProduct()
    {
        session_start();
    	if (isset($_SESSION['productName']))
    	{
    		$productName = $_SESSION['productName'];

            $result = getCompleteProductDB($productName);

            if($result['message'] == 'OK')
            {
                # All the info of the design
                $result['unitPrice'] = getUnitPrice();
                $result['designId'] = $_SESSION['designId'];
                echo json_encode($result);
            }
            else
            {
                if($result['message'] == 'NONE')
                {
                    # No designs with that designId
                    echo json_encode($result);
                }
                else
                {
                    # Error
                    die(json_encode($result));
                }
            }
    	}
    	else
    	{
    		echo json_encode(errors(417));
    	}
    }

    #Action to get user cart
	function getUserCart()
	{
		session_start();
		if (isset($_SESSION['userName']))
		{
			$result = getCartDB($_SESSION['userName']);

			if ($result['message'] == 'OK')
			{
				echo json_encode($result);
			}
			else
			{
				if ($result['message'] == 'NONE')
				{
					echo json_encode($result);
				}
				else
				{
					die(json_encode($result));
				}
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}

    #Action to get user order history
	function getOrderHistory()
	{
		session_start();
		if (isset($_SESSION['userName']))
		{
			$result = getOrderHistoryDB($_SESSION['userName']);

			if ($result['message'] == 'OK')
			{
				echo json_encode($result);
			}
			else
			{
				if ($result['message'] == 'NONE')
				{
					echo json_encode($result);
				}
				else
				{
					die(json_encode($result));
				}
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}


    # Action to add an order to the Cart
    # Needs all the columns of the cart except username from the front-end that includes:
    # designId, productId, color, size, unitPrice (productPrice+designPrice),
    # quantity and the total price of the order
	function addItemsToCart()
	{
        session_start();
		if(isset($_SESSION['userName']) && isset($_SESSION['designId']) && isset($_SESSION['productName']))
		{
            $userName = $_SESSION['userName'];
            $design = $_SESSION['designId'];
            $product =  $_SESSION['productName'];
            $color =  $_POST['color'];
            $size =  $_POST['size'];
            $quantity =  $_POST['quantity'];
            $unitPrice = getUnitPrice();

            $totalPrice =  $unitPrice * intval($quantity);

			$result = addItemsToCartDB($userName, $design, $product, $color, $size, $unitPrice, $quantity, $totalPrice);
			if($result['status'] == 'COMPLETE')
			{
				echo json_encode($result);
			}
			else
			{
                # Something went wrong
				die(json_encode($result));
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
	}

    function getUnitPrice()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

		if(isset($_SESSION['designId']) && isset($_SESSION['productName']))
		{
            $design = $_SESSION['designId'];
            $product =  $_SESSION['productName'];

            $productPrice = getProductPriceDB($product);
            $designPrice = getDesignPriceDB($design);
            $unitPrice = $productPrice + (($designPrice * $productPrice) / 100.0);

			return $unitPrice;
		}
		else
		{
			die(json_encode(errors(417)));
		}
    }

    # Action to delete a single order from the cart
    # Needs the orderId of the order from the front-end
    function deleteItemFromCart()
    {
        $orderId = $_POST['orderId'];

        session_start();
		if(isset($_SESSION['userName']))
		{
			$result = deleteItemFromCartDB($orderId);
			if($result['status'] == 'COMPLETE')
			{
				echo json_encode($result);
			}
			else
			{
                # Something went wrong
				die(json_encode($result));
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
    }

    # Action to complete the purchase of the orders from the cart
    # It needs the action and an array with the orderIds from the front-end
    function buyCart()
    {
        $orders = $_POST['orders'];

		session_start();
		if(isset($_SESSION['userName']))
		{
			for ($i = 0; $i < count($orders); ++$i) {
    			$orderId = $orders[$i]['orderId'];

				$result = buyCartDB($orderId);

				if($result['status'] != 'COMPLETE')
				{
					die(json_encode($result));
				}
    		}
			echo json_encode(array('message' => 'COMPLETE'));
		}
		else
		{
			die(json_encode(errors(417)));
		}
    }

    # Action to upload a design
    # Needs the designName, the price given by the user for that design and the description from the front-end
    # Return a json with the designId to use it to save the image in the corresponding folder
    function uploadDesign($designId)
    {
		session_start();
		if(isset($_SESSION['userName']))
		{
            $designName = $_POST['designName'];
            $price =  $_POST['price'];
            $description =  $_POST['description'];

			$result = uploadDesignDB($designId, $_SESSION['userName'], $designName, $description, $price);

			if($result['status'] == 'COMPLETE')
			{
				return $result;
			}
			else
			{
                # Something went wrong
				die(json_encode($result));
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
    }

    # Action to add a comment of a design
    # Needs the designId and the comment from the front-end
    function commentDesign()
    {
		session_start();
		if(isset($_SESSION['userName']))
		{
            $designId = $_SESSION['designId'];
            $comment = $_POST['comment'];

			$result = commentDesignDB($_SESSION['userName'], $designId, $comment);

			if($result['status'] == 'COMPLETE')
			{
                $result['firstName'] = $_SESSION['userFirstName'];
                $result['lastName'] = $_SESSION['userLastName'];
				echo json_encode($result);
			}
			else
			{
                # Something went wrong
				die(json_encode($result));
			}
		}
		else
		{
			die(json_encode(errors(417)));
		}
    }

    # Action to get all the comments from a design
    # Neeeds the designId from the front-end
    # Return a json with the username, comment and Date
    function getCommentsOfDesign()
    {
        session_start();
        $designId = $_SESSION['designId'];
        $result = getCommentsOfDesignDB($designId);

        if($result['message'] == 'OK')
        {
            # The json of the comment
            echo json_encode($result);
        }
        else
        {
            if($result['message'] == 'NONE')
            {
                # No comments with that designId
                echo json_encode($result);
            }
            else
            {
                # Error
                die(json_encode($result));
            }
        }
    }

    function addDesignFile()
    {
        // 'images' refers to your file input name attribute
        if (empty($_FILES['images'])) {
            echo json_encode(['error'=>'No files found for upload.']);
            // or you can throw an exception
            return; // terminate
        }

        // get the files posted
        $images = $_FILES['images'];

        // get user id posted
        //$userid = empty($_POST['userid']) ? '' : $_POST['userid'];

        // get user name posted
        //$username = empty($_POST['username']) ? '' : $_POST['username'];

        // a flag to see if everything is ok
        $success = null;

        // file paths to store
        $paths= [];

        // get file names
        $filenames = $images['name'];

        // loop and process files
        for($i=0; $i < count($filenames); $i++){
            $ext = explode('.', basename($filenames[$i]));
            $fileNewName = md5(uniqid()) . "." . array_pop($ext);
            $target = "../img/designs" . DIRECTORY_SEPARATOR . $fileNewName;
            if(move_uploaded_file($images['tmp_name'][$i], $target)) {
                $success = true;
                $paths[] = $target;
            } else {
                $success = false;
                break;
            }
        }

        // check and process based on successful status
        if ($success === true) {
            // call the function to save all data to database
            // code for the following function `save_data` is not
            // mentioned in this example
            uploadDesign($fileNewName);

            // store a successful response (default at least an empty array). You
            // could return any additional response info you need to the plugin for
            // advanced implementations.
            $output = ['newName'=>$fileNewName];
            // for example you can get the list of files uploaded this way
            // $output = ['uploaded' => $paths];
        } elseif ($success === false) {
            $output = ['error'=>'Error while uploading images. Contact the system administrator'];
            // delete any uploaded files
            foreach ($paths as $file) {
                unlink($file);
            }
        } else {
            $output = ['error'=>'No files were processed.'];
        }

        // return a json encoded response for plugin to process successfully
        echo json_encode($output);
    }

?>
