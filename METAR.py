#!/usr/bin/env python
# coding: utf-8

# # METAR Data Query using Siphon from a THREDDS server

# In[1]:


import numpy as np

import matplotlib.pyplot as plt
from matplotlib.dates import DateFormatter, AutoDateLocator,YearLocator, HourLocator,DayLocator,MonthLocator

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


# In[2]:


# Load in a collection of functions that process GEMPAK weather conditions and cloud cover data.
# Create a dictionary for attaching units to the different variables
gem_col_units = {'time': None,
 'station': None,
 'latitude': 'degrees',
 'longitude': 'degrees',
 'ALTI': 'inHg',
 'CEIL': 'feet',
 'CHC1': 'feet',
 'CHC2': 'feet',
 'CHC3': 'feet',
 'COUN': None,
 'CTYL': None,
 'CTYM': None,
 'CTYH': None,
 'DRCT': 'degrees',
 'DWPC': 'degC',
 'GUST': 'kts',
 'MSUN': 'minutes',
 'P01I': 'inches',
 'P03C': 'hPa',
 'P03D': None,
 'P03I': 'inches',
 'P06I': 'inches',
 'P24I': 'inches',
 'PMSL': 'hPa',
 'SKNT': 'kts',
 'SNEW': 'inches',
 'SNOW': 'inches',
 'SPRI': None,
 'STAT': None,
 'STD2': None,
 'STID': None,
 'STNM': None,
 'T6NC': 'degC',
 'T6XC': 'degC',
 'TDNC': 'degC',
 'TDXC': 'degC',
 'TMPC': 'degC',
 'VSBY': 'miles',
 'WEQS': 'inches',
 'WNUM': None
}

def calc_clouds(chc1, chc2, chc3):
    
    total_cloud = {
    '1' : 0, # CLR
    '2' : 3, # SCT
    '3' : 6, # BKN
    '4' : 8, # OVC
    '5' : 9, # OBS
    '6' : 1, # FEW
    '-1': -1 # MSG
    }
    for n in range (0, chc1.size):
        chc1c = chc1[n].astype(str)[-1]
        chc2c = chc2[n].astype(str)[-1]
        chc3c = chc3[n].astype(str)[-1]
        default = -1
        chc1[n] = total_cloud.get(chc1c,default)
        chc2[n] = total_cloud.get(chc2c,default)
        chc3[n] = total_cloud.get(chc3c,default)
        
    # Now, to determine the total cloud cover, find the maxima of the layers.  
    CLDC = np.maximum.reduce([chc1,chc2,chc3])
    return CLDC

def convert_wnum (wnum):
    # This dictionary maps METAR present weather codes that are read in from a GEMPAK 
    # surface station file (GEMPAK surface parameter WNUM) to the corresponding WMO 
    # integer weather code, so it can be used by MetPy's wx_code_map dictionary, as
    # defined in wx_symbols.py.
    # Source:  GEMPAK ptwcod.f
    gemWx = {
       -1: 19, # FC (Tornado)
       -2: 19, # FC (Funnel Cloud)
       -3: 19, # FC (Waterspout)
        1: 63, # RA
        2: 53, # DZ
        3: 73, # SN
        4: 90, # GR
        5: 17, # TS
        6:  5, # HZ
        7:  4, # FU
        8:  6, # DU
        9: 10, # BR
        10: 18, # SQ
        11:  0, # PY -- Not in current wx_code map
        13: 61, # -RA
        14: 65, # +RA
        15: 67, # FZRA
        16: 81, # SHRA
        17: 51, # -DZ
        18: 55, # +DZ
        19: 57, # FZDZ
        20: 71, # -SN
        21: 75, # +SN
        22: 86, # SHSN
        23: 79, # PL
        24: 77, # SG
        25: 88, # GS
        26: 89, # -GR
        27: 90, # +GR
        28:  0, # -TSSN -- Not in current wx_code map
        29: 97, # +TSSN
        30: 49, # FZFG
        31: 45, # FG
        32: 38, # BLSN
        33: 1007, # BLDU
        34:  0, # BLPY -- Not in current wx_code map
        35:  0, # BLPN -- Not in current wx_code map
        36: 78, # IC
        41: 76, # UP
        49: 66, # -FZRA
        50: 67, # +FZRA
        51: 89, # -SHRA
        52: 81, # +SHRA
        53: 56, # -FZDZ
        54: 57, # +FZDZ
        55: 85, # -SHSN
        56: 86, # +SHSN
        57: 79, # -PL -- Not in current wx_code map; use PL
        58: 79, # +PL -- Not in current wx_code map; use PL
        59: 77, # -SG -- Not in current wx_code map; use SG
        60: 77, # +SG -- Not in current wx_code map; use SG
        61: 87, # -GS
        62: 88, # +GS
        63: 79, # SHPL -- Not in current wx_code map; use PL
        64: 78, # -IC -- Not in current wx_code map; use IC
        65: 78, # +IC -- Not in current wx_code map; use IC
        66: 95, # TSRA
        67: 88, # SHGS
        68: 1007, # +BDLU -- Not in current wx_code map; use BLDU
        69:  0, # +BLSA -- Not in current wx_code map
        70: 39, # +BLSN
        75: 87, # -SHGS
        76: 88, # +SHGS
        77: 95, # -TSRA
        78: 97 # +TSRA        
    }
    default = 0
    for n in range (0,wnum.size):
        if (wnum[n] ==  0):
          pass
        else:
#         Up to three character codes can make up a GEMPAK weather number (WNUM).  Determine these
#         invididual GEMPAK weather code numbers, but use only the first at this time
          inum = [0, 0, 0]
          num = wnum[n]
          inum[0] = num % 80
          num = (num - inum[0]) // 80
          inum [1] = num % 80
          num = (num - inum[1]) //80
          inum [2] = num
          wnum[n] = gemWx.get(inum[0], default)

def convert_wnum_str (wnum):
    # This dictionary maps METAR present weather codes that are read in from a GEMPAK 
    # surface station file (GEMPAK surface parameter WNUM) to the corresponding WMO 
    # integer weather code, so it can be used by MetPy's wx_code_map dictionary, as
    # defined in wx_symbols.py.
    # Source:  GEMPAK ptwcod.f
    gemWx = {
       -1: 'Funnel Cloud', # FC (Tornado)
       -2: 'Funnel Cloud', # FC (Funnel Cloud)
       -3: 'Funnel Cloud', # FC (Waterspout)
        1: 'Rain', # RA
        2: 'Drizzle', # DZ
        3: 'Snow', # SN
        4: 'Hail', # GR
        5: 'Thunderstorms', # TS
        6: 'Haze', # HZ
        7: 'Smoke', # FU
        8: 'Dust', # DU
        9: 'Mist', # BR
        10: 'Squalls', # SQ
        11: 'Spray', # PY -- Not in current wx_code map
        13: 'Light Rain', # -RA
        14: 'Heavy Rain', # +RA
        15: 'Freezing Rain', # FZRA
        16: 'Rain Showers', # SHRA
        17: 'Light Drizzle', # -DZ
        18: 'Heavy Drizzle', # +DZ
        19: 'Freezing Drizzle', # FZDZ
        20: 'Light Snow', # -SN
        21: 'Heavy Snow', # +SN
        22: 'Snow Showers', # SHSN
        23: 'Ice Pellets', # PL
        24: 'Snow Grains', # SG
        25: 'Small Hail', # GS
        26: 'Light Hail', # -GR
        27: 'Heavy Hail', # +GR
        28: 'Light Thunderstorms, Snow', # -TSSN -- Not in current wx_code map
        29: 'Heavy Thunderstorms, Snow', # +TSSN
        30: 'Freezing Fog', # FZFG
        31: 'Fog', # FG
        32: 'Blowing Snow', # BLSN
        33: 'Blowing Dust', # BLDU
        34: 'Blowing Spray', # BLPY -- Not in current wx_code map
        36: 'Ice Crystals', # IC
        41: 'Unknown Precipitation', # UP
        49: 'Light Freezing Rain', # -FZRA
        50: 'Heavy Freezing Rain', # +FZRA
        51: 'Light Rain Showers', # -SHRA
        52: 'Heavy Rain Showers', # +SHRA
        53: 'Light Freezing Drizzle', # -FZDZ
        54: 'Heavy Freezing Drizzle', # +FZDZ
        55: 'Light Snow Showers', # -SHSN
        56: 'Heavy Snow Showers', # +SHSN
        57: 'Light Ice Pellets', # -PL -- Not in current wx_code map; use PL
        58: 'Heavy Ice Pellets', # +PL -- Not in current wx_code map; use PL
        59: 'Light Snow Grains', # -SG -- Not in current wx_code map; use SG
        60: 'Heavy Snow Grains', # +SG -- Not in current wx_code map; use SG
        61: 'Light Small Hail', # -GS
        62: 'Heavy Small Hail', # +GS
        63: 'Ice Pellet Showers', # SHPL -- Not in current wx_code map; use PL
        64: 'Light Ice Crystals', # -IC -- Not in current wx_code map; use IC
        65: 'Heavy Ice Crystals', # +IC -- Not in current wx_code map; use IC
        66: 'Thunderstorms, Rain', # TSRA
        67: 'Small Hail Showers', # SHGS
        68: 'Heavy Blowing Dust', # +BLDU -- Not in current wx_code map; use BLDU
        69: 'Heavy Blowing Sand', # +BLSA -- Not in current wx_code map
        70: 'Heavy Blowing Snow', # +BLSN
        75: 'Light Small Hail Showers', # -SHGS
        76: 'Heavy Small Hail Showers', # +SHGS
        77: 'Light Thunderstorms, Rain', # -TSRA
        78: 'Heavy Thunderstorms, Rain' # +TSRA        
    }
    default = 0
    for n in range (0,wnum.size):
        if (wnum[n] ==  0):
          wX = 'no_wx'
        else:
#         Up to three character codes can make up a GEMPAK weather number (WNUM).  Determine these
#         invididual GEMPAK weather code numbers, but use only the first at this time
          inum = [0, 0, 0]
          num = wnum[n]
          inum[0] = num % 80
          num = (num - inum[0]) // 80
          inum [1] = num % 80
          num = (num - inum[1]) //80
          inum [2] = num
          wX = gemWx.get(inum[0], default)
    return wX
#This only works if you are converting a single wnum

def get_wx_img(wx_str, cloud_cover, wdsp):
    Wx_img = {
       'Funnel Cloud': 'NWS_images/wX/funnel_cloud/tor.png', # FC (Tornado)
       'Funnel Cloud': 'NWS_images/wX/funnel_cloud/fc.png', # FC (Funnel Cloud)
       'Funnel Cloud': 'NWS_images/wX/funnel_cloud/fc.png', # FC (Waterspout)
       'Rain': 'NWS_images/wX/rain/ra.png', # RA
       'Drizzle': 'NWS_images/wX/drizzle/minus_ra.png', # DZ
       'Snow': 'NWS_images/wX/snow/sn.png', # SN
       'Hail': 'NWS_images/wX/ice_pellets/ip.png', # GR
       'Thunderstorms': 'NWS_images/wX/thunderstorms/tsra.png', # TS
       'Haze': 'NWS_images/wX/haze/hz.png', # HZ
       'Smoke': 'NWS_images/wX/smoke/fu.png', # FU
       'Dust': 'NWS_images/wX/dust/du.png', # DU
       'Mist': 'NWS_images/wX/fog/fg.png', # BR
       'Squalls': '******come back to this one******', # SQ
       'Spray': 'NWS_images/wX/drizzle/minus_ra.png', # PY -- Not in current wx_code map
       'Light Rain': 'NWS_images/wX/drizzle/minus_ra.png', # -RA
       'Heavy Rain': 'NWS_images/wX/rain/ra.png', # +RA
       'Freezing Rain': 'NWS_images/wX/freezing_rain/fzra.png', # FZRA
       'Rain Showers': 'NWS_images/wX/showers/shra.png', # SHRA
       'Light Drizzle': 'NWS_images/wX/drizzle/minus_ra.png', # -DZ
       'Heavy Drizzle': 'NWS_images/wX/drizzle/minus_ra.png', # +DZ
       'Freezing Drizzle': 'NWS_images/wX/freezing_rain/fzra.png', # FZDZ
       'Light Snow': 'NWS_images/wX/snow/sn.png', # -SN
       'Heavy Snow': 'NWS_images/wX/snow/sn.png', # +SN
       'Snow Showers': 'NWS_images/wX/snow/sn.png', # SHSN
       'Ice Pellets': 'NWS_images/wX/ice_pellets/ip.png', # PL
       'Snow Grains': 'NWS_images/wX/snow/sn.png', # SG
       'Small Hail': 'NWS_images/wX/ice_pellets/ip.png', # GS
       'Light Hail': 'NWS_images/wX/ice_pellets/ip.png', # -GR
       'Heavy Hail': 'NWS_images/wX/ice_pellets/ip.png', # +GR
       'Light Thunderstorms, Snow': 'NWS_images/wX/snow/sn.png', # -TSSN -- Not in current wx_code map
       'Heavy Thunderstorms, Snow': 'NWS_images/wX/snow/sn.png', # +TSSN
       'Freezing Fog': 'NWS_images/wX/fog/fg.png', # FZFG
       'Fog': 'NWS_images/wX/fog/fg.png', # FG
       'Blowing Snow': 'NWS_images/wX/snow/sn.png', # BLSN
       'Blowing Dust': 'NWS_images/wX/dust/du.png', # BLDU
       'Blowing Spray': 'NWS_images/wX/drizzle/minus_ra.png', # BLPY -- Not in current wx_code map
       'Ice Crystals': 'NWS_images/wX/ice_pellets/ip.png', # IC
       'Unknown Precipitation': 'NWS_images/wX/rain/ra.png', # UP
       'Light Freezing Rain': 'NWS_images/wX/freezing_rain/fzra.png', # -FZRA
       'Heavy Freezing Rain': 'NWS_images/wX/freezing_rain/fzra.png', # +FZRA
       'Light Rain Showers': 'NWS_images/wX/showers/shra.png', # -SHRA
       'Heavy Rain Showers': 'NWS_images/wX/showers/shra.png', # +SHRA
       'Light Freezing Drizzle': 'NWS_images/wX/freezing_rain/fzra.png', # -FZDZ
       'Heavy Freezing Drizzle': 'NWS_images/wX/freezing_rain/fzra.png', # +FZDZ
       'Light Snow Showers': 'NWS_images/wX/snow/sn.png', # -SHSN
       'Heavy Snow Showers': 'NWS_images/wX/snow/sn.png', # +SHSN
       'Light Ice Pellets': 'NWS_images/wX/ice_pellets/ip.png', # -PL -- Not in current wx_code map; use PL
       'Heavy Ice Pellets': 'NWS_images/wX/ice_pellets/ip.png', # +PL -- Not in current wx_code map; use PL
       'Light Snow Grains': 'NWS_images/wX/snow/sn.png', # -SG -- Not in current wx_code map; use SG
       'Heavy Snow Grains': 'NWS_images/wX/snow/sn.png', # +SG -- Not in current wx_code map; use SG
       'Light Small Hail': 'NWS_images/wX/ice_pellets/ip.png', # -GS
       'Heavy Small Hail': 'NWS_images/wX/ice_pellets/ip.png', # +GS
       'Ice Pellet Showers': 'NWS_images/wX/ice_pellets/ip.png', # SHPL -- Not in current wx_code map; use PL
       'Light Ice Crystals': 'NWS_images/wX/ice_pellets/ip.png', # -IC -- Not in current wx_code map; use IC
       'Heavy Ice Crystals': 'NWS_images/wX/ice_pellets/ip.png', # +IC -- Not in current wx_code map; use IC
       'Thunderstorms, Rain': 'NWS_images/wX/thunderstorms/tsra.png', # TSRA
       'Small Hail Showers': 'NWS_images/wX/ice_pellets/ip.png', # SHGS
       'Heavy Blowing Dust': 'NWS_images/wX/dust/du.png', # +BLDU -- Not in current wx_code map; use BLDU
       'Heavy Blowing Sand': 'NWS_images/wX/dust/du.png', # +BLSA -- Not in current wx_code map
       'Heavy Blowing Snow': 'NWS_images/wX/snow/sn.png', # +BLSN
       'Light Small Hail Showers': 'NWS_images/wX/ice_pellets/ip.png', # -SHGS
       'Heavy Small Hail Showers': 'NWS_images/wX/ice_pellets/ip.png', # +SHGS
       'Light Thunderstorms, Rain': 'NWS_images/wX/thunderstorms/tsra.png', # -TSRA
       'Heavy Thunderstorms, Rain': 'NWS_images/wX/thunderstorms/tsra.png' # +TSRA        
    }
    
    if wx_str != 'no_wx':
        img_str = Wx_img.get(wx_str)
    elif wdsp > 13:
        if cloud_cover == 0:
            img_str = 'NWS_images/cloud_cover/clear/wind_skc.png'
        elif cloud_cover == 1:
            img_str = 'NWS_images/cloud_cover/few/wind_few.png'
        elif cloud_cover == 3:
            img_str = 'NWS_images/cloud_cover/scattered/wind_sct.png'
        elif cloud_cover == 6:
            img_str = 'NWS_images/cloud_cover/broken/wind_bkn.png'
        elif cloud_cover == 8:
            img_str = 'NWS_images/cloud_cover/overcast/wind_ovc.png'
        elif cloud_cover == 9:
            img_str = 'Cloud Cover Obstructed'
        elif cloud_cover == -1:
            img_str = 'Cloud Cover Missing'
    else:
        if cloud_cover == 0:
            img_str = 'NWS_images/cloud_cover/clear/skc.png'
        elif cloud_cover == 1:
            img_str = 'NWS_images/cloud_cover/few/few.png'
        elif cloud_cover == 3:
            img_str = 'NWS_images/cloud_cover/scattered/sct.png'
        elif cloud_cover == 6:
            img_str = 'NWS_images/cloud_cover/broken/bkn.png'
        elif cloud_cover == 8:
            img_str = 'NWS_images/cloud_cover/overcast/ovc.png'
        elif cloud_cover == 9:
            img_str = 'Cloud Cover Obstructed'
        elif cloud_cover == -1:
            img_str = 'Cloud Cover Missing'
    return img_str


# In[3]:


# Albany version is GEMPAK converted to netCDF.
# Two possibilities:  one is the one-year archive, updated once per day; the other is the most-recent week archive, updated in real time.
#metar_cat_url = 'http://thredds.atmos.albany.edu:8080/thredds/catalog/metarArchive/ncdecoded/catalog.xml?dataset=metarArchive/ncdecoded/Archived_Metar_Station_Data_fc.cdmr'
metar_cat_url = 'http://thredds.atmos.albany.edu:8080/thredds/catalog/metar/ncdecoded/catalog.xml?dataset=metar/ncdecoded/Metar_Station_Data_fc.cdmr'
# Parse the xml and return a THREDDS Catalog Object.
catalog = TDSCatalog(metar_cat_url)

metar_dataset = catalog.datasets['Feature Collection']


# In[4]:


ncss_url = metar_dataset.access_urls['NetcdfSubset']


# In[5]:


# We have the URL for our catalog's NetCDF Subset service, now create an object using the ncss client and pull
ncss = NCSS(ncss_url)


# In[6]:


ncss.variables.remove('_isMissing')


# In[7]:


# get current date and time

now = datetime.utcnow()
now = datetime(now.year, now.month, now.day, now.hour)

# build the query
query = ncss.query()


# In[8]:


# Select a location or list of locatons. 
#This can be either a single point (THREDDS will attempt to locate the nearest station) or an actual METAR site ID.

query.add_query_parameter(stns='ALB',subset='stns')

query.time(now)

#query.variables('all')
query.variables('PMSL', 'TMPC', 'DWPC', 'WNUM',
                'DRCT', 'SKNT', 'GUST', 'ALTI', 'CHC1', 'CHC2', 'CHC3')
query.accept('netcdf')


# In[9]:


data = ncss.get_data(query)


# In[10]:


data.variables


# In[11]:


station_id = data['station_id'][0].tobytes() #get station id
station_id = station_id.decode('ascii')
print(station_id)


# In[12]:


time_var = data.variables['time'] #get the date & time of metar
#print (time_var)
time = num2date(time_var, time_var.units, only_use_cftime_datetimes=False, only_use_python_datetimes=True)
time[0]


# In[13]:


print(time[0])


# In[14]:


tmpc = data.variables['TMPC'][0] #defien variables (using the first value)
dwpc = data.variables['DWPC'][0]
slp = data.variables['PMSL'][0]
wX = data.variables['WNUM'][0]
wdsp = data.variables['SKNT'][0]
wdir = data.variables['DRCT'][0]
gust = data.variables['GUST'][0]
pres = data.variables['ALTI'][0]


# In[15]:


tmpc = tmpc.data * units('degC') #attch units where necessary
tmpf = tmpc.to('degF')
tmpC = round(tmpc, 0)
tmpF = round(tmpf, 0)

dwpc = dwpc.data * units('degC')
dwpf = dwpc.to('degF')
dwpC = round(dwpc, 0)
dwpF = round(dwpf, 0)


# In[16]:


RH = mpcalc.relative_humidity_from_dewpoint(tmpc, dwpc).to('percent') #calculate RH
RH = round(RH, 0)


# In[17]:


wdsp = wdsp.data * units('kts')
wdir_d = wdir.data * units('degrees')
wdsp


# In[18]:


u, v = mpcalc.wind_components(wdsp, wdir_d)


# In[19]:


gust = gust.data * units('kts')


# In[20]:


wnum = (np.nan_to_num(data['WNUM'],True).astype(int))
wx = convert_wnum_str(wnum)

# Need to handle missing (NaN) and convert to proper code
chc1 = (np.nan_to_num(data['CHC1'],True).astype(int))
chc2 = (np.nan_to_num(data['CHC2'],True).astype(int))
chc3 = (np.nan_to_num(data['CHC3'],True).astype(int))
cloud_cover = calc_clouds(chc1, chc2, chc3)


# In[21]:


cloud_cover = cloud_cover[0]


# In[22]:


if cloud_cover == 0: #turn cloud cover number into a string
    cc = 'Clear'
elif cloud_cover == 1:
    cc = 'Mostly Clear'
elif cloud_cover == 3:
    cc = 'Partly Cloudy'
elif cloud_cover == 6:
    cc = 'Mostly Cloudy'
elif cloud_cover == 8:
    cc = 'Overcast'
elif cloud_cover == 9:
    cc =  'Cloud Cover Obstructed'
elif cloud_cover == -1:
    cc = 'Cloud Cover Missing'


# In[23]:


fig1, axs1 = plt.subplots(figsize=[2.2,2.2]) #plot wind barb
axs1.barbs(u, v, length=12, pivot='middle')
plt.xticks([], [])
plt.yticks([], [])
plt.savefig('images/wind_barb.png')


# In[24]:


im = Image.open('images/wind_barb.png') #open the saved plot


# In[25]:


im = im.crop((30, 30, 195, 195)) #crop out border


# In[26]:


rgba = im.convert("RGBA") #make background transparent
datas = rgba.getdata() 
  
newData = [] 
for item in datas: 
    if item[0] == 255 and item[1] == 255 and item[2] == 255:  # finding black colour by its RGB value 
        # storing a transparent value when we find a black colour 
        newData.append((255, 255, 255, 0)) 
    else: 
        newData.append(item)  # other colours remain unchanged 
  
rgba.putdata(newData) 
rgba.save("images/transparent_barb.png", "PNG")


# In[27]:


def wdir_to_wdir_str(wind_dir): #get wdir str from wdr
    if 22.5 < wind_dir < 67.5:
        wind_dir_str = 'NE'
    elif 67.5 < wind_dir < 112.5:
        wind_dir_str = 'E'
    elif 112.5 < wind_dir < 157.5:
        wind_dir_str = 'SE'
    elif 157.5 < wind_dir < 202.5:
        wind_dir_str = 'S'
    elif 202.5 < wind_dir < 247.5:
        wind_dir_str = 'SW'
    elif 247.5 < wind_dir < 292.5:
        wind_dir_str = 'W'
    elif 292.5 < wind_dir < 337.5:
        wind_dir_str = 'NW'
    elif wind_dir > 337.5 or wind_dir < 22.5:
        wind_dir_str = 'N'
        
    return wind_dir_str


# In[28]:


wdir_str = wdir_to_wdir_str(wdir)


# In[29]:


slp = slp.__float__()


# In[30]:


slp = slp * units('hPa')
slpHG = slp.to('inHg')


# In[31]:


slp = round(slp, 1)
slpHG = round(slpHG, 2)


# In[32]:


print('----------------')
print(f'Conditions in Albany as of {time[0]} UTC')
if wx == 'no_wx':
    print(cc)
else:
    print(wx)
print(f'Temperature: {tmpF} ({tmpC})')
print(f'Dewpoint: {dwpF} ({dwpC})')
print(f'Relative Humidity: {RH}')
if np.isnan(gust) == True:
    if wdsp == 0:
        print('Calm')
    else:
        print(f'Wind: {wdir_str} at {wdsp}')
else:
    print(f'Wind: {wdir_str} at {wdsp}, gusts to {gust}')
print(f'Sea-level Pressure: {slp} hPa')
print(f'Station Pressure {pres} inHG')
print('----------------')


# In[33]:


tmpF = tmpF.magnitude
tmpC = tmpC.magnitude
dwpF = dwpF.magnitude
dwpC = dwpC.magnitude
RH = RH.magnitude
slp = slp.magnitude
slpHG = slpHG.magnitude
wdsp = wdsp.magnitude
gust = gust.magnitude


# In[34]:


tmpF = int(tmpF)
tmpC = int(tmpC)
dwpF = int(dwpF)
dwpC = int(dwpC)
RH = int(RH)
windsp = int(wdsp)
if np.isnan(gust) == True:
    pass
else:
    gust = int(gust)


# In[35]:


f = open('metar.txt', 'w')
if wx == 'no_wx':
    f.write(f'{cc}\n')
else:
    f.write(f'{wx}\n')
f.close()

f = open('metar.txt', 'a')
f.write(f'{tmpF}°F\n')
f.write(f'{tmpC}°C\n')
f.write(f'{dwpF}°F\n')
f.write(f'{dwpC}°C\n')
if np.isnan(gust) == True:
    if wdsp == 0:
        f.write('Calm\n')
    else:
        f.write(f'{wdir_str} at {windsp}kt\n')
else:
    f.write(f'{wdir_str} at {windsp}kt, G{gust}kt\n')
f.write(f'{RH}%\n')
f.write(f'{slp}mb\n')
f.write(f'{slpHG}"\n')
f.close()


# In[36]:


cc


# In[37]:


imgWx = get_wx_img(wx, cloud_cover, wdsp)
imgWx


# In[38]:


current_wx = Image.open(imgWx)
current_wx


# In[39]:


if current_wx.size[0] != 86:
    current_wx = current_wx.resize((86, 86))
else:
    pass


# In[40]:


current_wx.save('images/current_wx.png')


# In[ ]:




