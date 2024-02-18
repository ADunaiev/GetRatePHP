<!doctype html>
<html>
<head>

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
	
	<!--Let browser know website is optimized for mobile-->      
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/> 
	<title>Get rate</title>
    <link rel="stylesheet" href="/css/site.css">
</head>
<body>
	<div class="container">

        <nav>
            <div class="nav-wrapper light-blue">
            <a href="/" class="brand-logo center">Get Rate</a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <ul class="right hide-on-med-and-down">
                <!--
                <li><a href="sass.html">Sass</a></li>
                <li><a href="badges.html">Components</a></li>
                <li><a href="collapsible.html">Javascript</a></li>
                <li><a href="mobile.html">Mobile</a></li> -->
                <!-- Modal Trigger -->
                <a class="modal-trigger btn-flat" href="#modal1"><i style="color:white;" class="material-icons">perm_identity</i></a>      
            </ul>
            </div>
        </nav>

        <ul class="sidenav" id="mobile-demo">
            <!--
            <li><a href="sass.html">Sass</a></li>
            <li><a href="badges.html">Components</a></li>
            <li><a href="collapsible.html">Javascript</a></li>
            <li><a href="mobile.html">Mobile</a></li> -->
            <li><a class="modal-trigger" href="#modal1">Sign-In</a></li> 
        </ul>
        
        <?php include $page_body ; ?>

<!-- Modal Structure -->
<div id="modal1" class="modal">
    <div class="modal-content">
        <h5>Вхід у систему</h5>
        <div class="row">
            <form class="col s12">
                <div class="row">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">email</i>
                        <input id="sign-in-email" type="email" class="validate">
                        <label for="sign-in-email">Email</label>
                    </div>
                    <div class="input-field col s12">
                        <i class="material-icons prefix">password</i>
                        <input id="sign-in-password" type="password" class="validate">
                        <label for="sign-in-password">Password</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="row center-align">   
            <div class="col s12">
                <a href="#!" style="width:100%;" class="modal-close waves-effect blue btn">Sign in</a>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <a href="/signup" style="width:100%;" class="modal-close waves-effect waves-green btn">Sign Up</a>
            </div>
        </div>
    </div>
</div>

<!-- Compiled and minified JavaScript -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
 <script src="/js/site.js"></script>
</body>
   
</html>