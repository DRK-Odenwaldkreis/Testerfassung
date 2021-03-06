from blabel import LabelWriter


def createLabel(content):
    vorname = content[0]
    nachname = content[1]
    date = content[2].strftime("%d.%m.%Y")
    geburtsdatum = content[3]
    adresse = content[4]
    ort = content[5]
    token = content[6]
    label_writer = LabelWriter("../utils/Labels/template.html", default_stylesheets=("../utils/Labels/style.css",))
    filename = "../../Labels/" + str(token) + ".pdf"
    records = [
        dict(Vorname=vorname, Nachname=nachname, Adresse=adresse, Wohnort=ort, Geburtsdatum=geburtsdatum, Date=date),
    ]
    label_writer.write_labels(records, target=filename)
    return filename