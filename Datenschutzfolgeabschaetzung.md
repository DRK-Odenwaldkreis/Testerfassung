## IT System Corona Testzentrum
# Datenschutzfolgeabschätzung
Durch den Betrieb eines IT gestützten System für die Weiterleitung von Coronaschnelltestergebnissen an getestete Personen und evtl. das Gesundheitsamt und die damit zusammenhängende Verarbeitung personenbezogener Daten der getesteten Personen kann der Verlust der personenbezogenen Daten bzw. der Zugriff von Unberechtigten nicht vollständig ausgeschlossen werden. In der hier vorliegenden Datenschutzfolgeabschätzung nach §67 Bundesdatenschutzgesetz werden daher die mit Hilfe der Software durchgeführten Vorgänge erläutert und nach datenschutzrechtlichen Gesichtspunkten bewertet

## 1. Zweck der Verarbeitung
Zweck der Verarbeitung ist der automatisierter Versand von Ergebnissen der Coronaschnelltests an getestete Personen sowie bei positivem Befund an das Gesundheitsamt des Odenwaldkreis.

## 2. Beschreibung der Vorgänge

### Anmeldung zu einem Schnelltest
Personen, die sich für einen Test interessieren, können sich online, mit Angabe ihrer Daten inklusive Emailadresse, für einen Test anmelden.
Dabei gibt es drei Optionen:
1. Anmeldung für einen Test mit Terminvereinbarung
2. Anmeldung für einen Test ohne Terminvereinbarung
3. Anmeldung für einen Test bei einem teilnehmenden Pfleg- bzw. Behindertenwohnheim

Die zu testenden Personen können sich für einen freien Termin in einem der Testzentren anmelden und bekommen zur E-Mail-Verifizierung eine E-Mail zugeschickt, die sie bestätigen müssen. Mit Hilfe einer Webapplikation werden die Daten der Person in einer zentralen Datenbank auf einem Webserver erfasst. Die in dieser Datenbank gespeicherten personenbezogenen Daten bestehen aus der Kombination von Vorname, Nachname, Geburtsdatum, Adresse, Telefonnummer, Emailadresse und Teststation sowie Testzeitpunkt. Mit der Registrierung bekommen die zu testenden Personen ein QR Code (Buchungscode) angestellt. 
Mit Hilfe des Buchungscodes können die Personen beim Testzentrum die Buchung eines Termins für einen SARS Cov2 Schnelltest nachweisen. Personen, die keinen Termin online gebucht haben können zusätzlich noch vor Ort durch Mitarbeiter der Testzentren erfasst werden. 
Bei der Registrierung über die Website oder im Testzentrum werden die zu testenden Personen über die Erfassung der personenbezogenen Daten aufgeklärt.

### Anmeldung für Bewohner von Pflege- und Behindertenwohnheimen
Es ist davon auszugehen, dass Bewohner von Pflege- bzw. Behindertenwohnheimen sich nicht selbstständig für einen Schnelltest anmelden können. Die Anmeldung obliegt dann der Leitung des Heims (dies muss natürlich mit den Bewohnern bzw. Gesetzlichem Betreuer abgestimmt sein). Da es unpraktisch erscheint alle Bewohner eines Heim einzeln über das Webinterface anzumelden, stellen wir den Heimleitungen den Weg einer Sammelanmeldung zur Verfügung. Dabei übermittelt die Heimleitung eine CSV Datei mit den notwendigen Daten an die zuständige Stelle im DRK KV Odenwaldkreis e.V. (diese Übermittlung ist von dem hier beschriebenen IT System unabhängig, die Vertraulichkeit der persönlichen Daten der Betroffenen muss jedoch auch während dieser Übermittelung gewährleistet sein).
Der zuständige Mitarbeiter des DRK KV Odenwaldkreis im Backoffice (siehe unten) kann die CSV Datei mit den Daten der Heimbewohner in der Webapp hochladen, weitere Daten wie Name und Kontaktdaten der Einrichtung sowie Testdatum zufügen.

### Speicherdauer für Voranmeldungen
Je nach Anmeldeart werden die persönlichen Daten aus der Voranmeldung unterschiedlich lange gespeichert. 
1. Anmeldung für einen Test mit Terminvereinbarung:
Die Daten zur Voranmeldung werden mind. bis zum gebuchten Testzeitpunkt gespeichert. Nimmt die Person den Test wirklich in Anspruch werden mit Abschluss des Test die Voranmeldedaten in die Testdatenbank transferiert, in der Voranmeldungsdatenbank verbleiben keine Daten. Löschfristen für die Testdatenbank sind weiter unten im Text beschrieben. Nimmt die Person den Test nicht wahr, werden die Daten zum Ende des Tags, für den der Test vereinbart war, gelöscht.
2. Anmeldung für einen Test ohne Terminvereinbarung:
Einige der Teststationen bieten auch Voranmeldungen ohne konkrete Terminbuchungen wahr. Meldet sich eine Person über diesen Weg an werden nach absolviertem Test die Voranmeldedaten in die Testdatenbank transferiert, in der Voranmeldungsdatenbank verbleiben keine Daten. Löschfristen für die Testdatenbank sind weiter unten im Text beschrieben. Nimmt die Person den Test nicht wahr, werden die Daten zwei Tage nach Ende des Tages, an dem die Anmeldung stattfand, gelöscht.
3. Anmeldung für Bewohner von Pflege- bzw. Behindertenwohnheimen: 
Siehe 2.

### Ablauf im Testzentrum
Nach Anmeldung im Testzentrums (durch Scan des Buchungscodes oder manueller Erfassung) wird der zu testenden Person noch ein zweiter QR Code ausgehändigt (Testcode). Dieser Testcode wird durch einen Mitarbeiter gescannt wodurch der im Code enthaltene Token den persönlichen Daten der zu testenden Person hinzugefügt.
Nun wird der Coronaschnelltest durchgeführt. Der Test wird daraufhin mit dem Testcode zusammen abgelegt. Sobald das Ergebnis feststeht, scannt ein Helfer den Testcode und kann direkt das Testergebnis eintragen. Dadurch werden dem Datensatz noch Testergebnis und Ergebniszeitpunkt hinzugefügt.
Dadurch, dass der Testcode immer beim Schnelltest verbleibt und das Testergebnis nicht manuell mit den personenbezogenen Daten verknüpft werden muss, wird das Risiko einer Verwechslung drastisch minimiert.

### Übermittelung von Testergebnissen
Nach Eintragen des Ergebnis in die Webapp wird automatisch eine Email mit einem Link zum Testergebnis an die getestete Person verschickt. Um das Testergebnis angezeigt zu bekommen, muss sich die Person durch Eingabe ihres Geburtsdatums authentifizieren. Der Link zu Abholung ist max. 48 Stunden gültig. Bei Vorliegen eines positiven Ergebnis wird das Gesundheitsamt Odenwaldkreis ebenfalls per Email informiert. Die Email an das Gesundheitsamt enthält dabei keine personenbezogenen Daten. 
Die Datensätze zu positiven Testergebnissen werden in eine CSV Datei exportiert, auf die das Gesundheitsamt Zugriff hat.
Für Personen ohne Emailaccount kann das Testzertifikat auch manuell vor Ort erstellt werden. Das Gesundheitsamt wird trotzdem automatisch informiert.
Testergebnisse in Pflege- und Behindertenwohnheimen werden nicht per Email an die getesteten Personen übermittelt. Nach Abschluss des Test kann eine autorisierter Mitarbeiter (Backoffice) die Testzertifikate gesammelt downloaden und diese der Heimleitung übermitteln (Diese Übermittlung ist von dem hier beschriebenen IT System unabhängig, die Vertraulichkeit der persönlichen Daten der Betroffenen muss jedoch auch während dieser Übermittelung gewährleistet sein).

### Zugriffsrechte in der Webapp
In der Webapp des Testzentrums gibt es einen öffentlichen und einen internen Bereich. Aus dem öffentlichen Bereit sind einzig die Online Buchung von Terminen für einen Coronaschnelltest und Abrufen von generellen Informationen zum Schnelltest möglich.
Die Nutzung des internen Bereichs der Webapp ist nur Mitarbeitern des Testzentrum und des Gesundheitsamt möglich. Der Zugang ist Passwort geschützt. Im internen Bereich der Webapp existieren weiter folgende Rollen mit unterschiedlichen Berechtigungen:
- Mitarbeiter des Testzentrum: Können Datensätze anlegen und korrigieren; durch Scannen eines QR Codes, der aktuell in Benutzung ist, Testergebnisse eintragen. Mitarbeiter des Testzentrum können nur Vorgänge des aktuellen Tages im jeweiligen Testzentrum einsehen.
- Mitarbeiter des Gesundheitsamt: Können Datensätze von positiv getesteten Personen als CSV exportieren
- Backoffice: Kann alle Vorgänge in der Datenbank einsehen, Kann Datensätze korrigieren, Anlegen von Teststationen und Terminen, Upload von CSVs mit Datensätzen für Sammeltests, Download von Testergebnissen von Sammeltests.
- Gruppenleitung Testzentrum: Alle Funktionen von Mitarbeiter des Testzentrum. Kann zusätzlich alle Vorgänge des jeweiligen Testzentrums einsehen. 
- Administrator: Alle Funktionen freigeschaltet.
Jede Nacht erstellt die Webapp eine Übersicht über die Anzahl der Tests und die Positivrate. Diese Übersicht enthält keinerlei personenbezogene Daten.

### Speicherdauer von personenbezogenen Daten 
Die Datensätze von negativ getesteten Personen werden nach 48 Stunden aus der Datenbank gelöscht. Die Datensätze von positiv getesteten Personen werden für 3 Monate gespeichert. Die Speicherdauer von Daten aus Voranmeldungen ist wie oben beschrieben.
Zum Zweck der Abrechnung werden die einzelnen Testvorgänge nach Löschung der personenbezogenen Daten archiviert. Die Archivierung beinhaltet lediglich die laufende Nummer der Tests, Testort, Testzeitpunkt und den für den Test verwendete Testkarte. Es werden keine personenbezogene Daten und keine Testergebnisse archiviert.

### Anbindung an die Corona-Warn-App
Auf Wunsch der zu testenden Person kann das Testergebnis zusätzlich auch in die Corona-Warn-App (CWA) des Robert-Koch-Instituts übermittelt werden. Bei der Voranmeldung in der Webapp oder vor Ort muss dafür explizit zugestimmt werden. Stimmt eine Person der Übermittlung des Ergebnisses in die CWA zu, wird ein UUID4 Identifier zufällig erzeugt (122 zufällige Bits). Aus UUID4 Identifier, Name, Geburtsdatum und Testzeitpunkt wird ein QR Code generiert. Durch Scan des QR Codes werden die Daten in die CWA übermittelt.  Der UUID4 Identifier wird dem Datensatz der zu testenden Person in unserer internen Datenbank hinzugefügt.
Sobald das Testergebnis feststeht wird das Ergebnis zusammen mit dem SHA256 Hashwert der UUID4 der Person an die CWA Server übermittelt. Die Verbindung zum CWA Server ist per mTLS verschlüsselt. Die Person kann dann mit Hilfe der App ihr Ergebnis vom CWA Server abholen.
Das Übermittlungsverfahren der Daten an CWA und CWA Server ist dabei von den Entwicklern der CWA (SAP und Deutsche Telekom) vorgegeben. Die CWA Server werden von T-Systems betrieben. Zu weiteren Details der CWA, wie Speicherfristen verweisen wir auf die Datenschutzfolgeabschätzung der CWA des Robert-Koch-Instituts: https://www.coronawarn.app/assets/documents/cwa-datenschutz-folgenabschaetzung.pdf

## 3. Bewertung der Gefahren für die Rechtsgüter betroffener Personen
Als wesentliche Gefahren beim Einsatz des hier beschriebenen Testsystems lassen sich der Verlust der gespeicherten Daten, unautorisierter Abruf der personenbezogenen Daten sowie unautorisierte Veränderungen der personenbezogenen Daten feststellen. Verfahren zur Gefahrenvermeidung und Sicherheitsvorkehrungen werden im nächsten Abschnitt erläutert.

## 4. Sicherheitsmaßnahmen 
Die zentrale Datenbank wir auf einem virtuellen Server in einem kommerziellen Rechenzentrum vorgehalten und ist daher durch den Betreiber des Rechenzentrums vor Datenverlust geschützt.
Die zentrale Datenbank, und alle Komponenten der Webapplikation liegen auf Servern der Firma Contabo GmbH. Die Server befinden sich in Deutschland und mit der Firma Contabo wurde eine Vereinbarung zur Auftragsdatenverarbeitung geschlossen. Zum Server haben nur die Administratoren der Webapp Zugang, auch Mitarbeiter der Firma Contabo haben keinen Zugriff. Alle Kommunikation mit der Webapp und der zentralen Datenbank sind TSL verschlüsselt. Alle internen Komponenten der Webapplikation sind passwortgeschützt und können nur von autorisierten Mitarbeitern aufgerufen werden. Passwörter werden nicht im Klartext sondern nur als Hashwert gespeichert. Die verwendete Software zum Betrieb von Server, Datenbank und Webapp wird von den Administratoren auf dem neuesten Stand gehalten. Somit ist ein ausreichender Schutz gegen unautorisierte Zugriffe gewährleistet. Der Emailserver wird von der Firma domainfactory GmbH betrieben. Auch zu diesem Server haben nur die Administratoren der Webapp Zugang. Eine Vereinbarung zur Auftragsdatenverarbeitung wurde geschlossen. Die Links zum Abrufen von Testergebnissen bestehen aus einer langen, zufälligen Zeichenfolge. Weiter muss sich die Person zum Abruf des Testergebnis noch durch ihr Geburtsdatum authentifizieren. Nach zehn falschen Eingaben wird der Link ungültig.
Nur authentifizierte Mitarbeiter des Testzentrum können Testergebnisse eintragen oder bestehende Datensätze ändern.

## 5. Bewertung der Notwendigkeit und Verhältnismäßigkeit der Vorgänge
Die Rechtsgrundlage zur Erfassung und Verarbeitung der personenbezogenen Daten ist Art. 9 Abs. 2 lit. i DSGVO i.V.m. § 9 Abs. 1 IfSG und weiterhin §7,8 Infektionsschutzgesetz, da es sich bei SARS-COV2 ein Krankeitserreger mit namentlicher Meldepflicht handelt. Durch die elektronische Verarbeitung der Daten ergeben sich entscheidende Vorteile:
- Minimierung der Verwechselungsgefahr (Sie Abschnitt 2)
- Schnellere Erfassung von Daten und Ergebnissen und dadurch höherer Durchsatz in der Teststation
- Schnellere und einfachere Übermittlung von Testergebnissen an Testperson und ggf. Gesundheitsamt

Die mit Abstand schwierigste Abwägung betrifft den Versand des Testergebnis per Email. Emailversand ist im Allgemeinen nicht verschlüsselt und der Link zum Testergebnis kann unter Umständen während der Übermittelung der Email von Dritten eingesehen werden.  Als zusätzliche Schutzmaßnahme muss sich die betreffende Person durch Eingabe ihres Geburtsdatums authentifizieren.  Die Authentifizierung durch Geburtsdatum für die Abholung des Testergebnis ist nicht optimal, besser wäre ein zufälliges und stärkeres Passwort. Dazu müsste dann allerdings die Infrastruktur zum Drucken der Passwörter an jedem Testzentrum vorgehalten werden. Weiter darf auch der Fall von verlorenen Passwörtern nicht vernachlässigt werden. Das Problem der Passwortstärke wird dadurch behoben das der Link nach zehnmaliger Eingabe eines falschen Geburtsdatums ungültig wird. Dadurch wird das Risiko eines nicht-authentifizierten Zugriffs stark minimiert. Dazu müsste ein Angreifer die Email mit Link abfangen und das Geburtsdatum der betreffenden Person kennen. Diese Szenario kann zwar nicht vollkommen ausgeschlossen werden, wir halten es aber für genügend unwahrscheinlich.
Nach Abwägung der oben genannten Vorteile gegen die von uns als minimal angesehenen Gefahren für Rechtsgüter der betroffenen Personen bewerten wir den Einsatz einer IT gestützten Lösung zur Erfassung von Testergebnissen als verhältnismäßig.
