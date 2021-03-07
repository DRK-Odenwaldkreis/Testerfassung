# Testerfassung
Eine Lösung zur Erfassung und Verarbeitung der Testergebnisse in den Testzentren des Odenwaldkreises.



# Komponenten

Das Projekt besteht aus mehreren Teilanwendungen. Vor Ort gibt es ein zwei Endgeräte die im Browser angemeldet sind. Ein Mitarbeiter registriert die Personen die getestet werden möchten. 


## Getting Started:


## Ablauf

### Testperson Kommt

### Testauswerteperson trägt Ergebnis ein


## MySQL Datenbank

Für die Persistierung wird eine MySQL Datenbank verwendet. Diese kann sowohl von einem Dienstleister in einem Rechenzentrum betrieben werden, als auch lokal auf dem Rechner des Terminals laufen. Je nachdem aus welchen Netzwerken Zugang zur Webapplikation benötigt würde, ist dies dementsprechend zu planen.

Für die Verwendung werden aktuelle zwei Tabellen benötigt. 
Einmal "Teststation", welche die Metainformationen zu der Teststation enthält. 
Darüberhinaus einmal "Vorgang", in welche die Testvorgänge gespeichert werden.

Erzeugt werden können die Tabellen mit folgenden SQL statements:

Vorgang:
```mysql
CREATE TABLE `Vorgang` (
  `id` int(11) NOT NULL,
  `Teststation` int(11) NOT NULL,
  `Token` int(11) NOT NULL,
  `Registrierungszeitpunkt` datetime NOT NULL DEFAULT current_timestamp(),
  `Ergebniszeitpunkt` datetime NOT NULL,
  `Nachname` varchar(100) NOT NULL,
  `Vorname` varchar(100) NOT NULL,
  `Adresse` varchar(150) NOT NULL,
  `Telefon` varchar(15) NOT NULL,
  `Mailadresse` varchar(50) NOT NULL,
  `Ergebnis` int(11) NOT NULL,
  `Mailsend` tinyint(4) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Vorgang`
  ADD UNIQUE KEY `id` (`id`);
  
ALTER TABLE `Vorgang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
```


Teststation:

```CREATE TABLE `Teststation` (
  `id` int(11) NOT NULL,
  `Ort` varchar(100) NOT NULL,
  `Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `Teststation`
  ADD KEY `id` (`id`);
  
ALTER TABLE `Teststation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
```

## Webpage

