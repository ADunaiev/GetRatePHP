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
        <header class="container">
            <nav>
                <div class="nav-wrapper cyan darken-1">
                    <a href="/" class="brand-logo center">Get Rate</a>
                    <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                    <ul class="right hide-on-med-and-down">

                        <li><a href="/request">Request</a></li>
                        <!--
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
                <li><a class="modal-trigger" href="/request">Request</a></li> 
                <li><a class="modal-trigger" href="#modal1">Sign-In</a></li> 
            </ul>

            <!-- Modal Structure -->
            <div id="modal1" class="modal">
                <form class="modal-content">
                    <h5>Authorization</h5>
                    <div class="row">
                        <div class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">email</i>
                                    <input id="sign-in-email" name="sign-in-email" type="email" class="validate">
                                    <label for="sign-in-email">Email</label>
                                    <span class="helper-text" data-error="wrong" data-success="right">Ваш email</span>
                                </div>
                                <div class="input-field col s12">
                                    <i class="material-icons prefix">password</i>
                                    <input id="sign-in-password" name="sign-in-password" type="password" class="validate">
                                    <label for="sign-in-password">Password</label>
                                    <span class="helper-text" data-error="wrong" data-success="right">Пароль</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row center-align">   
                        <div class="col s12">
                            <!--<a href="#!" style="width:100%;" class="modal-close waves-effect blue btn">Sign in</a> -->
                            <button style="width:100%;" class="waves-effect blue btn" type="button" id="auth-button">Sign in</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <a href="/signup" style="width:100%;" class="modal-close waves-effect waves-green btn">Sign Up</a>
                        </div>
                    </div>
                </form>
            </div>

        </header>

        <main class="container">
            <?php include $page_body ; ?>
        </main>
      
        <!-- Footer -->
        <footer class="page-footer cyan darken-1 container">
            <div class="container">
                <div class="row">
                    <div class="col l6 s12">
                        <h5 class="white-text">Get Rate</h5>
                        <p class="grey-text text-lighten-4">
                            Developer Andrii Dunaiev</br>
                            adunaev@me.com
                        </p>
                    </div>
                    <div class="col l4 offset-l2 s12">
                        <h5 class="white-text">Links</h5>
                        <ul>
                            <li><a class="grey-text text-lighten-3" href="https://gol.ua/">Global Ocean Link</a></li>
                            <li><a class="grey-text text-lighten-3" href="https://gol.lt/">Global Ocean Link Lithuania</a></li>
                            <li><a class="grey-text text-lighten-3" href="https://globaloceanlink.pl/">Global Ocean Link Poland</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-copyright">
                <div class="container">
                    © 2024 Copyright Andrii Dunaiev
                    <a class="grey-text text-lighten-4 right" href="#!">More Links</a>
                </div>
            </div>
        </footer>      
    </body>

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="/js/site.js"></script>
</html>