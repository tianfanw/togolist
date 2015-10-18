<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ToGolist</title>

    <link href="/css/normalize.css" rel="stylesheet">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/popup.css" rel="stylesheet">
    <link href="/css/base.css" rel="stylesheet">
    <link href="/css/navbar.css" rel="stylesheet">
    @yield('css')

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Francois+One' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-default">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll" id="navbar-logo">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="/">ToGoList</a>
                <div id="navbar-search-bar-mobile">
                    <form class="navbar-form navbar-search-form" role="search">
                        <input type="text" class="form-control navbar-search-input" placeholder="Enter Interest or Location" required>
                    </form>
                </div>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" id="navbar-menu">
                    @include('partials.navbar-menu')
                </ul>
            </div>
            <!-- /.navbar-collapse -->

            <!-- Search bar -->
            <div id="navbar-search-bar">
                <form class="navbar-form navbar-search-form" role="search">
                    <input type="text" class="form-control navbar-search-input" placeholder="Enter Interest or Location" required>
                </form>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>

    <!-- Navbar popups -->
    <div id="navbar-popup">
        @include('partials.navbar-popup')
    </div>

    <!-- Flash messages -->
    <div class="container" id="flash-messages">
        @include('partials.flash')
    </div>

    <!-- Main -->
    <div class="container" id="body-container">
        @yield('main')
        
        <div class="footer">
            <div class="container">
                <p>Copyright &copy; 2015 ToGoList Inc. All Rights Reserved. Contact Info:
                    <a href="mailto:contact@togolist.org">contact@togolist.org</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/base.js"></script>
    @yield('javascript')
    <script src="/js/popup.js"></script>
</body>
</html>
