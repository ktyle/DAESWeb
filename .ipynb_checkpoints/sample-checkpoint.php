<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAlbany Weather Data and Forecasts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@100;300&display=swap" rel="stylesheet">
    <script 
        src="https://code.jquery.com/jquery-3.7.1.js" 
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" 
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
    <style>
    body {
        font-family: Public Sans, sans-serif;
        font-weight: 100;
        margin: 0;
        padding: 0;
    }
    
    @font-face {
        font-family: ualbFont;
        src: url(tradegothicltstd-bdcn20-webfont.woff);
    }
    
    /*this is the start of stuff for the nav bar/dropdown*/
    .nav-bar {
        z-index: 1000;
        top: 0;
        position: fixed;
        background-color: #333;
        height: 134px;
        width: 100%;
        padding-left: 15px;
        padding-right: 15px;
        font-weight: 300;
    }

    .nav-bar-logos {
        z-index: 1001;
        width: 450px;
        position: fixed;
        background-color: #333;
        top: 16px;
        left: 20px;
    }

    .nav-bar-list {
        display: grid;
        grid-template-columns: 40px auto auto auto auto auto 100px;
        list-style-type: none;
        position: absolute;
        border-top: 0.5px solid #888;
        width: 96%;
        height: 41px;
        color: rgb(238, 238, 238);
        font-size: 16px;
        line-height: 24px;
        margin-top: -8px;
        margin-bottom: 0;
        padding: 0 0 0 1em;
    }

    .nav-button {
        background-color: rgba(0, 0, 0, 0);
        border: none;
        color: white;
        position: absolute;
        text-align: center;
        height: 40px;
        width: 40px;
        left: 10px;
        top: 1px;
    }

    .nav-button:hover {
        background-color: #ffc107;
        cursor: pointer;
    }

    .nav-item {
        height: 100%;
    }

    .nav-item-select {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        transition: 0.5s;
    }

    .nav-item-select:hover {
        background-color: #ffc107;
        color: #333;
    }

    .nav-item-text {
        display: block;
        position: absolute;
        bottom: -8px;
        text-decoration: none;
        white-space: nowrap;
        padding-left: 10px;
    }

    .dropdown-list {
        display: none;
        color: rgb(238, 238, 238);
        background-color: #333;
        width: 18.8%;
        font-size: 16px;
        line-height: 24px;
        list-style-type: none;
        position: absolute;
        top: 40px;
        height: 400px;
        padding-left: 0;
    }

    .dropdown-link {
        text-decoration: none;
        color: rgb(238, 238, 238);
    }

    .dropdown-item {
        height: auto;
        min-height: 36px;
        color: rgb(238, 238, 238);
        font-size: 16px;
        line-height: 24px;
        margin-top: 0px;
    }

    .dropdown-text {
        padding: 6px 16px 0 10px;
    }

    .dropdown-item-blank {
        height: auto;
        min-height: 36px;
        color: rgb(238, 238, 238);
        line-height: 24px;
        margin-top: 0px;
    }

    .nav-item-select-blank {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    /*this is the end of stuff for the nav bar/dropdown
      and the start of stuff for the header img*/
    .header-container {
        width: 100%;
    }
    
    .header-img-container {
        margin: 2em 2em;
        overflow: hidden;
        position: relative;
    }
    
    .header-img {
        height: auto;
        width: auto;
        max-width: none;
    }
    
    .header-text-container {
        position: absolute;
        bottom: 15%;
        left: 40px;
        padding: 2em 1.5em 1.5em;
        background-color: rgba(55, 17, 84, .8);
        display: block;
        max-width: 40%;
        min-width: 400px;
    }

    .header-title {
        color: white; 
        font-family: ualbFont; 
        font-size: 4em; 
        text-transform: uppercase; 
        margin-top: 0;
        margin-bottom: 0;
    }

    .header-body {
        color: white; 
        font-size: 22px; 
        font-family: 'Public Sans', sans-serif;
    }
        
    /*this is the end of stuff for the header img*/
    .section-title {
        font-family: ualbFont;
        color: #46166B;
        font-size: 3em; 
        padding-left: 1.2em;
    }

    .section-title-link {
        font-family: ualbFont;
        color: #46166B;
        font-size: 3em; 
        padding-left: 1.2em;
        font-weight: bold;
    }
    
    .section-title-link:link, .section-title-link:visited, .section-title-link:active {
        color: #46166B; 
        background-color: transparent; 
        text-decoration: none;
    }
    
    .section-title-link:hover {
        text-decoration: underline;
    }
    /*start of current conditions section*/
    .current-conditions-container { 
        list-style-type: none;
        padding: 0;
        height: auto;
        max-height: 525px;
        padding: 0;
    }
    
    .section-title-container {
        margin-left: auto;
        margin-right: auto;
        max-width: 1500px;
    }
    
    .current-conditions-container-list {
        display: flex;
        background-image: url('images/cloud.png');
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
        flex-direction: row;
        list-style-type: none;
        padding: 0;
    }
    
    .inner-list-1 {
        width: 58%; /* Adjust the spacing between lists */
        max-width: 800px;
        margin-left: auto;
    }
    
    .inner-list-2 {
        width: 42%; /* Adjust the spacing between lists */
        max-width: 700px;
        margin-right: auto;
    }
    
    .img-gallery {
        display: flex;
        justify-content: center;
        list-style-type: none;
        padding: 0;
    }
    
    .gallery-button-container {
        margin-top: auto;
        margin-bottom: auto;
    }
    
    .gallery-button {
        background-color: rgba(0, 0, 0, 0);
        color: #46166B;
        font-size: 42px;
        text-align: center;
        border: none;
        transition-duration: 0.5s;
    }
    
    .gallery-button:hover {
        color: #ffc107;
        cursor: pointer;
        opacity: 1;
    }
    
    .gallery-button-large {
        background-color: rgba(0, 0, 0, 0);
        color: #46166B;
        font-size: 72px;
        text-align: center;
        border: none;
        transition-duration: 0.5s;
    }
    
    .gallery-button-large:hover {
        color: #ffc107;
        cursor: pointer;
        opacity: 1;
    }
    
    .gallery-img-container {
        width: 575px;
    }
    
    .text-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1px;
        list-style-type: none; /* Remove bullet points */
        padding: 8px 0 0 0;
        height: 150px;
    }
    
    .text-item {
        flex: 1;
        height: 150px;
        padding: 0 6px 0 6px;
    }
    
    .current-img {
        width: 100px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .current-wx {
        height: 16px; 
        font-size: 18px; 
        text-align: center; 
        margin-top: 5px;
    }
    
    .mohawk-img {
        height: auto; 
        width: auto; 
        max-height: 300px;
        display: block;
        margin: 0 auto;
    }
    
    .mohawk-text {
        height: 16px; 
        font-size: 18px; 
        text-align: center;
        margin-top: 10px;
        color: #46166B;
        font-weight: 300;
    }
    
    .wx-data {
        font-size: 55px; 
        font-weight: 900; 
        margin-top: 1px;
        margin-bottom: 1px;
    }
    
    .wx-data-2 {
        font-size: 35px; 
        margin-top: 1px;
    }
    
    .wind-text {
        font-size: 18px; 
        text-align: center;
    }
    
    .pres-data {
        font-size: 35px; 
        margin-top: 1px; 
        margin-bottom: 1px;
    }
    /*end of current conditions section/start of forecast section*/
    .forecast-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        list-style-type: none; /* Remove bullet points */
        padding: 0;
        background-color: #eee;
    }
    
    .forecast-item {
        display: inline-block;
        text-align: center;
        flex: 1;
        margin: 0 10px;
        max-width: 375px;
    }
    
    .forecast-time {
        height: 16px;
        font-size: 18px;
        font-weight: 300;
    }
    
    .forecast-img {
        width: 150px;
        height: auto;
        display: block;
        margin: 0 auto;
    }
    
    .forecast-string {
        height: 45px;
    }
    
    .vl {
        border-left: 1px solid black;
        height: 275px;
    }
    
    .padding {
        height: 80px;
    }
    
    /*.real-time-product-title {
        color: #46166B;
        text-align: center;
        margin-top: 8px; 
        margin-bottom: 16px;
    }*/
    /*start of visualization products section*/
    .real-time-product-title {
        font-family: ualbFont;
        color: #46166B;
        font-size: 3em; 
        font-weight: bold;
        text-align: center;
        margin: auto;
    }
    
    .real-time-product-title:link, .real-time-product-title:visited, .real-time-product-title:active {
        color: #46166B; 
        background-color: transparent; 
        text-decoration: none;
    }
    
    .real-time-product-title:hover {
        text-decoration: underline;
    }
    
    .ab-gallery {
        width: 60%;
    }
    
    .gfs-img {
        display: block;
        margin: auto;
        width: 90%;
    }

    .legacy-background {
        display: flex;
        margin: auto;
        flex-direction: row;
        list-style-type: none;
        padding: 0;
    }
    
    .legacy-products {
        margin: auto;
        width: 45%;
        height: 15vh;
        min-height: 100px;
        max-height: 200px;
        position: relative;
        min-width: 500px;
        max-width: 1200px;
        align-items: center;
    }
        
    .legacy-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
    }
    
    .legacy-products-text {
        font-family: ualbFont;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        background: rgba(0,0,0,0.8);
        text-decoration: none;
        transition-duration: 0.5s;
        font-size: 36px;
        font-weight: bolder;
        width: 100%;
        height: 100%;
        position: absolute;
        top: -2px;
        left: -2px;
        border: 2px solid white;
    }

    .legacy-products-text:hover {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #46166B;
        background: rgba(255,255,255,0.95);
        text-decoration: none;
        font-size: 36px;
        font-weight: bolder;
        width: 100%;
        height: 101%;
        position: absolute;
        top: -2px;
        left: -2px;
        border: 2px solid #46166B;
        cursor: pointer;
    }
    
    @media (min-width: 1500px) {
    
        .forecast-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            list-style-type: none; /* Remove bullet points */
            background-color: #eee;
        }
        
        .header-img {
                max-height: 100%;
                width: 100vw;
        }
        
        .header-text-container {
            left: 7%;
        }
    }
    
    @media (max-width: 1100px) {
    
        .header-img {
            position: relative;
            height: auto;
            width: auto;
            max-width: none;
            max-height: 450px;
        }
        
        .header-text-container {
            max-width: 40%;
        }
        
        .current-conditions-container { 
            list-style-type: none;
            padding: 0;
            height: auto;
            max-height: 1000px;
        }
        
        .section-title-container {
            margin-left: auto;
            margin-right: auto;
            max-width: 1500px;
        }
        
        .current-conditions-container-list {
            display: flex;
            background-image: url('images/cloud.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            flex-direction: column;
            list-style-type: none;
            padding: 0;
        }
            
        .inner-list-1 {
            width: 100%; /* Adjust the spacing between lists */
            margin: auto;
        }
        
        .inner-list-2 {
            width: 100%; /* Adjust the spacing between lists */
            justify-content: center;
            margin-top: 50px;
            margin-left: auto;
            margin-righ: auto;
        }
        
        .text-item {
            text-align: center;
            flex: 1;
            height: 150px;
        }  
            
        .mohawk-img {
            height: auto; 
            width: auto; 
            max-height: 50vh;
            max-width: 90%;
            display: block;
            margin: 0 auto;
            font-weight: bolder;
        }
        
        .forecast-img {
            width: 125px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
            
        .forecast-string {
            height: 60px;
        }
            
        .ab-gallery {
            width: 90%;
        }
    }
    </style>
    <script>
        // Reload the webpage every 5 minutes (300,000 milliseconds)
        setInterval(function() {
            window.location.reload(true);
        }, 300000);
    </script>
</head>
<body>
<div style="height: 134px;"></div>
<a class="nav-bar-logos" href="https://www.albany.edu/" id="albanyLink">
    <img style="height: 57px; margin-bottom: 6px;" src="images/nav/minerva.png" id="minerva">
    <img style="height: 57px; margin-top: 14px;" src="images/nav/UAlbanymark.png" id="albanyLogo">
</a>
<div class="nav-bar" id="navBar">
    <div style="padding: 20px 0 5px; display: grid; height: 75px;"></div>
    <nav>
        <ul class="nav-bar-list" id="nav-bar-dropdown">
            <li style="width: 30px;">
                <button id="dropdown-button" class="nav-button">
                    <img id="dropdown-button-img" src="images/nav/l_down_arrow.png" style="width: 16px;">
                </button>
            </li>
            <li id="dropdownSelect1" class="nav-item">
                <a href="https://www.albany.edu/daes" class="dropdown-link">
                    <div class="nav-item-select">
                        <p class="nav-item-text">DAES Home</p>
                    </div>
                </a>
                <ul id="dropdown1" class="dropdown-list">
                    <a href="https://www.albany.edu/daes/programs" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Degree Programs</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/daes/daes-research" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Research</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/daes/faculty" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">People</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/daes/tutoring" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Student Resources</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/daes/department-office" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Department Office</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/daes/inclusion-and-diversity" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Inclusion and Diversity</p>
                        </li>
                    </a>
                </ul>
            </li>
            <li id="dropdownSelect2" class="nav-item">
                <a href="https://www.atmos.albany.edu/student/rolsen/sample.php" class="dropdown-link">
                    <div class="nav-item-select">
                        <p class="nav-item-text">Weather Data & Forecasts</p>
                    </div>
                <a/>
                <ul id="dropdown2" class="dropdown-list">
                    <a href="https://www.atmos.albany.edu/student/rolsen/products.html" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">UAlbany DAES Products</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/index.php?d=wx_data" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">UAlbany DAES Legacy Products</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/student/abentley/realtime.html" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Alicia M. Bentley's GFS Maps</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/student/mbarletta/4Panel/4Panel.php" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Michael Barletta's WxEdge</p>
                        </li>
                    </a>
                </ul>
            </li>
            <li id="dropdownSelect3" class="nav-item">
                <a href="https://www.nysmesonet.org/" class="dropdown-link">
                    <div class="nav-item-select">
                        <p class="nav-item-text">NYS Mesonet</p>
                    </div>
                <a/>
                <ul id="dropdown3" class="dropdown-list">
                    <a href="https://www.nysmesonet.org/weather/local#network=nysm&stid=voor" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Current and Forecast Weather</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/maps" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Maps</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/meteogram#network=nysm&stid=voor" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Meteograms</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/cameras#controls=true&duration=2h&layout=1x1&sites=nysm%3Avoor" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Camera Viewer</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/today_stats" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Todays's Stats</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/winter" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Winter Products</p>
                        </li>
                    </a>
                    <a href="https://www.nysmesonet.org/weather/requestdata" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Request Data</p>
                        </li>
                    </a>
                </ul>
            </li>
            <li id="dropdownSelect4" class="nav-item">
                <a href="https://www.atmos.albany.edu/index.php?d=wx_contest" class="dropdown-link">
                    <div class="nav-item-select">
                        <p class="nav-item-text">Forecast Contests</p>
                    </div>
                </a>
                <ul id="dropdown4" class="dropdown-list">
                    <a href="https://www.atmos.albany.edu/index.php?d=contest_alb" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Albany Forecast Contest</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/index.php?d=contest_trw" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">National Thunderstorm Forecast Contest</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/facstaff/ralazear/fcst/fcst_alb.html" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Albany Forecast Products</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/daes/contests/fgamerules.pdf" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Contest Rules</p>
                        </li>
                    </a>
                    <a href="https://www.wxchallenge.com/" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Current Wx Challenge</p>
                        </li>
                    </a>
                    <a href="https://www.atmos.albany.edu/facstaff/ralazear/fcst/wxch.html" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">WxChallenge City Forecast Products</p>
                        </li>
                    </a>
                </ul>
            </li>
            <li id="dropdownSelect5" class="nav-item">
                <a href="https://www.albany.edu/asrc" class="dropdown-link">
                    <div class="nav-item-select">
                        <p class="nav-item-text">ASRC</p>
                    </div>
                <a/>
                <ul id="dropdown5" class="dropdown-list">
                    <a href="https://www.albany.edu/asrc/xcite-laboratory" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">xCITE Lab</p>
                        </li>
                    </a>
                    <a href="https://whiteface.asrc.albany.edu/" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Whiteface Mountain Field Station</p>
                        </li>
                    </a>
                    <a href="https://weatheranalytics.org/" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">Center of Excellence in Weather & Climate Analytics</p>
                        </li>
                    </a>
                    <a href="https://www.albany.edu/state-weather-risk-communication-center" class="dropdown-link">
                        <li class="dropdown-item">
                            <p class="dropdown-text">State Weather Risk Communictaion Center</p>
                        </li>
                    </a>
                </ul>
            </li>
            <li id="dropdownSelect6" class="nav-item">
                <a class="dropdown-link">
                    <div class="nav-item-select-blank">
                        <p class="nav-item-text"></p>
                    </div>
                <a/>
                <ul id="dropdown6" class="dropdown-list">
                    <a class="dropdown-link">
                        <li class="dropdown-item-blank">
                        </li>
                    </a>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<div class="header-container">
    <div class="header-img-container">
        <img class="header-img" src="images/balloon_crop.jpg">
        <div class="header-text-container">
            <h1 class="header-title">Albany Weather Data and Forecasts</h1>
            <p class="header-body">
            A suite of weather and climate products produced by UAlbany students, faculty, and alumni.</p>
        </div>
    </div>
</div>
<ul class="current-conditions-container">
    <li class="section-title-container">
        <br>
        <h1 class="section-title" style="margin-bottom: 10px;">Current Conditions at Albany International Airport</h1>
    </li>
    <ul class="current-conditions-container-list">
        <li class="inner-list-1">
            <ul class="text-container">
                <li class="text-item">
                    <br>
                    <img class="current-img" src="images/current_wx.png" alt="Image 1" id="wxImg">
                    <p class="current-wx" id="wxText"></p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Temperature:</u>
                    <p class="wx-data" id="tmpF"></p>
                    <p class="wx-data-2" id="tmpC"></p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Dew Point:</u>
                    <p class="wx-data" id="dewpointF"></p>
                    <p class="wx-data-2" id="dewpointC"></p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Humididty:</u>
                    <p class="wx-data" id="RH"></p>
                </li>
            </ul>
            <br>
            <ul class="text-container">
                <li class="text-item">
                    <p class="wind-text" style="margin-bottom: 0px;"><u>Wind:</u></p>
                    <img class="current-img" src="images/transparent_barb.png" alt="Image 1">
                    <p class="wind-text" style="margin-top: 0px;">
                        <?php
                        $myFile = "metar.txt";
                        $lines = file($myFile);//file in to an array
                        echo $lines[5];
                        ?>
                    </p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Station Pressure:</u>
                    <p class="wx-data" style="font-weight: bolder;" id="presHg"></p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Sea-Level Pressure:</u>
                    <p class="pres-data" style="font-weight: bolder;" id="slphPa"></p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Visibility:</u>
                    <p class="pres-data" style="font-weight: bolder;" id="vis"></p>
                </li>
            </ul> 
        </li>
        <li class="inner-list-2">
            <ul class="img-gallery">
                <li class="gallery-button-container" style="margin-left: auto;">
                    <button class="gallery-button" id="prevButton">&#8249;</button>
                </li>
                <li class="gallery-img-container">
                    <br>
                    <a id="changingLink" href="https://www.atmos.albany.edu/facstaff/ralazear/mohawk/mohawk_2d.html" 
                    target="_blank">
                        <img id="changingImage" src="images/now.jpg" alt="Image 1" class="mohawk-img"}>
                    </a>
                    <p id="changingText" class="mohawk-text">Latest Mohawk Tower Image</p>
                </li>
                <li class="gallery-button-container" style="margin-right: auto;">
                    <button class="gallery-button" id="nextButton">&#8250;</button>
                </li>
            </ul>
        </li>
    </ul>
</ul>
<div class="padding"></div>
<div class="section-title-container">
    <a class="section-title-link" href="https://www.weather.gov/aly/">NWS Albany 2-Day Forecast</a>
</div>
<ul class="forecast-container">
    <li class="forecast-item">
        <p class="forecast-time" id="date1"></p>
        <img class="forecast-img" src="images/im1.png" alt="Image 1" id="img1">
        <p class="forecast-string" id="wx1"></p>
        <p id="temp1" style="font-size: 22px;"></p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time" id="date2"></p>
        <img class="forecast-img" src="images/im2.png" alt="Image 2" id="img2">
        <p class="forecast-string" id="wx2"></p>
        <p id="temp2" style="font-size: 22px;"></p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time" id="date3"></p>
        <img class="forecast-img" src="images/im3.png" alt="Image 3" id="img3">
        <p class="forecast-string" id="wx3"></p>
        <p id="temp3" style="font-size: 22px;"></p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time" id="date4"></p>
        <img class="forecast-img" src="images/im4.png" alt="Image 4" id="img4">
        <p class="forecast-string" id="wx4"></p>
        <p id="temp4" style="font-size: 22px;"></p>
    </li>
</ul>
<div class="padding"></div>
<div id="main" style="width: 96%; height: 400px; margin: auto;"></div>
<script type="module">
    import * as d3 from "https://cdn.jsdelivr.net/npm/d3@7/+esm";
    
    const data = await d3.csv("ALB_obs_fore.csv");
    var length = data.length;
    console.log(data);
    
    var times = [];
    var temps = [];
    var dewps = [];
    var wdsps = [];
    var wdchs = [];

    for(let i = 0; i < length; i++) {
        let time = data[i].Time;
        //time = time.substring(0, time.length -12);
        times.push(time);
        
        let temp = data[i].T;
        temps.push(temp);
        
        let dewp = data[i].Td;
        dewps.push(dewp);

        let wdsp = data[i].Wind;
        wdsps.push(wdsp);

        let wdch = data[i].WindChill;
        wdchs.push(wdch);
    }
    console.log(temps);
    console.log(times);
    console.log(wdsps);
    console.log(wdchs);

    var chartDom = document.getElementById('main');
    var myChart = echarts.init(chartDom);
    var option;
    
    option = {
      title: {
        text: 'ALB 24-hour Observations and 24-hour Hour Forecast'
      },
      tooltip: {
        trigger: 'axis'
      },
      legend: {
        data: ['Temperature', 'Dewpoint', 'Wind Chill']
      },
      grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
      },
      toolbox: {
        feature: {
          saveAsImage: {}
        }
      },
      xAxis: {
        type: 'category',
        data: times,
      },
      yAxis: {
        type: 'value',
      },
      series: [
        {
          name: 'Temperature',
          data: temps,
          type: 'line'
        },
        
        {
          name: 'Dewpoint',
          data: dewps,
          type: 'line'
        },

        {
          name: 'Wind Chill',
          data: wdchs,
          type: 'line'
        }
      ]
    };
    
    option && myChart.setOption(option);
</script>
<div class="padding"></div>
<div style="width: 100%; height: auto; text-align: center; padding-bottom: 16px;">
    <a class="real-time-product-title" 
    href="https://www.atmos.albany.edu/student/abentley/realtime.html">Alicia M. Bentley&#8217;s Real-time GFS Maps</a> 
</div>
<ul class ="img-gallery" style="max-width: 2250px; margin: auto;">
    <li class="gallery-button-container" style="margin-right: 0;">
        <button id="abPrevButton" class="gallery-button-large">&#8249;</button>
    </li>
    <li class="ab-gallery">
        <a id="abLink" 
         href="https://www.atmos.albany.edu/student/abentley/realtime/standard.php?domain=northamer&variable=850_thetae">
            <img id="abImage" src="images/abentley/850_thetae_57.png" alt="Image 1" class="gfs-img"}>
        </a>
    </li>
    <li class="gallery-button-container" style="margin-left: 0;">
        <button id="abNextButton" class="gallery-button-large">&#8250;</button>
    </li>
</ul>
<div class="padding"></div>
<div class="padding"></div>
<ul class="legacy-background">
    <li class="legacy-products">
        <a href="https://www.atmos.albany.edu/index.php?d=wx_data" target="_blank">
        <img id="darkImage" src="images/mos_crop.png" class="legacy-img">
        <img id="lightImage" src="images/mos_crop_r.png" class="legacy-img">
        <div id="legacyButton" class="legacy-products-text">
            <p style="text-align: center;">UAlbany DAES Legacy Products</p>
        </div>
        </a>
    </li>
</ul>
<div class="padding">
<script>
//current conditions image gallery
    const contentTriplets = [
        {image: "images/now.jpg", link: "https://www.atmos.albany.edu/facstaff/ralazear/mohawk/mohawk_2d.html",
         text: "Latest Mohawk Tower Image"},
        {image: "images/latest.gif", link: "https://www.atmos.albany.edu/weather/radar/base/ENX/animate.html",
         text: "Latest KENX Radar Image"},
        {image: "images/00latestvis-nd.gif", link: "https://www.atmos.albany.edu/weather/satellite/ne/00latestvis-nd.gif",
         text: "Latest Visible Satellite Image"},
        {image: "images/stationmap.png", link: "http://www.nysmesonet.org", text: "Latest NYS Mesonet Observations"}
    ]; //image, text and, link for gallery

    let currentIndex = 0;

    function changeContent() {
        const imgElement = document.getElementById('changingImage');
        const linkElement = document.getElementById('changingLink');
        const textElement = document.getElementById('changingText');

        const currentTriplet = contentTriplets[currentIndex];
        imgElement.src = currentTriplet.image;
        linkElement.href = currentTriplet.link;
        textElement.innerText = currentTriplet.text;
    }

    function showPrevImage() {
        currentIndex = (currentIndex - 1 + contentTriplets.length) % contentTriplets.length;
        changeContent();
    } //function to show the previous image in the gallery

    function showNextImage() {
        currentIndex = (currentIndex + 1) % contentTriplets.length;
        changeContent();
    } //function to show next image in the image gallery

    function setImgInterval() {
        galleryInterval = setInterval(showNextImage, 4000);
    } //interval to automatically switch out gallery images

    function clearImgInterval() {
        clearInterval(galleryInterval);
    } //function to clear interval (used when stopping gallery from changing on hover)

    document.getElementById('prevButton').addEventListener('click', showPrevImage); //next button
    document.getElementById('nextButton').addEventListener('click', showNextImage); //last button

    document.getElementById('changingImage').addEventListener('mouseover', clearImgInterval); //pasue interval when hovered
    document.getElementById('changingImage').addEventListener('mouseout', setImgInterval); //restart interval on mouse leave

    galleryInterval = setInterval(showNextImage, 4000); //runs next img function every 4 seconds
</script>
<script>
//alicia bentley image gallery
    const imgLinkPair = [
        {image: "images/abentley/850_thetae_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/standard.php?domain=northamer&variable=850_thetae"},
        {image: "images/abentley/6hprecip_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/standard.php?domain=northamer&variable=6hprecip"},
        {image: "images/abentley/IVT_conv_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/standard.php?domain=northamer&variable=IVT_conv"},
        {image: "images/abentley/rel_vort_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/standard.php?domain=northamer&variable=rel_vort"},
        {image: "images/abentley/irro_wind_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/subtrop.php?domain=northamer&variable=irro_wind"},
        {image: "images/abentley/330K_isen_57.png", 
         link: "https://www.atmos.albany.edu/student/abentley/realtime/subtrop.php?domain=northamer&variable=330K_isen"}
    ];

    let abCurrentIndex = 0;

    function abChangeContent() {
        const abImgElement = document.getElementById('abImage');
        const abLinkElement = document.getElementById('abLink');

        const currentPair = imgLinkPair[abCurrentIndex];
        abImgElement.src = currentPair.image;
        abLinkElement.href = currentPair.link;
    }

    function abShowPrevImage() {
        abCurrentIndex = (abCurrentIndex - 1 + imgLinkPair.length) % imgLinkPair.length;
        abChangeContent();
    }

    function abShowNextImage() {
        abCurrentIndex = (abCurrentIndex + 1) % imgLinkPair.length;
        abChangeContent();
    }

    document.getElementById('abPrevButton').addEventListener('click', abShowPrevImage);
    document.getElementById('abNextButton').addEventListener('click', abShowNextImage);

    setInterval(abShowNextImage, 3000);    
</script>
<script>
    // Add event listeners for hovering over legacy button
    const legacyButton = document.getElementById('legacyButton');
    const defaultImage = document.getElementById('darkImage');
    const hoveredImage = document.getElementById('lightImage');

    // Set transition property for opacity in CSS for a smoother fade effect
    darkImage.style.transition = 'opacity 0.5s ease-in-out';
    lightImage.style.transition = 'opacity 0.5s ease-in-out';

    // Set initial opacity values
    darkImage.style.opacity = 0;
    lightImage.style.opacity = 1;

    legacyButton.addEventListener('mouseover', function () {
        darkImage.style.opacity = 1;
        lightImage.style.opacity = 0;
    }); //switch opacity on hover

    legacyButton.addEventListener('mouseout', function () {
        darkImage.style.opacity = 0;
        lightImage.style.opacity = 1;
    }); //switch opacity back on mouse out
</script>
<script>
//Use NWS Api to get the forecast data, ignore the fact you spent a month wriitng python code to do this exact thing
const apiUrl = "https://api.weather.gov/points/42.68,-73.81"

// Make first request to the NWS API 
fetch(apiUrl, {
    headers: {
        'User-Agent': 'just testing this out (rwolsen@albany.edu)', // Replace with your app's user agent
        'Accept': 'application/geo+json'
    }
    })
    .then(response => response.json())
    .then(data => {
        // Extract forecast information from the response
        var forecastURL = data.properties.forecast;

        // Log the forecast to the console
        console.log(forecastURL);

        fetch(forecastURL, {
            headers: {
                'User-Agent': 'just testing this out (rwolsen@albany.edu)',
                'Accept': 'application/geo+json'
            }
            })
            .then(response => response.json())
            .then(data => {

                // Log the forecast to the console
                //console.log(forecastPeriod1);
                //console.log(forecastPeriod2);
                //console.log(forecastPeriod3);
                //console.log(forecastPeriod4);
                
                // Extract forecast information from the response
                var forecastPeriod1 = data.properties.periods[0];
                    var date1 = forecastPeriod1.name;
                    var img1 = forecastPeriod1.icon;
                    var wx1 = forecastPeriod1.shortForecast;
                    var day1 = forecastPeriod1.isDaytime;
                    if (day1 == true) {
                        var temp1 = "High: " + forecastPeriod1.temperature + "°F";
                    } else {
                        var temp1 = "Low: " + forecastPeriod1.temperature + "°F";
                    }
                var forecastPeriod2 = data.properties.periods[1];
                    var date2 = forecastPeriod2.name;
                    var img2 = forecastPeriod2.icon;
                    var wx2 = forecastPeriod2.shortForecast;
                    var day2 = forecastPeriod2.isDaytime;
                    if (day2 == true) {
                        var temp2 = "High: " + forecastPeriod2.temperature + "°F";
                    } else {
                        var temp2 = "Low: " + forecastPeriod2.temperature + "°F";
                    }
                var forecastPeriod3 = data.properties.periods[2];
                    var date3 = forecastPeriod3.name;
                    var img3 = forecastPeriod3.icon;
                    var wx3 = forecastPeriod3.shortForecast;
                    var day3 = forecastPeriod3.isDaytime;
                    if (day1 == true) {
                        var temp3 = "High: " + forecastPeriod3.temperature + "°F";
                    } else {
                        var temp3 = "Low: " + forecastPeriod3.temperature + "°F";
                    }
                var forecastPeriod4 = data.properties.periods[3];
                    var date4 = forecastPeriod4.name;
                    var img4 = forecastPeriod4.icon;
                    var wx4 = forecastPeriod4.shortForecast;
                    var day4 = forecastPeriod4.isDaytime;
                    if (day4 == true) {
                        var temp4 = "High: " + forecastPeriod4.temperature + "°F";
                    } else {
                        var temp4 = "Low: " + forecastPeriod4.temperature + "°F";
                    }

                //get all that stuff on the webpage
                document.getElementById("date1").innerHTML = date1;
                document.getElementById("wx1").innerHTML = wx1;
                document.getElementById("temp1").innerHTML = temp1;
                document.getElementById("img1").src = img1;

                document.getElementById("date2").innerHTML = date2;
                document.getElementById("wx2").innerHTML = wx2;
                document.getElementById("temp2").innerHTML = temp2;
                document.getElementById("img2").src = img2;

                document.getElementById("date3").innerHTML = date3;
                document.getElementById("wx3").innerHTML = wx3;
                document.getElementById("temp3").innerHTML = temp3;
                document.getElementById("img3").src = img3;

                document.getElementById("date4").innerHTML = date4;
                document.getElementById("wx4").innerHTML = wx4;
                document.getElementById("temp4").innerHTML = temp4;
                document.getElementById("img4").src = img4;

            })
            .catch(error => {
            console.error('Error fetching second JSON:', error);
        }); 
    })
    .catch(error => {
    console.error('Error fetching first JSON:', error);
}); 
</script>
<script>
//Use NWS Api to get the current data, spent a lot less time on this one so its less painful
const apiUrl2 = "https://api.weather.gov/stations/KALB/observations/latest"

// Make first request to the NWS API 
fetch(apiUrl2, {
    headers: {
        'User-Agent': 'just testing this out (rwolsen@albany.edu)', // Replace with your app's user agent
        'Accept': 'application/geo+json'
    }
    })
    .then(response => response.json())
    .then(data => {
        // Extract forecast information from the response
        const newAPI = data.properties;
        
        console.log(newAPI);

        var currentWx = newAPI.icon;
        document.getElementById("wxImg").src = currentWx;

        var wxText = newAPI.textDescription;
        document.getElementById("wxText").innerHTML = wxText;
        
        var tmpC = newAPI.temperature.value;
        var tmpF = tmpC * (9/5) + 32
        document.getElementById("tmpC").innerHTML = Math.round(tmpC) + "°C";
        document.getElementById("tmpF").innerHTML = Math.round(tmpF) + "°F";
        
        var dewpC = newAPI.dewpoint.value;
        var dewpF = dewpC * (9/5) + 32
        document.getElementById("dewpointC").innerHTML = Math.round(dewpC) + "°C";
        document.getElementById("dewpointF").innerHTML = Math.round(dewpF) + "°F";

        var RH = newAPI.relativeHumidity.value;
        document.getElementById("RH").innerHTML = Math.round(RH) + "%";
        
        var slpPa = newAPI.seaLevelPressure.value;
        if (slpPa != null) {
            var slphPa = slpPa * .01
            var slpHg = slpPa * .00029529983071445
            document.getElementById("slphPa").innerHTML = slphPa.toFixed(1) + "hPa";
        } else {
            document.getElementById("slphPa").innerHTML = "N/A";
        }

        var presPa = newAPI.barometricPressure.value;
        var presHg = presPa * .00029529983071445
        document.getElementById("presHg").innerHTML = presHg.toFixed(2) + '"';

        var visM = newAPI.visibility.value;
        var vis = visM * .0006213712
        document.getElementById("vis").innerHTML = vis.toFixed(2) + "mi";
    })
    .catch(error => {
    console.error('Error fetching first JSON:', error);
}); 
</script>
<script>
const dropdownSelect = document.getElementById("nav-bar-dropdown");
const dropdown = document.querySelectorAll(".dropdown-list");
let timeout;

// Add event listener to the dropdownSelect
dropdownSelect.addEventListener("mouseenter", function() {
    // Add delay to dropdown
    timeout = setTimeout(() => {
        // Use jQuery's slideDown to show the dropdown with a slide-down transition
        $(dropdown).slideDown(300);
    }, 500);
});

// Add event listener to the dropdownSelect
dropdownSelect.addEventListener("mouseleave", function() {
    // Cancel dropdown if mouseout before the delay is over
    clearTimeout(timeout);
    // Use jQuery's slideUp to hide the dropdown with a slide-up transition
    $(dropdown).slideUp(300);
});
</script>
<script>
//Change color of dropdown rows and dropdown items on hover
//dropdown1
const dropdownSelect1 = document.getElementById('dropdownSelect1');
const dropdown1 = document.getElementById('dropdown1');

// Change the background color of dropdown row 1 on hover
dropdownSelect1.addEventListener('mouseenter', function() {
    dropdownSelect1.style.backgroundColor = '#46166B';
    dropdown1.style.backgroundColor = '#46166B';
});

// Reset the color and background color of dropdown row 1 on mouse leave
dropdownSelect1.addEventListener('mouseleave', function() {
    dropdownSelect1.style.backgroundColor = '';
    dropdown1.style.backgroundColor = '';
});

//dropdown2
const dropdownSelect2 = document.getElementById('dropdownSelect2');
const dropdown2 = document.getElementById('dropdown2');

// Change the background color of dropdown row 2 on hover
dropdownSelect2.addEventListener('mouseenter', function() {
    dropdownSelect2.style.backgroundColor = '#46166B';
    dropdown2.style.backgroundColor = '#46166B';
});

// Reset the color and background color of dropdown row 2 on mouse leave
dropdownSelect2.addEventListener('mouseleave', function() {
    dropdownSelect2.style.backgroundColor = '';
    dropdown2.style.backgroundColor = '';
});

//dropdown3
const dropdownSelect3 = document.getElementById('dropdownSelect3');
const dropdown3 = document.getElementById('dropdown3');

// Change the background color of dropdown row 3 on hover
dropdownSelect3.addEventListener('mouseenter', function() {
    dropdownSelect3.style.backgroundColor = '#46166B';
    dropdown3.style.backgroundColor = '#46166B';
});

// Reset the color and background color of dropdown row 3 on mouse leave
dropdownSelect3.addEventListener('mouseleave', function() {
    dropdownSelect3.style.backgroundColor = '';
    dropdown3.style.backgroundColor = '';
});

//dropdown4
const dropdownSelect4 = document.getElementById('dropdownSelect4');
const dropdown4 = document.getElementById('dropdown4');

// Change the background color of dropdown row 4 on hover
dropdownSelect4.addEventListener('mouseenter', function() {
    dropdownSelect4.style.backgroundColor = '#46166B';
    dropdown4.style.backgroundColor = '#46166B';
});

// Reset the color and background color of dropdown row 4 on mouse leave
dropdownSelect4.addEventListener('mouseleave', function() {
    dropdownSelect4.style.backgroundColor = '';
    dropdown4.style.backgroundColor = '';
});

//dropdown5
const dropdownSelect5 = document.getElementById('dropdownSelect5');
const dropdown5 = document.getElementById('dropdown5');

// Change the background color of dropdown row 5 on hover
dropdownSelect5.addEventListener('mouseenter', function() {
    dropdownSelect5.style.backgroundColor = '#46166B';
    dropdown5.style.backgroundColor = '#46166B';
});

// Reset the color and background color of dropdown row 5 on mouse leave
dropdownSelect5.addEventListener('mouseleave', function() {
    dropdownSelect5.style.backgroundColor = '';
    dropdown5.style.backgroundColor = '';
});

// Get references to all elements with the class "dropdown-item"
const dropdownItems = document.querySelectorAll('.dropdown-item');

// Add event listeners to each item within each dropdown
dropdownItems.forEach(function(item) {
    item.addEventListener('mouseenter', function() {
        item.style.color = '#333';
        item.style.backgroundColor = '#ffc107';
        item.style.transitionDuration = '0.5s'
    });

    item.addEventListener('mouseleave', function() {
        item.style.color = '';
        item.style.backgroundColor = '';
        item.style.transitionDuration = '0.5s'
    });
});
</script>
<script>
// change arrow from dropdown
const dropdownButton = document.getElementById('dropdown-button');
const imageToChange = document.getElementById('dropdown-button-img');
var navBarDropdown = document.getElementById('nav-bar-dropdown');

// Define the image source URL's
const d_upImageSrc = 'images/nav/d_up_arrow.png';
const l_upImageSrc = 'images/nav/l_up_arrow.png';
const l_downImageSrc = 'images/nav/l_down_arrow.png';

// Change the button to light and up when hovered
navBarDropdown.addEventListener('mouseenter', function() {
    imageToChange.src = l_upImageSrc;
});

// Change button to dark and down when leave
navBarDropdown.addEventListener('mouseleave', function() {
    imageToChange.src = l_downImageSrc;
});
    
// Change the button to dark and up when hovered
dropdownButton.addEventListener('mouseenter', function() {
    imageToChange.src = d_upImageSrc;
});

// Change button to dark and down when leave
dropdownButton.addEventListener('mouseleave', function() {
    imageToChange.src = l_upImageSrc;
});
</script>
<script>
//make the nav bar little when page is scrolled
var navBar = document.getElementById('navBar');
const albanyLink = document.getElementById('albanyLink');
const minerva = document.getElementById('minerva');
const albanyLogo = document.getElementById('albanyLogo');
    
    
// Function to be executed when the page is scrolled down a certain amount
function isScrolled() {
    //minimize navbar when scrolled down
    navBar.style.height = '40px';
    navBarDropdown.style.top = '7px';
    navBarDropdown.style.left = '85px';
    albanyLink.style.width = '59px';
    albanyLogo.style.display = 'none';
    minerva.style.margin = '4px 0 0 4px';
}

function isntScrolled() {
    //bring back full nav bar if scrolled up
    navBar.style.height = '';
    navBarDropdown.style.top = '';
    navBarDropdown.style.left = '';
    albanyLink.style.width = '';
    albanyLogo.style.display = '';
}

// Set the scroll threshold (in pixels) for when the function should be executed
var scrollThreshold = 134;

// Event listener for the scroll event
window.onscroll = function() {
    // Check if the page has been scrolled down by the specified threshold
    if (window.scrollY > scrollThreshold) {
        // Call the function when the scroll threshold is reached
        isScrolled();
    } else { 
        isntScrolled();
    }
};
</script>
</body>
</html>