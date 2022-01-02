from ics import Calendar, Event
from datetime import datetime,timedelta
import sys
sys.path.append("..")
from utils.slot import start_time
import pytz


def create_ics(day,slot,stunde,location,token):
    local = pytz.timezone("Europe/Berlin")
    naive = datetime.strptime(f'{day} {start_time(slot,stunde)}', "%Y-%m-%d %H:%M:%S")
    local_dt = local.localize(naive, is_dst=None)
    utc_dt = local_dt.astimezone(pytz.utc)
    duration = timedelta(minutes=15)
    utc_dt_end = utc_dt + duration
    c = Calendar()
    e = Event()
    e.name = "Termin Testzentrum"
    e.begin = utc_dt.strftime("%Y-%m-%d %H:%M:%S")
    e.end = utc_dt_end.strftime("%Y-%m-%d %H:%M:%S")
    e.location = location
    e.url = 'www.testzentrum-odw.de'
    e.TZID = 'Europe/Berlin'
    e.organizer = 'DRK Kreisverband Odenwaldkreis e.V.'
    c.events.add(e)
    c.events
    with open(f'../../Tickets/{token}.ics', 'w') as my_file:
        my_file.writelines(c)
    return f'../../Tickets/{token}.ics'