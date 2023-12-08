#!/usr/bin/env python
# coding: utf-8

# ## Checks NDFD data for NaN values in data
# ---

# In[ ]:


import numpy as np
from datetime import datetime, timedelta
import xarray as xr
import pandas as pd
import matplotlib.pyplot as plt
import cartopy.crs as ccrs
import cartopy.feature as cfeature
import metpy
from pyproj import Proj


# #### Get time rounded down to half hour

# In[ ]:


def rounded_to_the_last_30_minute():
    now = datetime.now()
    rounded = now - (now - datetime.min) % timedelta(minutes=30)
    return rounded


# In[ ]:


date = rounded_to_the_last_30_minute()
date


# #### Import the data

# In[ ]:


YYYYMMDD_HHMM = date.strftime('%Y%m%d_%H%M')
YYYYMMDD_HHMM


# In[ ]:


File = "https://thredds.ucar.edu/thredds/dodsC/grib/NCEP/NDFD/NWS/CONUS/CONDUIT/NDFD_NWS_CONUS_conduit_2p5km_"+YYYYMMDD_HHMM+".grib2"
File


# In[ ]:


ds = xr.open_dataset(File)


# In[ ]:


ds = ds.metpy.parse_cf()
ds = ds.metpy.assign_latitude_longitude(force=False)
ds


# In[ ]:


x, y = ds.x, ds.y


# #### Define function to get gridpoint nearest to a lat and lon

# In[ ]:


def find_closest(array, value):
    idx = (np.abs(array-value)).argmin()
    return idx


# In[ ]:


max_temp = ds.Maximum_temperature_height_above_ground_12_Hour_Maximum


# #### Define map projection & ETEC gridpoint

# In[ ]:


proj_data = max_temp.metpy.cartopy_crs
proj_data;

pFull = Proj(proj_data)


# In[ ]:


siteName = "ETEC"
siteLat, siteLon = (42.68, -73.81) #lat & lon of gridpoint over ETEC
siteX, siteY = pFull(siteLon, siteLat)
siteXidx, siteYidx = find_closest(x, siteX), find_closest(y, siteY)


# #### Define max temp Variable

# In[ ]:


forecastMax = max_temp.isel(x = siteXidx, y = siteYidx).isel()

timeDimMax, vertDimMax = forecastMax.metpy.time.name, forecastMax.metpy.vertical.name

idxTimeMax = slice(None, 2) # First time
idxVertMax = 0 # First (and in this case, only) vertical level

timeDictMax = {timeDimMax: idxTimeMax}
vertDictMax = {vertDimMax: idxVertMax}

forecastMax = forecastMax.isel(vertDictMax).isel(timeDictMax)


# #### Define wind speed variable

# In[ ]:


windSpeed = ds.Wind_speed_height_above_ground
windSpeed = windSpeed.isel(x = siteXidx, y = siteYidx).isel()

timeDimWdsp, vertDimWdsp = windSpeed.metpy.time.name, windSpeed.metpy.vertical.name

idxVertWdsp = 0 # First (and in this case, only) vertical level
vertDictWdsp = {vertDimWdsp: idxVertWdsp}

forecastWindSpeed = windSpeed.isel(vertDictWdsp)


# #### Define wind direction variable

# In[ ]:


windDir = ds.Wind_direction_from_which_blowing_height_above_ground
windDir = windDir.isel(x = siteXidx, y = siteYidx).isel()

timeDimWdr, vertDimWdr = windDir.metpy.time.name, windDir.metpy.vertical.name

idxVertWdr = 0
vertDictWdr = {vertDimWdr: idxVertWdr}

forecastWindDir = windDir.isel(vertDictWdr)


# #### Define weather string variable

# In[ ]:


wx = ds.Weather_string_surface
wx = wx.isel(x = siteXidx, y = siteYidx).isel()

timeDimWx = wx.metpy.time.name


# #### Define min temp variable

# In[ ]:


min_temp = ds.Minimum_temperature_height_above_ground_12_Hour_Minimum

forecastMin = min_temp.isel(x = siteXidx, y = siteYidx).isel()

timeDimMin, vertDimMin = forecastMin.metpy.time.name, forecastMin.metpy.vertical.name

idxTimeMin = slice(None, 2) # First time
idxVertMin = 0 # First (and in this case, only) vertical level

timeDictMin = {timeDimMin: idxTimeMin}
vertDictMin = {vertDimMin: idxVertMin}

forecastMin = forecastMin.isel(vertDictMin).isel(timeDictMin)


# #### Define precip probability variable

# In[ ]:


precip = ds.Total_precipitation_surface_12_Hour_Accumulation_probability_above_0p254

forecastPrecip = precip.isel(x = siteXidx, y = siteYidx).isel()

timeDimPrecip = forecastPrecip.metpy.time.name

idxTimeFull = slice(None, 4) # First 4 times

timeDictPrecip = {timeDimPrecip: idxTimeFull}

forecastPrecip = forecastPrecip.isel(timeDictPrecip)


# #### Define cloud cover variable

# In[ ]:


cloudCover = ds.Total_cloud_cover_surface

forecastCloudCover = cloudCover.isel(x = siteXidx, y = siteYidx).isel()

timeDimCloudCover = forecastCloudCover.metpy.time.name


# #### Define Temp variable

# In[ ]:


temp = ds.Temperature_height_above_ground

forecastTemp = temp.isel(x = siteXidx, y = siteYidx).isel()

timeDimTemp, vertDimTemp = forecastTemp.metpy.time.name, forecastTemp.metpy.vertical.name

idxVertTemp = 0

vertDictTemp = {vertDimTemp: idxVertTemp}

forecastTemp = forecastTemp.isel(vertDictTemp)


# #### Define dewpoint variable

# In[ ]:


dewp = ds.Dewpoint_temperature_height_above_ground

forecastDewp = dewp.isel(x = siteXidx, y = siteYidx).isel()

timeDimDewp, vertDimDewp = forecastDewp.metpy.time.name, forecastDewp.metpy.vertical.name

idxVertDewp = 0

vertDictDewp = {vertDimDewp: idxVertDewp}

forecastDewp = forecastDewp.isel(vertDictDewp)


# #### Define relative humidity variable

# In[ ]:


rh = ds.Relative_humidity_height_above_ground

forecastRH = rh.isel(x = siteXidx, y = siteYidx).isel()

timeDimRH, vertDimRH = forecastRH.metpy.time.name, forecastRH.metpy.vertical.name

idxVertRH = 0

vertDictRH = {vertDimRH: idxVertRH}

forecastRH = forecastRH.isel(vertDictRH)


# #### Define time lists at intervals of 1-hr & 12-hr

# In[ ]:


times1 = forecastCloudCover.metpy.time.values

times12 = forecastPrecip.metpy.time.values


# #### Define time lists for max and min temps

# In[ ]:


timesMax = forecastMax.metpy.time.values

timesMin = forecastMin.metpy.time.values

timesrh = forecastRH.metpy.time

#they get their own time list since they only exist for every other 12-hr period


# In[ ]:


print(YYYYMMDD_HHMM)
print('********************')


# #### Check cloud cover for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictCloudCover = {timeDimCloudCover: i}
    forecastCloudCoverNew = forecastCloudCover.isel(timeDictCloudCover)
    
    nanCheck = forecastCloudCoverNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Cloud Cover: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check wind speed for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictWdsp = {timeDimWdsp: i}
    wdspNew = forecastWindSpeed.isel(timeDictWdsp)
    
    nanCheck = wdspNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Wind Speed: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check wind direction for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictWdr = {timeDimWdr: i}
    wdrNew = forecastWindDir.isel(timeDictWdr)
    
    nanCheck = wdrNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Wind Direction: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check weather string for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictWx = {timeDimWx: i}
    wxNew = wx.isel(timeDictWx)
    
    nanCheck = wxNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Weather String: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check dewpoint for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictDewp = {timeDimDewp: i}
    dewpNew = forecastDewp.isel(timeDictDewp)
    
    nanCheck = dewpNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Dewpoint: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check temp for NaN

# In[ ]:


i = 0

for time_step in times1:

    timeDictTemp = {timeDimTemp: i}
    tempNew = forecastTemp.isel(timeDictTemp)
    
    nanCheck = tempNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Temperature: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check max temp for NaN

# In[ ]:


i = 0

for time_step in timesMax:

    timeDictMax = {timeDimMax: i}
    maxNew = forecastMax.isel(timeDictMax)
    
    nanCheck = maxNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Max Temp: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check min temp for NaN

# In[ ]:


i = 0

for time_step in timesMin:

    timeDictMin = {timeDimMin: i}
    minNew = forecastMin.isel(timeDictMin)
    
    nanCheck = minNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('Min Temp: NaN detected at index ' + istr)
     
    i = i + 1


# #### Check RH for NaN

# In[ ]:


i = 0

for time_step in timesrh:

    timeDictRH = {timeDimRH: i}
    rhNew = forecastRH.isel(timeDictRH)
    
    nanCheck = rhNew.isnull().values
    
    if nanCheck == False:
        pass
    else:
        istr = str(i)
        print('RH: NaN detected at index ' + istr)
     
    i = i + 1


# In[ ]:


print('******************')

