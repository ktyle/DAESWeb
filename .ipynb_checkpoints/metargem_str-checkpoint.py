import numpy as np

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