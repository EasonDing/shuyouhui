<!DOCTYPE html>

<head>
    <title>LaraChat</title>

    <!-- META DATA -->
    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/bicolor.min.css">

    <!-- CSS Global Compulsory -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/animate.min.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/logo.css" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- FAVICONS -->
    <link rel="shortcut icon" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="assets/img/apple-touch-icon-114x114.png">

</head>

<body>
<div id="awd-site-wrap" class="bg bg-home">

    <div id="bg">
        <div id="overlay">
            <div class="awd-site-bg bg-home"></div>
            <div class="awd-site-bg bg-subscribe"></div>
            <div class="awd-site-bg bg-about"></div>
            <div class="awd-site-bg bg-contact"></div>
            <div class="awd-site-bg bg-services"></div>
        </div>
        <canvas id="awd-site-canvas"></canvas>
    </div>

    <!-- START SITE HEADER -->
    <header id="awd-site-header">
        <div id="awd-site-logo" class="animated" data-animation="fadeInDown" data-animation-delay="500">
            <h1><a href="#" target="_blank"><i class="icon-paint"></i><span>LaraChat</span></a></h1>
        </div>

        <button class="menu-toggle animated" data-animation="fadeInDown" data-animation-delay="500" data-role="toggle">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <!-- START NAVIGATION -->
        <nav id="awd-site-nav" class="navigation animated" data-animation="fadeInDown" data-animation-delay="500">
            <div class="nav-container">

                <!-- START NAVIGATION MENU ITEMS -->
                <ul>
                    <li>
                        <a href="#" data-slide="home" class="active">首页</a>
                    </li>
                    <li>
                        <a href="#" data-slide="about">关于我们</a>
                    </li>
                    <li>
                        <a href="#" data-slide="services">我们的服务</a>
                    </li>
                    <li>
                        <a href="#" data-slide="contact">联系我们</a>
                    </li>
                </ul>
                <!-- END NAVIGATION MENU ITEMS -->

            </div>
        </nav>
        <!-- END NAVIGATION -->
    </header>
    <!-- END SITE HEADER -->

    <!-- START MAIN -->
    <main id="awd-site-main">
        <!-- START SECTION -->
        <section id="awd-site-content">
            <div class="sections-block">
                <div class="slides">

                    <div class="slides-wrap">
                        <!-- HOME SECTION -->
                        <div class="slide-item active" data-slide-id="home">
                            <!-- START CONTAINER -->
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- START SLIDE CONTENT-->
                                        <div class="slide-content">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 svm">
                                                    <div class="section-info text-left">
                                                        <div class="countdown">
                                                            <div id="clock" class="animated" data-animation="fadeIn" data-animation-delay="60"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5 col-lg-offset-1 col-md-6 svm">
                                                    <div class="section-info text-right">
                                                        <!-- START TITLE -->
                                                        <h2 class="section-title text-default animated" data-animation="fadeIn" data-animation-delay="50">嘿 小伙伴们!
                                                            <br> 我们即将来改变世界…</h2>
                                                        <!-- END TITLE -->
                                                        <p class="animated" data-animation="fadeIn" data-animation-delay="55">LaraChat 正在建设中...<br/> 很快就会与大家见面！</p>
                                                        <a href="#" class="btn go-slide animated" data-animation="fadeIn" data-animation-delay="60" data-slide="about">更多信息</a>
                                                        <a href="{{ url('/login') }}" class="btn btn-inverse animated" data-animation="fadeIn" data-animation-delay="60" data-slide="subscribe">登录！</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END SLIDE CONTENT-->
                                    </div>
                                </div>
                            </div>
                            <!-- END CONTAINER -->
                        </div>

                        <!-- ABOUT SECTION -->
                        <div class="slide-item" data-slide-id="about">
                            <!-- START CONTAINER -->
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">

                                        <!-- START SLIDE CONTENT-->
                                        <div class="slide-content">
                                            <div class="row">
                                                <div class="col-lg-5 col-lg-offset-2 col-md-6 col-md-offset-1 col-md-push-5 svm">
                                                    <div class="section-info text-right">
                                                        <!-- START TITLE -->
                                                        <h2 class="section-title text-default animated" data-animation="fadeIn" data-animation-delay="60">关于我们</h2>
                                                        <!-- END TITLE -->
                                                        <p class="animated" data-animation="fadeIn" data-animation-delay="100">LaraChat 是一个<strong>创造性</strong>
                                                            的应用, 适用于每一位用户。</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 col-md-pull-7 svm">
                                                    <div class="section-info text-left">
                                                        <!-- START FEATURES-LIST -->
                                                        <div class="features-list">

                                                            <!-- FEATURE -->
                                                            <div class="featured-item animated" data-animation="fadeIn" data-animation-delay="150">
                                                                <div class="featured-text">
                                                                    <h3><i class="fa fa-rocket"></i> 快速的</h3>
                                                                    <p>快速下单, 快速上门, 快速服务。都是一瞬间的事情!
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="featured-item">
                                                                <!-- FEATURE -->
                                                                <div class="featured-item animated" data-animation="fadeIn" data-animation-delay="150">
                                                                    <div class="featured-text">
                                                                        <h3><i class="icon-paint"></i> 无限的可能</h3>
                                                                        <p>发挥我们的创造力，带给你们不一样的惊喜！
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <!-- END FEATURES-LIST -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- END SLIDE CONTENT-->
                                    </div>
                                </div>
                            </div>
                            <!-- END CONTAINER -->
                        </div>

                        <!-- SERVICES SECTION -->
                        <div class="slide-item" data-slide-id="services">
                            <!-- START CONTAINER -->
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- START SLIDE CONTENT-->
                                        <div class="slide-content">
                                            <div class="row">
                                                <div class="col-lg-5 col-lg-offset-2 col-md-6 col-md-offset-1 col-md-push-5 svm">
                                                    <div class="section-info text-right">
                                                        <!-- START TITLE -->
                                                        <h2 class="section-title text-default animated" data-animation="fadeIn">我们的服务</h2>
                                                        <!-- END TITLE -->
                                                        <p class="animated" data-animation="fadeIn" data-animation-delay="100" style="text-align: left;">本着“安全、环保、快速、方便、经济、前卫”，的设计理念。具有综合性、专业性、系统性强的特点。
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 col-md-pull-7 svm">
                                                    <div class="section-info text-left">
                                                        <!-- SERVICE -->
                                                        <div class="service animated" data-animation="fadeIn" data-animation-delay="150">
                                                            <h3>效果显著</h3>
                                                            <p>用心的产品，细腻的服务。带给你们真心，放心！</p>
                                                        </div>

                                                        <!-- SERVICE -->
                                                        <div class="service animated" data-animation="fadeIn" data-animation-delay="150">
                                                            <h3>充分响应</h3>
                                                            <p>快速的响应，便捷的操作，带给你预约的体验！</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- END SLIDE CONTENT-->
                                    </div>
                                </div>
                            </div>
                            <!-- END CONTAINER -->
                        </div>

                        <!-- CONTACT SECTION -->
                        <div class="slide-item" data-slide-id="contact">
                            <!-- START CONTAINER -->
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- START SLIDE CONTENT-->
                                        <div class="slide-content">
                                            <div class="row">
                                                <div class="col-lg-5 col-lg-offset-1 col-md-6 col-md-push-6 svm">

                                                    <div class="section-info text-right">
                                                        <!-- START TITLE -->
                                                        <h2 class="section-title text-default animated" data-animation="fadeIn">联系我们</h2>
                                                        <!-- END TITLE -->
                                                        <div class="contact-info">
                                                            <p class="contact-item"><i class="fa fa-phone"></i> 电话: 123-4567-890</p>
                                                            <p class="contact-item"><i class="fa fa-envelope"></i> Email: develop@laralab.cn</p>
                                                            <p class="contact-item"><i class="fa fa-map-marker"></i> 嘉兴市智慧创新产业园2号楼三楼</p>
                                                        </div>
                                                        <p class="animated" data-animation="fadeIn" data-animation-delay="100">
                                                            <strong>想打个招呼吗?</strong><strong> - 欢迎在线留言。<br/> 期待你们的关注。</strong>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-md-pull-6 svm">
                                                    <div class="section-info text-left">
                                                        <!-- START CONTACT FORM -->
                                                        <form id="contact-form" class="contact-form">
                                                            <div class="row">
                                                                <div class="col-lg-4 col-md-6 col-md-12 animated" data-animation="fadeIn" data-animation-delay="200">
                                                                    <input type="text" name="contact-name" placeholder="姓名" class="contact-form-name required">
                                                                </div>
                                                                <div class="col-lg-4 col-md-6 col-md-12 animated" data-animation="fadeIn" data-animation-delay="200">
                                                                    <input type="email" name="contact-email" placeholder="邮箱地址" class="contact-form-email required">
                                                                </div>
                                                                <div class="col-lg-4 col-md-12 animated" data-animation="fadeIn" data-animation-delay="200">
                                                                    <input type="text" name="contact-subject" placeholder="主题" class="contact-form-subject required">
                                                                </div>
                                                                <!-- END COLUMN 4 -->
                                                                <div class="col-md-12 animated" data-animation="fadeIn" data-animation-delay="150">
                                                                    <textarea name="contact-message" placeholder="在线留言" class="contact-form-message required" rows="4"></textarea>
                                                                    <button class="btn btn-block" type="submit" id="submit" name="submit"><span>发送消息</span>
                                                                        <i class="fa fa-send"></i></button>
                                                                </div>
                                                                <!-- END COLUMN 8 -->
                                                            </div>
                                                            <div class="contact-notice"></div>
                                                        </form>
                                                        <!-- END CONTACT FORM -->
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- END SLIDE CONTENT-->
                                    </div>
                                </div>
                            </div>
                            <!-- END CONTAINER -->
                        </div>

                    </div>
                </div>
            </div>
        </section>
        <!-- END SECTION -->
    </main>
    <!-- END MAIN -->

    <footer id="awd-site-footer">
        <!-- START COPYRIGHT -->
        <div class="copyright animated" data-animation="fadeInUp" data-animation-delay="500">
            <p>© 2017 LaraLab 实验室 - 版权所有</p>
        </div>
        <!-- END COPYRIGHT -->

        <!-- START SOCIAL ICONS -->
        <nav class="social-icons animated" data-animation="fadeInUp" data-animation-delay="500">
            <ul>
                <li>
                    <a href="#"><i class="fa fa-facebook"></i></a>
                </li>
                <li>
                    <a href="#" target="_blank"><i class="fa fa-twitter"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-google-plus"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-linkedin"></i></a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-pinterest"></i></a>
                </li>
            </ul>
        </nav>
        <!-- END SOCIAL ICONS -->
    </footer>

</div>

<!-- JS -->
<script type="text/javascript" src="/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/js/vendor.js"></script>
<script type="text/javascript" src="/js/options.js"></script>
<script type="text/javascript" src="/js/index.js"></script>

</body>

</html>