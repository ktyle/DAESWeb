#!/usr/bin/env python
# coding: utf-8

# ## Create a simple forecast from the NDFD which can be used with NWS graphics

# In[1]:


import numpy as np
from datetime import datetime, timedelta
import xarray as xr
import pandas as pd
import matplotlib.pyplot as plt
import cartopy.crs as ccrs
import cartopy.feature as cfeature
import metpy
from metpy.units import units
from pyproj import Proj
import pytz
from pytz import timezone
from PIL import Image


# ### Get current time rounded down to last 30 minute interval

# In[2]:


def rounded_to_the_last_30_minute():
    now = datetime.now()
    rounded = now - (now - datetime.min) % timedelta(hours=1)
    return rounded


# In[3]:


date = rounded_to_the_last_30_minute()


# ### Import NDFD data for latest time

# In[4]:


YYYYMMDD_HHMM = date.strftime('%Y%m%d_%H%M')


# In[5]:


File = "https://thredds.ucar.edu/thredds/dodsC/grib/NCEP/NDFD/NWS/CONUS/CONDUIT/NDFD_NWS_CONUS_conduit_2p5km_"+YYYYMMDD_HHMM+".grib2"
File


# In[6]:


ds = xr.open_dataset(File)


# ### Parse for data map projection and add lats & lons

# In[7]:


ds = ds.metpy.parse_cf()
ds = ds.metpy.assign_latitude_longitude(force=False)
ds


# In[8]:


x, y = ds.x, ds.y


# ### Define max temp variable & function to get closest gridpoint to ETEC

# In[9]:


def find_closest(array, value):
    idx = (np.abs(array-value)).argmin()
    return idx


# ### Get gridpoint closest to ETEC

# In[10]:


proj_data = ds.Temperature_height_above_ground.metpy.cartopy_crs
proj_data;

pFull = Proj(proj_data)


# In[11]:


siteName = "ETEC"
siteLat, siteLon = (42.75, -73.80) #lat & lon of gridpoint over ETEC
siteX, siteY = pFull(siteLon, siteLat)
siteXidx, siteYidx = find_closest(x, siteX), find_closest(y, siteY)


# In[12]:


ds = ds.isel(x = siteXidx, y = siteYidx).isel()
ds


# In[13]:


temp = ds.Temperature_height_above_ground
timeDim, vertDim = temp.metpy.time.name, temp.metpy.vertical.name
idxVert = 0 # First (and in this case, only) vertical level
idxTime = slice(None, 24)
vertDict = {vertDim: idxVert}
timeDict = {timeDim: idxTime}
temp = temp.isel(vertDict)
temp = temp.isel(timeDict)
temp = temp.drop_vars(['reftime', 'x', 'y', 'metpy_crs', 'longitude', 'latitude', vertDim])
df_temp = temp.to_dataframe()


# In[14]:


temp


# In[15]:


dewp = ds.Dewpoint_temperature_height_above_ground
dewp = dewp.isel(vertDict)
dewp = dewp.isel(timeDict)
dewp = dewp.drop_vars(['reftime', 'x', 'y', 'metpy_crs', 'longitude', 'latitude', vertDim])
df_dewp = dewp.to_dataframe()


# In[16]:


dewp


# In[17]:


wdsp = ds.Wind_speed_height_above_ground
vertDimWind = wdsp.metpy.vertical.name
vertDictWind = {vertDimWind: idxVert}
wdsp = wdsp.isel(vertDictWind)
wdsp = wdsp.isel(timeDict)
wdsp = wdsp.drop_vars(['reftime', 'x', 'y', 'metpy_crs', 'longitude', 'latitude', vertDimWind])
wdsp = wdsp * 2.23694
df_wdsp = wdsp.to_dataframe()


# In[18]:


wdsp


# In[19]:


df_merge1 = pd.merge(df_temp, df_dewp, on=timeDim)
df = pd.merge(df_merge1, df_wdsp, on=timeDim)
df


# In[20]:


import numpy as np

import matplotlib.pyplot as plt
from matplotlib.dates import DateFormatter, AutoDateLocator, YearLocator, HourLocator, DayLocator, MonthLocator

from netCDF4 import num2date

from metpy.units import units
from siphon.catalog import TDSCatalog
from siphon.ncss import NCSS
from datetime import datetime, timedelta

import pandas as pd
import xarray as xr
import metpy
import metpy.calc as mpcalc
from PIL import Image
import pytz
from pytz import timezone


# In[21]:


# Albany version is GEMPAK converted to netCDF.
# Two possibilities:  one is the one-year archive, updated once per day; the other is the most-recent week archive, updated in real time.
#metar_cat_url = 'http://thredds.atmos.albany.edu:8080/thredds/catalog/metarArchive/ncdecoded/catalog.xml?dataset=metarArchive/ncdecoded/Archived_Metar_Station_Data_fc.cdmr'
metar_cat_url = 'http://thredds.atmos.albany.edu:8080/thredds/catalog/metar/ncdecoded/catalog.xml?dataset=metar/ncdecoded/Metar_Station_Data_fc.cdmr'
# Parse the xml and return a THREDDS Catalog Object.
catalog = TDSCatalog(metar_cat_url)

metar_dataset = catalog.datasets['Feature Collection']


# In[22]:


ncss_url = metar_dataset.access_urls['NetcdfSubset']


# In[23]:


# We have the URL for our catalog's NetCDF Subset service, now create an object using the ncss client and pull
ncss = NCSS(ncss_url)


# In[24]:


ncss.variables.remove('_isMissing')


# In[25]:


# get current date and time

now = datetime.utcnow()
now = datetime(now.year, now.month, now.day, now.hour)
day_1 = now - timedelta(hours = 23, minutes = 30)

# build the query
query = ncss.query()


# In[26]:


# Select a location or list of locatons. 
#This can be either a single point (THREDDS will attempt to locate the nearest station) or an actual METAR site ID.

query.add_query_parameter(stns='ALB',subset='stns')

query.time_range(day_1, now)

#query.variables('all')
query.variables('PMSL', 'TMPC', 'DWPC', 'WNUM',
                'DRCT', 'SKNT', 'GUST', 'ALTI', 'CHC1', 'CHC2', 'CHC3')
query.accept('netcdf')


# In[27]:


data = ncss.get_data(query)


# In[28]:


data


# In[29]:


station_id = data['station_id'][0].tobytes() #get station id
station_id = station_id.decode('ascii')
print(station_id)


# In[30]:


time_var = data.variables['time'] #get the date & time of metar
#print (time_var)
time = num2date(time_var, time_var.units, only_use_cftime_datetimes=False, only_use_python_datetimes=True)
time


# In[31]:


tmpc = data.variables['TMPC'] #define variables
dwpc = data.variables['DWPC']
#slp = data.variables['PMSL']
wdsp = data.variables['SKNT']
#wdir = data.variables['DRCT']
#gust = data.variables['GUST']
#pres = data.variables['ALTI']


# In[32]:


tmpc[20]


# In[33]:


length = len(time)
hours = np.arange(0, length, 1)


# In[34]:


tmpcs = []
dwpcs = []
wdsps = []
i = 0
for x in hours:
    tmpcs.append(tmpc[i].data) 
    dwpcs.append(dwpc[i].data)
    wdsps.append(wdsp[i].data)
    i = i + 1


# In[35]:


tmpCs = tmpcs * units('degC') #attch units where necessary
tmpKs = tmpCs.to('K').magnitude

dwpCs = dwpcs * units('degC')
dwpKs = dwpCs.to('K').magnitude

wdsKt = wdsps * units('kt')
wdmph = wdsKt.to('mph').magnitude


# In[36]:


df2 = pd.DataFrame(
    { timeDim : time,
     'Temperature_height_above_ground' : tmpKs,
     'Dewpoint_temperature_height_above_ground' : dwpKs,
     'Wind_speed_height_above_ground' : wdmph}   
)


# In[37]:


df2 = df2.set_index(df2.columns[0])


# In[38]:


df2


# In[39]:


df3 = pd.concat([df2, df])
df3 = df3.rename(columns={timeDim: 'Time',
                          "Temperature_height_above_ground": "T",
                          "Dewpoint_temperature_height_above_ground": "Td",
                          "Wind_speed_height_above_ground": "Wind"})


# In[40]:


df3 = df3.reset_index()


# In[41]:


df3['Time'] = pd.to_datetime(df3[timeDim])

fullTimes = df3['Time']
newTimes = []
for timestep in fullTimes:
    if timestep.minute > 29:
        newtime = timestep.replace(hour = timestep.hour + 1, minute = 00)
        newTimes.append(newtime)
    else:
        newtime = timestep
        newTimes.append(newtime)df4 = pd.DataFrame(
    { 'Time' : newTimes}
)df3['Time'] = df4['Time'].values
# In[42]:


df3['T'] = (df3['T'] - 273.15) * (9/5) + 32
df3['Td'] = (df3['Td'] - 273.15) * (9/5) + 32


# In[43]:


tempsF = df3['T']
windsMph = df3['Wind']


# In[44]:


windChills = []
i = 0
for wind in windsMph:
    if tempsF[i] <= 50 and windsMph[i] > 3:
        windChill = 35.74 + (0.6215 * tempsF[i]) - (35.75 * (windsMph[i]**0.16)) + (0.4275 * tempsF[i] * (windsMph[i]**0.16))
        windChills.append(windChill)
    else:
        windChills.append(float('NaN'))
    i = i + 1


# In[45]:


df4 = pd.DataFrame(
    {'WindChill' : windChills}   
)


# In[46]:


df5 = pd.concat([df3, df4], axis=1)
df5


# In[47]:


df5['T'] = round(df5['T'])
df5['Td'] = round(df5['Td'])
df5['Wind'] = round(df5['Wind'])
df5['WindChill'] = round(df5['WindChill'])


# In[48]:


df5


# In[49]:


df5 = df5.drop(columns=timeDim)


# In[50]:


df5.to_csv('ALB_obs_fore.csv')


# In[ ]:




