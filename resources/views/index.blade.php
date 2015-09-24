<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ToGoList</title>

    <link href="/css/normalize.css" rel="stylesheet">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/popup.css" rel="stylesheet">
    <link href="/css/base.css" rel="stylesheet">
    <link href="/css/index.css" rel="stylesheet">

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
<body id="page-top" class="index">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#">ToGoList</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" id="navbar-menu">
                    @include('partials.navbar-menu')
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

    <div class="container" id="flash-messages">
        @include('partials.flash')
    </div>

    <div class="main">
        <!-- Search box -->
        <div class="container">
            <div class="search-box">
                <div class="intro-text">Find the Best Destination According to Your Personal</div>
                <div class="intro-text">Interest, Background, or Preference</div>
                <div class="search-bar">
                    <form class="search-form">
                        <input type="text" id="search-input" placeholder="Enter Interest or Location" required>
                        <button type="submit" id="search-button" class="btn btn-default">EXPLORE</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Categories -->
        <div class="container">
            <div class="strike">
                <span>Or Choose from Popular Interest Categories</span>
            </div>
            <div class="row category-container">
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
                <div class="category">
                    <a href="/list"><img class="P1" alt="P1" src="image/P1.png"></a>
                </div>
            </div>
        </div>

        <!-- Navbar popups -->
        <div id="navbar-popup">
            @include('partials.navbar-popup');
        </div>

        <!-- Footer -->
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

    <!-- Javascript plugin for scroll effect -->
    <script src="/js/classie.js"></script>
    <script src="/js/cbpAnimatedHeader.js"></script>

    <script src="/js/base.js"></script>
    <script src="/js/popup.js"></script>
</body>