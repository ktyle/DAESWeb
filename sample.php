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

        .section-title {
            color: #46166B;
            text-indent: 30px;
            margin-top: 8px; 
            margin-bottom: -5px;
        }
        
        a:link, a:visited, a:active {
            color: #46166B; 
            background-color: transparent; 
            text-decoration: none;
        }
        
        a:hover {
            color: #46166B; 
            background-color: transparent; 
            text-decoration: underline;
        }
        
        .container-list {
            display: flex;
            flex-direction: row; 
            list-style-type: none;
            padding: 0;
            background-image: url('images/cloud.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            height: auto;
            max-height: 400px;
        }

        .inner-list-1 {
            width: 50%; /* Adjust the spacing between lists */
            /*background-color: blue;*/
        }
        
        .inner-list-2 {
            width: 50%; /* Adjust the spacing between lists */
        }
        
        .text-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1px;
            list-style-type: none; /* Remove bullet points */
            height: 150px;
        }
        
        .text-item {
            /*text-align: center;*/
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
            padding: 5px;
            list-style-type: none; /* Remove bullet points */
            background-color: #eee;
        }

        .forecast-item {
            display: inline-block;
            text-align: center;
            flex: 1;
            margin: 0 10px;
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
        
        .vl {
            border-left: 1px solid black;
            height: 275px;
        }
        
        @media (max-width: 800px) {
        .container-list {
            display: flex;
            flex-direction: column;
            list-style-type: none;
            padding: 0;
            background-image: url('images/cloud.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            height: auto;
            max-height: 775px;
          }
            
        .inner-list-1 {
            width: 100%; /* Adjust the spacing between lists */
          }
        
        .inner-list-2 {
            width: 100%; /* Adjust the spacing between lists */
            justify-content: center;
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
<h1 class="section-title">Current Conditions at Albany Internatinal Airport (ALB)</h1>
<ul class="container-list">
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
                <p class="wind-text" style="margin-bottom: -10px;"><u>Wind:</u></p>
                <img class="current-img" src="images/transparent_barb.png" alt="Image 1">
                <p class="wind-text" style="margin-top: -10px;">
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
        <div>
            <br>
            <a id="changingLink" href="https://www.atmos.albany.edu/facstaff/ralazear/mohawk/mohawk_2d.html" target="_blank">
                <img id="changingImage" src="images/now.jpg" alt="Image 1" class="mohawk-img"}>
            </a>
            <p id="changingText" class="mohawk-text">Latest Mohawk Tower Image</p>
        </div>
    </li>
</ul>
<h1 class="section-title">
    <a href="https://www.weather.gov/aly/">NWS Albany 2-Day Forecast</a>
</h1>
    <ul class="forecast-container">
        <li class="forecast-item">
            <p class="forecast-time">
                <?php
                $myFile = "forecast.txt";
                $lines = file($myFile);//file in to an array
                echo $lines[0];
                ?>
            </p>
            <img class="forecast-img" src="images/im1.png" alt="Image 1">
            <p style="height: 45px;">
                <?php
                echo $lines[2];
                ?>
            </p>
            <p style="font-size: 22px;">
                <?php
                echo $lines[1];
                ?>
            </p>
        </li>
        <li class="vl"></li>
        <li class="forecast-item">
            <p class="forecast-time">
                <?php
                echo $lines[4];
                ?>
            </p>
            <img class="forecast-img" src="images/im2.png" alt="Image 2">
            <p style="height: 45px;">
                <?php
                echo $lines[6];
                ?>
            </p>
            <p style="font-size: 22px;">
                <?php
                echo $lines[5];
                ?>
            </p>
        </li>
        <li class="vl"></li>
        <li class="forecast-item">
            <p class="forecast-time">
                <?php
                echo $lines[8];
                ?>
            </p>
            <img class="forecast-img" src="images/im3.png" alt="Image 3">
            <p style="height: 45px;">
                <?php
                echo $lines[10];
                ?>
            </p>
            <p style="font-size: 22px;">
                <?php
                echo $lines[9];
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
            <p style="height: 45px;">
                <?php
                echo $lines[14]; //line 2
                ?>
            </p>
            <p style="font-size: 22px;">
                <?php
                echo $lines[13]; //line 2
                ?>
            </p>
        </li>
    </ul>
    <script>
        // List of image and link pairs
        const contentTriplets = [
            { image: "images/now.jpg", link: "https://www.atmos.albany.edu/facstaff/ralazear/mohawk/mohawk_2d.html",
              text: "Latest Mohawk Tower Image" },
            { image: "images/latest.gif", link: "https://www.atmos.albany.edu/weather/radar/base/ENX/animate.html",
              text: "Latest KENX Radar Image" },
            { image: "images/00latestvis-nd.gif", link: "https://www.atmos.albany.edu/weather/satellite/ne/00latestvis-nd.gif",
              text: "Latest Visible Satellite Image" },
            { image: "images/stationmap.png", link: "http://www.nysmesonet.org",
              text: "Latest NYS Mesonet Observations" }
            // Add more image and link pairs as needed
        ];

        // Index to keep track of the current pair
        let currentIndex = 0;

        // Function to change the image and link
        function changeContent() {
            const imgElement = document.getElementById('changingImage');
            const linkElement = document.getElementById('changingLink');
            const textElement = document.getElementById('changingText');

            const currentTriplet = contentTriplets[currentIndex];
            imgElement.src = currentTriplet.image;
            linkElement.href = currentTriplet.link;
            textElement.innerText = currentTriplet.text;

            // Increment the index or reset to 0 if it reaches the end
            currentIndex = (currentIndex + 1) % contentTriplets.length;
        }

        // Set an interval to change the image and link every 5 seconds (5000 milliseconds)
        setInterval(changeContent, 4000);
    </script>
</body>
</html>