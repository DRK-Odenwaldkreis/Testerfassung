import sys
sys.path.append("..")
from utils.database import Database
from datetime import date
from datetime import datetime

#makes a histogram with ages, 10 year bins
def Histogram(data):

    hist = [0,0,0,0,0,0,0,0]

    today = date.today()
    #print("Today's date:", today)

    for i in data:
        diff = (today - date.fromisoformat(i[0])).days/365

        if diff < 10:
            hist[0] += 1
            continue
        if diff < 20:
            hist[1] += 1
            continue
        if diff < 30:
            hist[2] += 1
            continue
        if diff < 40:
            hist[3] += 1
            continue
        if diff < 50:
            hist[4] += 1
            continue
        if diff < 60:
            hist[5] += 1
            continue
        if diff < 70:
            hist[6] += 1
        else:
            hist[7] += 1


    return hist


#makes a histogram with ages from persons tested positve (in database)
def Histogram_Positive():
    DatabaseConnect = Database()
    sql = "Select Geburtsdatum from Vorgang where Ergebnis = 1;"
    positives = DatabaseConnect.read_all(sql)
    DatabaseConnect.close_connection()
    return Histogram(positives)


#makes a histogram with ages from a persons in database 
def Histogram_All():
    DatabaseConnect = Database()
    sql = "Select Geburtsdatum from Vorgang;"
    alle = DatabaseConnect.read_all(sql)
    DatabaseConnect.close_connection()
    return Histogram(alle)    

if __name__ == "__main__":
    

    #make histogram of positives
    hist = Histogram_Positive()

    print(hist)

    print(sum(hist))


    #make histogram of all
    hist = Histogram_All()

    print(hist)

    print(sum(hist))

