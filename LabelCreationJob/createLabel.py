from blabel import LabelWriter
import datetime

def createLabel(content):
    vorname = content[0]
    nachname = content[1]
    date = content[2].strftime("%d.%m.%Y")
    geburtsdatum = datetime.datetime.strptime(content[3], '%Y-%m-%d').strftime("%d.%m.%Y")
    adresse = content[4]
    ort = content[5]
    token = content[6]
    telefon = content[7]
    label_writer = LabelWriter("../utils/Labels/template.html", default_stylesheets=("../utils/Labels/style.css",))
    filename = "../../Labels/" + str(token) + ".pdf"
    records = [
        dict(Vorname=vorname, Nachname=nachname, Adresse=adresse, Wohnort=ort, Geburtsdatum=geburtsdatum, Date=date, Telefon=telefon),
    ]
    label_writer.write_labels(records, target=filename)
    return filename