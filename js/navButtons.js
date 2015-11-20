$(document).on('ready',function() {

    // Setting the action to get user session
    $.ajax({
        type: 'POST',
        url: 'data/applicationLayer.php',
        dataType: 'json',
        data: {'action':'GET_SES'},
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        success: function(jsonData) {
            var navButtons = '<li class="dropdown">' +
                '<a href="#" class="dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-user" aria-hidden="true"></span> User <b class="caret"></b></a>' +
                '<ul class="dropdown-menu">'+
                    '<li><a href="userProfile.html">My Profile</a></li>' +
                    '<li><a href="orderHistory.html">Order History</a></li>' +
                    '<li class="divider"></li>' +
                    '<li><a href="uploadDesign.html">Upload Design</a></li>' +
                    '<li class="divider"></li>' +
                    '<li><a href="#">Logout</a></li></ul></li>' +
                '<li><a href="cart.html"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Cart </a></li>';

                $("#navRightButtons").html(navButtons);
        },
        error: function(errorMsg){
            var navButtons = '<li><a href="login.html">Login / Register</a></li>';

            $("#navRightButtons").html(navButtons);
        }
    });

});
