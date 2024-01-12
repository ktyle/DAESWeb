<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UAlbany Weather Data and Forecasts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .nav-bar {
            background-color: #333;
            height: 134px;
        }

        .section-title {
            color: #46166B;
            font-size: 2em;
            //margin-top: 20px; 
            //margin-bottom: 20px;
            //margin-left: auto;
            padding-left: 20px;
        }
        
        a:link, a:visited, a:active {
            color: #46166B; 
            background-color: transparent; 
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }

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
            width: 50%; /* Adjust the spacing between lists */
            max-width: 750px;
            margin-left: auto;
        }
        
        .inner-list-2 {
            width: 50%; /* Adjust the spacing between lists */
            max-width: 750px;
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
            padding: 0;
            height: 150px;
        }
        
        .text-item {
            flex: 1;
            height: 150px;     
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
            font-weight: bolder;
        }
        
        .wx-data {
            font-size: 55px; 
            font-weight: bolder; 
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
            font-weight: bolder;
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

        .real-time-product-title {
            color: #46166B;
            text-align: center;
            margin-top: 8px; 
            margin-bottom: 16px;
        }

        .gfs-img {
            display: block;
            margin: auto;
            width: 90%;
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
        }

        @media (max-width: 950px) {
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
<ul class="current-conditions-container">
    <li class="section-title-container">
        <br>
        <h1 class="section-title">Current Conditions at Albany International Airport</h1>
    </li>
    <ul class="current-conditions-container-list">
        <li class="inner-list-1">
            <ul class="text-container">
                <li class="text-item">
                    <br>
                    <img class="current-img" src="images/current_wx.png" alt="Image 1">
                    <p class="current-wx">
                        <?php
                        $myFile = "metar.txt";
                        $lines = file($myFile);//file in to an array
                        echo $lines[0];
                        ?>
                    </p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Temperature:</u>
                    <p class="wx-data">
                        <?php
                        echo $lines[1];
                        ?>
                    </p>
                    <p class="wx-data-2">
                        <?php
                        echo $lines[2];
                        ?>
                    </p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Dew Point:</u>
                    <p class="wx-data">
                        <?php
                        echo $lines[3];
                        ?>
                    </p>
                    <p class="wx-data-2">
                        <?php
                        echo $lines[4];
                        ?>
                    </p>
                </li>
            </ul>
            <br>
            <ul class="text-container">
                <li class="text-item">
                    <p class="wind-text" style="margin-bottom: 0px;"><u>Wind:</u></p>
                    <img class="current-img" src="images/transparent_barb.png" alt="Image 1">
                    <p class="wind-text" style="margin-top: 0px;">
                        <?php
                        echo $lines[5];
                        ?>
                    </p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Humididty:</u>
                    <p class="wx-data">
                        <?php
                        echo $lines[6];
                        ?>
                    </p>
                </li>
                <li class="text-item">
                    <br>
                    <u style="font-size: 18px;">Sea-level Pressure:</u>
                    <p class="pres-data" style="font-weight: bolder;">
                        <?php
                        echo $lines[7];
                        ?>
                    </p>
                    <p class="pres-data">
                        <?php
                        echo $lines[8];
                        ?>
                    </p>
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
    <h1 class="section-title">
        <a href="https://www.weather.gov/aly/">NWS Albany 2-Day Forecast</a>
    </h1>
</div>
<ul class="forecast-container">
    <li class="forecast-item">
        <p class="forecast-time">
            <?php
            $myFile = "forecast.txt";
            $lines = file($myFile);//Day
            echo $lines[0];
            ?>
        </p>
        <img class="forecast-img" src="images/im1.png" alt="Image 1">
        <p class="forecast-string">
            <?php
            echo $lines[2]; //Weather string
            ?>
        </p>
        <p style="font-size: 22px;">
            <?php
            echo $lines[1]; //High/Low Temp
            ?>
        </p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time">
            <?php
            echo $lines[4]; //Day
            ?>
        </p>
        <img class="forecast-img" src="images/im2.png" alt="Image 2">
        <p class="forecast-string">
            <?php
            echo $lines[6]; //Weather string
            ?>
        </p>
        <p style="font-size: 22px;">
            <?php
            echo $lines[5]; //High/Low Temp
            ?>
        </p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time">
            <?php
            echo $lines[8]; //Day
            ?>
        </p>
        <img class="forecast-img" src="images/im3.png" alt="Image 3">
        <p class="forecast-string">
            <?php
            echo $lines[10]; //Weather string
            ?>
        </p>
        <p style="font-size: 22px;">
            <?php
            echo $lines[9]; //High/Low Temp
            ?>
        </p>
    </li>
    <li class="vl"></li>
    <li class="forecast-item">
        <p class="forecast-time">
            <?php
            echo $lines[12]; //line 2
            ?>
        </p>
        <img class="forecast-img" src="images/im4.png" alt="Image 4">
        <p class="forecast-string">
            <?php
            echo $lines[14]; //Weather string
            ?>
        </p>
        <p style="font-size: 22px;">
            <?php
            echo $lines[13]; //High/Low Temp
            ?>
        </p>
    </li>
</ul>
<div class="padding"></div>
<h1 class="real-time-product-title">
    <a href="https://www.atmos.albany.edu/student/abentley/realtime.html">Alicia M. Bentley&#8217;s Real-time GFS Maps</a> 
</h1>
<ul class ="img-gallery">
    <li class="gallery-button-container" style="margin-right: 0;">
        <button id="abPrevButton" class="gallery-button-large">&#8249;</button>
    </li>
    <li style="width: 60%">
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
<script>
    const contentTriplets = [
        {image: "images/now.jpg", link: "https://www.atmos.albany.edu/facstaff/ralazear/mohawk/mohawk_2d.html",
         text: "Latest Mohawk Tower Image"},
        {image: "images/latest.gif", link: "https://www.atmos.albany.edu/weather/radar/base/ENX/animate.html",
         text: "Latest KENX Radar Image"},
        {image: "images/00latestvis-nd.gif", link: "https://www.atmos.albany.edu/weather/satellite/ne/00latestvis-nd.gif",
         text: "Latest Visible Satellite Image"},
        {image: "images/stationmap.png", link: "http://www.nysmesonet.org", text: "Latest NYS Mesonet Observations"}
    ];

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
    }

    function showNextImage() {
        currentIndex = (currentIndex + 1) % contentTriplets.length;
        changeContent();
    }

    document.getElementById('prevButton').addEventListener('click', showPrevImage);
    document.getElementById('nextButton').addEventListener('click', showNextImage);

    setInterval(showNextImage, 5000);
</script>
<script>
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
</body>
</html>