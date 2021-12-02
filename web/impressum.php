<!doctype html>

<?php

/* **************

Websystem für das Testzentrum DRK Odenwaldkreis
Author: Marc S. Duchene
March 2021

imprint site

** ************** */



// Include functions
include_once 'menu.php';


// Print html header
echo $GLOBALS['G_html_header'];

// Print html menu
echo $GLOBALS['G_html_menu'];
echo $GLOBALS['G_html_menu2'];

// Print html content part A
echo $GLOBALS['G_html_main_right_a'];



echo '<h3 class="imprint">Impressum und Kontakt</h3>
<a href="https://drk-odenwaldkreis.de/impressum/">Direkt beim DRK Kreisverband Odenwaldkreis e. V.</a>';



if($GLOBALS['FLAG_MODE_MAIN'] == 2) {
    echo '<h3 class="imprint">Rückfragen</h3>
    <p>Bei Fragen zu den Impfmöglichkeiten oder organisatorischen Anfragen:</p>
    <p><a href="https://portal-civ.ekom21.de/civ.public/start.html?oe=00.00.LKOW&mode=cc&cc_key=Impfhotline">Kontaktformular</a></p>
    <p>Service-Hotline <a href="tel:+496062703346">06062 70 33 46</a></p>
    <p>&nbsp;</p>';
  } elseif($GLOBALS['FLAG_MODE_MAIN'] == 1) {
    echo '<h3 class="imprint">Rückfragen</h3>
    <p>Bei Fragen zum Testen oder organisatorischen Anfragen:</p>
    <p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
    <p>&nbsp;</p>';
  } elseif($GLOBALS['FLAG_MODE_MAIN'] == 3) {
    echo '<h3 class="imprint">Rückfragen</h3>
    <p>Bei Fragen zum Testen oder organisatorischen Anfragen:</p>
    <p><a href="mailto:testzentrum@drk-odenwaldkreis.de">testzentrum@drk-odenwaldkreis.de</a></p>
    <p>&nbsp;</p>';
  }


echo '<h3 class="imprint">Technischer Support für die Teams vor Ort und Behörden</h3>
<p><a target="_blank" href="https://github.com/DRK-Odenwaldkreis/Testerfassung">Für die Dokumentation der Web-Anwendung hier klicken</a></p>
<p>&nbsp;</p>
<p>Bei technischen Fragen zum Erfassungssystem bitte eine E-Mail schreiben an den Support:</p>
<p><a href="mailto:info@testzentrum-odenwald.de">info@testzentrum-odenwald.de</a></p>
<p>&nbsp;</p>

<div id="datenschutz_website" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzhinweis bei der Verwendung der Website</h3>


<ol>
<li><strong> Datenschutz auf einen Blick</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p><strong>Allgemeine Hinweise</strong></p>
<p>Die folgenden Hinweise geben einen einfachen &Uuml;berblick dar&uuml;ber, was mit Ihren personenbezogenen Daten passiert, wenn Sie diese Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie pers&ouml;nlich identifiziert werden k&ouml;nnen. Ausf&uuml;hrliche Informationen zum Thema Datenschutz entnehmen Sie unserer unter diesem Text aufgef&uuml;hrten Datenschutzerkl&auml;rung.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Datenerfassung auf dieser Website</strong></p>
<p><strong>&nbsp;</strong></p>
<p><strong>Wer ist verantwortlich f&uuml;r die Datenerfassung auf dieser Website?</strong></p>
<p>Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten k&ouml;nnen Sie dem Abschnitt &bdquo;Hinweis zur Verantwortlichen Stelle&ldquo; in dieser Datenschutzerkl&auml;rung entnehmen.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Wie erfassen wir Ihre Daten?</strong></p>
<p>Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen. Hierbei kann es sich z.&nbsp;B. um Daten handeln, die Sie in ein Kontaktformular eingeben.</p>
<p>Andere Daten werden automatisch oder nach Ihrer Einwilligung beim Besuch der Website durch unsere IT-Systeme erfasst. Das sind vor allem technische Daten (z.&nbsp;B. Internetbrowser, Betriebssystem oder Uhrzeit des Seitenaufrufs). Die Erfassung dieser Daten erfolgt automatisch, sobald Sie diese Website betreten.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Wof&uuml;r nutzen wir Ihre Daten?</strong></p>
<p>Ein Teil der Daten wird erhoben, um eine fehlerfreie Bereitstellung der Website zu gew&auml;hrleisten. Andere Daten k&ouml;nnen zur Analyse Ihres Nutzerverhaltens verwendet werden.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Welche Rechte haben Sie bez&uuml;glich Ihrer Daten?</strong></p>
<p>Sie haben jederzeit das Recht, unentgeltlich Auskunft &uuml;ber Herkunft, Empf&auml;nger und Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten. Sie haben au&szlig;erdem ein Recht, die Berichtigung oder L&ouml;schung dieser Daten zu verlangen. Wenn Sie eine Einwilligung zur Datenverarbeitung erteilt haben, k&ouml;nnen Sie diese Einwilligung jederzeit f&uuml;r die Zukunft widerrufen. Au&szlig;erdem haben Sie das Recht, unter bestimmten Umst&auml;nden die Einschr&auml;nkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen. Des Weiteren steht Ihnen ein Beschwerderecht bei der zust&auml;ndigen Aufsichtsbeh&ouml;rde zu.</p>
<p>Hierzu sowie zu weiteren Fragen zum Thema Datenschutz k&ouml;nnen Sie sich jederzeit an uns wenden.</p>
<p>&nbsp;</p>


<ol start="2">
<li><strong> Hosting</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p><strong>Externes Hosting</strong></p>
<p>Diese Website wird bei einem externen Dienstleister gehostet (Hoster). Die personenbezogenen Daten, die auf dieser Website erfasst werden, werden auf den Servern des Hosters gespeichert. Hierbei kann es sich v. a. um IP-Adressen, Kontaktanfragen, Meta- und Kommunikationsdaten, Vertragsdaten, Kontaktdaten, Namen, Websitezugriffe und sonstige Daten, die &uuml;ber eine Website generiert werden, handeln.</p>
<p>Der Einsatz des Hosters erfolgt zum Zwecke der Vertragserf&uuml;llung gegen&uuml;ber unseren potenziellen und bestehenden Kunden (Art. 6 Abs. 1 lit. b DSGVO) und im Interesse einer sicheren, schnellen und effizienten Bereitstellung unseres Online-Angebots durch einen professionellen Anbieter (Art. 6 Abs. 1 lit. f DSGVO).</p>
<p>Unser Hoster wird Ihre Daten nur insoweit verarbeiten, wie dies zur Erf&uuml;llung seiner Leistungspflichten erforderlich ist und unsere Weisungen in Bezug auf diese Daten befolgen.</p>
<p>&nbsp;</p>
<p>Wir setzen folgenden Hoster ein:</p>
<p>&nbsp;</p>
<p>Contabo GmbH<br />Aschauer Stra&szlig;e 32a<br />81549 M&uuml;nchen<br />Deutschland</p>


<p><strong>&nbsp;</strong></p>
<ol start="3">
<li><strong> Allgemeine Hinweise und Pflicht&shy;informationen</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p><strong>Datenschutz</strong></p>
<p>Die Betreiber dieser Seiten nehmen den Schutz Ihrer pers&ouml;nlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend den gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerkl&auml;rung.</p>
<p>Wenn Sie diese Website benutzen, werden verschiedene personenbezogene Daten erhoben. Personenbezogene Daten sind Daten, mit denen Sie pers&ouml;nlich identifiziert werden k&ouml;nnen. Die vorliegende Datenschutzerkl&auml;rung erl&auml;utert, welche Daten wir erheben und wof&uuml;r wir sie nutzen. Sie erl&auml;utert auch, wie und zu welchem Zweck das geschieht.</p>
<p>Wir weisen darauf hin, dass die Daten&uuml;bertragung im Internet (z.&nbsp;B. bei der Kommunikation per E-Mail) Sicherheitsl&uuml;cken aufweisen kann. Ein l&uuml;ckenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht m&ouml;glich.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Hinweis zur verantwortlichen Stelle</strong></p>
<p>Die verantwortliche Stelle f&uuml;r die Datenverarbeitung auf dieser Website ist:</p>
<p>DRK Kreisverband Odenwaldkreis e.V.<br />Frank Sauer, Vorstand<br />Illigstra&szlig;e 11<br />64711 Erbach</p>
<p>Telefon: 06062 607-0<br />E-Mail: <a href="mailto:info@drk-odenwaldkreis.de">info@drk-odenwaldkreis.de</a></p>
<p>&nbsp;</p>
<p>Verantwortliche Stelle ist die nat&uuml;rliche oder juristische Person, die allein oder gemeinsam mit anderen &uuml;ber die Zwecke und Mittel der Verarbeitung von personenbezogenen Daten (z.&nbsp;B. Namen, E-Mail-Adressen o. &Auml;.) entscheidet.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Speicherdauer</strong></p>
<p>Soweit innerhalb dieser Datenschutzerkl&auml;rung keine speziellere Speicherdauer genannt wurde, verbleiben Ihre personenbezogenen Daten bei uns, bis der Zweck f&uuml;r die Datenverarbeitung entf&auml;llt. Wenn Sie ein berechtigtes L&ouml;schersuchen geltend machen oder eine Einwilligung zur Datenverarbeitung widerrufen, werden Ihre Daten gel&ouml;scht, sofern wir keine anderen rechtlich zul&auml;ssigen Gr&uuml;nde f&uuml;r die Speicherung Ihrer personenbezogenen Daten haben (z.&nbsp;B. steuer- oder handelsrechtliche Aufbewahrungsfristen); im letztgenannten Fall erfolgt die L&ouml;schung nach Fortfall dieser Gr&uuml;nde.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Datenschutz&shy;beauftragter</strong></p>
<p>Wir haben f&uuml;r unser Unternehmen einen Datenschutzbeauftragten bestellt.</p>
<p>Herr Kai Schwardt<br />im Hause DRK Kreisverband Odenwaldkreis e.V.<br />Illigstra&szlig;e 11<br />64711 Erbach</p>
<p>Telefon: 06062 607-0<br />E-Mail: datenschutz@drk-odenwaldkreis.de</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Hinweis zur Datenweitergabe in die USA und sonstige Drittstaaten</strong></p>
<p>Wir verwenden unter anderem Tools von Unternehmen mit Sitz in den USA oder sonstigen datenschutzrechtlich nicht sicheren Drittstaaten. Wenn diese Tools aktiv sind, k&ouml;nnen Ihre personenbezogene Daten in diese Drittstaaten &uuml;bertragen und dort verarbeitet werden. Wir weisen darauf hin, dass in diesen L&auml;ndern kein mit der EU vergleichbares Datenschutzniveau garantiert werden kann. Beispielsweise sind US-Unternehmen dazu verpflichtet, personenbezogene Daten an Sicherheitsbeh&ouml;rden herauszugeben, ohne dass Sie als Betroffener hiergegen gerichtlich vorgehen k&ouml;nnten. Es kann daher nicht ausgeschlossen werden, dass US-Beh&ouml;rden (z.&nbsp;B. Geheimdienste) Ihre auf US-Servern befindlichen Daten zu &Uuml;berwachungszwecken verarbeiten, auswerten und dauerhaft speichern. Wir haben auf diese Verarbeitungst&auml;tigkeiten keinen Einfluss.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerruf Ihrer Einwilligung zur Datenverarbeitung</strong></p>
<p>Viele Datenverarbeitungsvorg&auml;nge sind nur mit Ihrer ausdr&uuml;cklichen Einwilligung m&ouml;glich. Sie k&ouml;nnen eine bereits erteilte Einwilligung jederzeit widerrufen. Die Rechtm&auml;&szlig;igkeit der bis zum Widerruf erfolgten Datenverarbeitung bleibt vom Widerruf unber&uuml;hrt.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerspruchsrecht gegen die Datenerhebung in besonderen F&auml;llen sowie gegen Direktwerbung (Art. 21 DSGVO)</strong></p>
<p>WENN DIE DATENVERARBEITUNG AUF GRUNDLAGE VON ART. 6 ABS. 1 LIT. E ODER F DSGVO ERFOLGT, HABEN SIE JEDERZEIT DAS RECHT, AUS GR&Uuml;NDEN, DIE SICH AUS IHRER BESONDEREN SITUATION ERGEBEN, GEGEN DIE VERARBEITUNG IHRER PERSONENBEZOGENEN DATEN WIDERSPRUCH EINZULEGEN; DIES GILT AUCH F&Uuml;R EIN AUF DIESE BESTIMMUNGEN GEST&Uuml;TZTES PROFILING. DIE JEWEILIGE RECHTSGRUNDLAGE, AUF DENEN EINE VERARBEITUNG BERUHT, ENTNEHMEN SIE DIESER DATENSCHUTZERKL&Auml;RUNG. WENN SIE WIDERSPRUCH EINLEGEN, WERDEN WIR IHRE BETROFFENEN PERSONENBEZOGENEN DATEN NICHT MEHR VERARBEITEN, ES SEI DENN, WIR K&Ouml;NNEN ZWINGENDE SCHUTZW&Uuml;RDIGE GR&Uuml;NDE F&Uuml;R DIE VERARBEITUNG NACHWEISEN, DIE IHRE INTERESSEN, RECHTE UND FREIHEITEN &Uuml;BERWIEGEN ODER DIE VERARBEITUNG DIENT DER GELTENDMACHUNG, AUS&Uuml;BUNG ODER VERTEIDIGUNG VON RECHTSANSPR&Uuml;CHEN (WIDERSPRUCH NACH ART. 21 ABS. 1 DSGVO).</p>
<p>WERDEN IHRE PERSONENBEZOGENEN DATEN VERARBEITET, UM DIREKTWERBUNG ZU BETREIBEN, SO HABEN SIE DAS RECHT, JEDERZEIT WIDERSPRUCH GEGEN DIE VERARBEITUNG SIE BETREFFENDER PERSONENBEZOGENER DATEN ZUM ZWECKE DERARTIGER WERBUNG EINZULEGEN; DIES GILT AUCH F&Uuml;R DAS PROFILING, SOWEIT ES MIT SOLCHER DIREKTWERBUNG IN VERBINDUNG STEHT. WENN SIE WIDERSPRECHEN, WERDEN IHRE PERSONENBEZOGENEN DATEN ANSCHLIESSEND NICHT MEHR ZUM ZWECKE DER DIREKTWERBUNG VERWENDET (WIDERSPRUCH NACH ART. 21 ABS. 2 DSGVO).</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Beschwerde&shy;recht bei der zust&auml;ndigen Aufsichts&shy;beh&ouml;rde</strong></p>
<p>Im Falle von Verst&ouml;&szlig;en gegen die DSGVO steht den Betroffenen ein Beschwerderecht bei einer Aufsichtsbeh&ouml;rde, insbesondere in dem Mitgliedstaat ihres gew&ouml;hnlichen Aufenthalts, ihres Arbeitsplatzes oder des Orts des mutma&szlig;lichen Versto&szlig;es zu. Das Beschwerderecht besteht unbeschadet anderweitiger verwaltungsrechtlicher oder gerichtlicher Rechtsbehelfe.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Recht auf Daten&shy;&uuml;bertrag&shy;barkeit</strong></p>
<p>Sie haben das Recht, Daten, die wir auf Grundlage Ihrer Einwilligung oder in Erf&uuml;llung eines Vertrags automatisiert verarbeiten, an sich oder an einen Dritten in einem g&auml;ngigen, maschinenlesbaren Format aush&auml;ndigen zu lassen. Sofern Sie die direkte &Uuml;bertragung der Daten an einen anderen Verantwortlichen verlangen, erfolgt dies nur, soweit es technisch machbar ist.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>SSL- bzw. TLS-Verschl&uuml;sselung</strong></p>
<p>Diese Seite nutzt aus Sicherheitsgr&uuml;nden und zum Schutz der &Uuml;bertragung vertraulicher Inhalte, wie zum Beispiel Bestellungen oder Anfragen, die Sie an uns als Seitenbetreiber senden, eine SSL- bzw. TLS-Verschl&uuml;sselung. Eine verschl&uuml;sselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von &bdquo;http://&ldquo; auf &bdquo;https://&ldquo; wechselt und an dem Schloss-Symbol in Ihrer Browserzeile.</p>
<p>Wenn die SSL- bzw. TLS-Verschl&uuml;sselung aktiviert ist, k&ouml;nnen die Daten, die Sie an uns &uuml;bermitteln, nicht von Dritten mitgelesen werden.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Auskunft, L&ouml;schung und Berichtigung</strong></p>
<p>Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf unentgeltliche Auskunft &uuml;ber Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empf&auml;nger und den Zweck der Datenverarbeitung und ggf. ein Recht auf Berichtigung oder L&ouml;schung dieser Daten. Hierzu sowie zu weiteren Fragen zum Thema personenbezogene Daten k&ouml;nnen Sie sich jederzeit an uns wenden.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Recht auf Einschr&auml;nkung der Verarbeitung</strong></p>
<p>Sie haben das Recht, die Einschr&auml;nkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen. Hierzu k&ouml;nnen Sie sich jederzeit an uns wenden. Das Recht auf Einschr&auml;nkung der Verarbeitung besteht in folgenden F&auml;llen:</p>
<ul>
<li>Wenn Sie die Richtigkeit Ihrer bei uns gespeicherten personenbezogenen Daten bestreiten, ben&ouml;tigen wir in der Regel Zeit, um dies zu &uuml;berpr&uuml;fen. F&uuml;r die Dauer der Pr&uuml;fung haben Sie das Recht, die Einschr&auml;nkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.</li>
<li>Wenn die Verarbeitung Ihrer personenbezogenen Daten unrechtm&auml;&szlig;ig geschah/geschieht, k&ouml;nnen Sie statt der L&ouml;schung die Einschr&auml;nkung der Datenverarbeitung verlangen.</li>
<li>Wenn wir Ihre personenbezogenen Daten nicht mehr ben&ouml;tigen, Sie sie jedoch zur Aus&uuml;bung, Verteidigung oder Geltendmachung von Rechtsanspr&uuml;chen ben&ouml;tigen, haben Sie das Recht, statt der L&ouml;schung die Einschr&auml;nkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.</li>
<li>Wenn Sie einen Widerspruch nach Art. 21 Abs. 1 DSGVO eingelegt haben, muss eine Abw&auml;gung zwischen Ihren und unseren Interessen vorgenommen werden. Solange noch nicht feststeht, wessen Interessen &uuml;berwiegen, haben Sie das Recht, die Einschr&auml;nkung der Verarbeitung Ihrer personenbezogenen Daten zu verlangen.</li>
</ul>
<p>Wenn Sie die Verarbeitung Ihrer personenbezogenen Daten eingeschr&auml;nkt haben, d&uuml;rfen diese Daten &ndash; von ihrer Speicherung abgesehen &ndash; nur mit Ihrer Einwilligung oder zur Geltendmachung, Aus&uuml;bung oder Verteidigung von Rechtsanspr&uuml;chen oder zum Schutz der Rechte einer anderen nat&uuml;rlichen oder juristischen Person oder aus Gr&uuml;nden eines wichtigen &ouml;ffentlichen Interesses der Europ&auml;ischen Union oder eines Mitgliedstaats verarbeitet werden.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerspruch gegen Werbe-E-Mails</strong></p>
<p>Der Nutzung von im Rahmen der Impressumspflicht ver&ouml;ffentlichten Kontaktdaten zur &Uuml;bersendung von nicht ausdr&uuml;cklich angeforderter Werbung und Informationsmaterialien wird hiermit widersprochen. Die Betreiber der Seiten behalten sich ausdr&uuml;cklich rechtliche Schritte im Falle der unverlangten Zusendung von Werbeinformationen, etwa durch Spam-E-Mails, vor.</p>



<p><strong>&nbsp;</strong></p>
<ol start="4">
<li><strong>Datenverarbeitung bei Anmeldung zu einem Corona-Schnelltest / PCR-Test</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p>Informationen zur Datenverarbeitung im Rahmen Ihrer Anmeldung / Durchführung eines Corona Schnelltests entnehmen Sie bitte der gesonderten Datenschutzinformation gemäß DSVGO Art.  13.</p>
<p>
<a href="impressum.php#datenschutz_durchfuehrung_impf">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der Durchführung von Corona Schnelltests/PCR-Test</a></p>

<p><strong>&nbsp;</strong></p>
<ol start="5">
<li><strong>Datenverarbeitung bei Anmeldung zu einer Corona-Impfung</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p>Informationen zur Datenverarbeitung im Rahmen Ihrer Anmeldung / Durchführung einer Corona Impfung entnehmen Sie bitte der gesonderten Datenschutzinformation gemäß DSVGO Art. 13.</p>
<p>
<a href="impressum.php#datenschutz_durchfuehrung_agtest">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der im Rahmen der Anmeldung und Terminkoordination von Corona-Impfungen</a></p>

<p><strong>&nbsp;</strong></p>
<ol start="6">
<li><strong>Datenverarbeitung bei Anmeldung zu einem Antikörpertest</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p>Informationen zur Datenverarbeitung im Rahmen Ihrer Anmeldung / Durchführung einer Corona Impfung entnehmen Sie bitte der gesonderten Datenschutzinformation gemäß DSVGO Art.  13.</p>
<p>
<a href="impressum.php#datenschutz_durchfuehrung_antikoerpertest">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der im Rahmen der Anmeldung und Terminkoordination von Corona Antikörpertests</a></p>



<p><strong>&nbsp;</strong></p>
<ol start="7">
<li><strong> Datenerfassung auf dieser Website</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p><strong>Cookies</strong></p>
<p>Unsere Internetseiten verwenden so genannte &bdquo;Cookies&ldquo;. Cookies sind kleine Textdateien und richten auf Ihrem Endger&auml;t keinen Schaden an. Sie werden entweder vor&uuml;bergehend f&uuml;r die Dauer einer Sitzung (Session-Cookies) oder dauerhaft (permanente Cookies) auf Ihrem Endger&auml;t gespeichert. Session-Cookies werden nach Ende Ihres Besuchs automatisch gel&ouml;scht. Permanente Cookies bleiben auf Ihrem Endger&auml;t gespeichert, bis Sie diese selbst l&ouml;schen&nbsp;oder eine automatische L&ouml;schung durch Ihren Webbrowser erfolgt.</p>
<p>Teilweise k&ouml;nnen auch Cookies von Drittunternehmen auf Ihrem Endger&auml;t gespeichert werden, wenn Sie unsere Seite betreten (Third-Party-Cookies). Diese erm&ouml;glichen uns oder Ihnen die Nutzung bestimmter Dienstleistungen des Drittunternehmens (z.&nbsp;B. Cookies zur Abwicklung von Zahlungsdienstleistungen).</p>
<p>Cookies haben verschiedene Funktionen. Zahlreiche Cookies sind technisch notwendig, da bestimmte Websitefunktionen ohne diese nicht funktionieren w&uuml;rden (z.&nbsp;B. die Warenkorbfunktion oder die Anzeige von Videos). Andere Cookies dienen dazu, das Nutzerverhalten auszuwerten&nbsp;oder Werbung anzuzeigen.</p>
<p>Cookies, die zur Durchf&uuml;hrung des elektronischen Kommunikationsvorgangs (notwendige Cookies) oder zur Bereitstellung bestimmter, von Ihnen erw&uuml;nschter Funktionen (funktionale Cookies, z.&nbsp;B. f&uuml;r die Warenkorbfunktion) oder zur Optimierung der Website (z.&nbsp;B. Cookies zur Messung des Webpublikums) erforderlich sind, werden auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO gespeichert, sofern keine andere Rechtsgrundlage angegeben wird. Der Websitebetreiber hat ein berechtigtes Interesse an der Speicherung von Cookies zur technisch fehlerfreien und optimierten Bereitstellung seiner Dienste. Sofern eine Einwilligung zur Speicherung von Cookies abgefragt wurde, erfolgt die Speicherung der betreffenden Cookies ausschlie&szlig;lich auf Grundlage dieser Einwilligung (Art. 6 Abs. 1 lit. a DSGVO); die Einwilligung ist jederzeit widerrufbar.</p>
<p>Sie k&ouml;nnen Ihren Browser so einstellen, dass Sie &uuml;ber das Setzen von Cookies informiert werden und Cookies nur im Einzelfall erlauben, die Annahme von Cookies f&uuml;r bestimmte F&auml;lle oder generell ausschlie&szlig;en sowie das automatische L&ouml;schen der Cookies beim Schlie&szlig;en des Browsers aktivieren. Bei der Deaktivierung von Cookies kann die Funktionalit&auml;t dieser Website eingeschr&auml;nkt sein.</p>
<p>Soweit Cookies von Drittunternehmen oder zu Analysezwecken eingesetzt werden, werden wir Sie hier&uuml;ber im Rahmen dieser Datenschutzerkl&auml;rung gesondert informieren und ggf. eine Einwilligung abfragen.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Server-Log-Dateien</strong></p>
<p>Der Provider der Seiten erhebt und speichert automatisch Informationen in so genannten Server-Log-Dateien, die Ihr Browser automatisch an uns &uuml;bermittelt. Dies sind:</p>
<ul>
<li>Browsertyp und Browserversion</li>
<li>verwendetes Betriebssystem</li>
<li>Referrer URL</li>
<li>Hostname des zugreifenden Rechners</li>
<li>Uhrzeit der Serveranfrage</li>
<li>IP-Adresse</li>
</ul>
<p>Eine Zusammenf&uuml;hrung dieser Daten mit anderen Datenquellen wird nicht vorgenommen.</p>
<p>Die Erfassung dieser Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO. Der Websitebetreiber hat ein berechtigtes Interesse an der technisch fehlerfreien Darstellung und der Optimierung seiner Website &ndash; hierzu m&uuml;ssen die Server-Log-Files erfasst werden.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Anfrage per E-Mail, Telefon oder Telefax</strong></p>
<p>Wenn Sie uns per E-Mail, Telefon oder Telefax kontaktieren, wird Ihre Anfrage inklusive aller daraus hervorgehenden personenbezogenen Daten (Name, Anfrage) zum Zwecke der Bearbeitung Ihres Anliegens bei uns gespeichert und verarbeitet. Diese Daten geben wir nicht ohne Ihre Einwilligung weiter.</p>
<p>Die Verarbeitung dieser Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. b DSGVO, sofern Ihre Anfrage mit der Erf&uuml;llung eines Vertrags zusammenh&auml;ngt oder zur Durchf&uuml;hrung vorvertraglicher Ma&szlig;nahmen erforderlich ist. In allen &uuml;brigen F&auml;llen beruht die Verarbeitung auf unserem berechtigten Interesse an der effektiven Bearbeitung der an uns gerichteten Anfragen (Art. 6 Abs. 1 lit. f DSGVO) oder auf Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO) sofern diese abgefragt wurde.</p>
<p>Die von Ihnen an uns per Kontaktanfragen &uuml;bersandten Daten verbleiben bei uns, bis Sie uns zur L&ouml;schung auffordern, Ihre Einwilligung zur Speicherung widerrufen oder der Zweck f&uuml;r die Datenspeicherung entf&auml;llt (z.&nbsp;B. nach abgeschlossener Bearbeitung Ihres Anliegens). Zwingende gesetzliche Bestimmungen &ndash; insbesondere gesetzliche Aufbewahrungsfristen &ndash; bleiben unber&uuml;hrt.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Registrierung auf dieser Website</strong></p>
<p>Sie k&ouml;nnen sich auf dieser Website registrieren, um zus&auml;tzliche Funktionen auf der Seite zu nutzen. Die dazu eingegebenen Daten verwenden wir nur zum Zwecke der Nutzung des jeweiligen Angebotes oder Dienstes, f&uuml;r den Sie sich registriert haben. Die bei der Registrierung abgefragten Pflichtangaben m&uuml;ssen vollst&auml;ndig angegeben werden. Anderenfalls werden wir die Registrierung ablehnen.</p>
<p>F&uuml;r wichtige &Auml;nderungen etwa beim Angebotsumfang oder bei technisch notwendigen &Auml;nderungen nutzen wir die bei der Registrierung angegebene E-Mail-Adresse, um Sie auf diesem Wege zu informieren.</p>
<p>Die Verarbeitung der bei der Registrierung eingegebenen Daten erfolgt zum Zwecke der Durchf&uuml;hrung des durch die Registrierung begr&uuml;ndeten Nutzungsverh&auml;ltnisses und ggf. zur Anbahnung weiterer Vertr&auml;ge (Art. 6 Abs. 1 lit. b DSGVO).</p>
<p>Die bei der Registrierung erfassten Daten werden von uns gespeichert, solange Sie auf dieser Website registriert sind und werden anschlie&szlig;end gel&ouml;scht. Gesetzliche Aufbewahrungsfristen bleiben unber&uuml;hrt.</p>


<p><strong>&nbsp;</strong></p>
<ol start="8">
<li><strong> Plugins und Tools</strong></li>
</ol>
<p><strong>&nbsp;</strong></p>
<p><strong>Google Web Fonts</strong></p>
<p>Diese Seite nutzt zur einheitlichen Darstellung von Schriftarten so genannte Web Fonts, die von Google bereitgestellt werden. Beim Aufruf einer Seite l&auml;dt Ihr Browser die ben&ouml;tigten Web Fonts in ihren Browsercache, um Texte und Schriftarten korrekt anzuzeigen.</p>
<p>Zu diesem Zweck muss der von Ihnen verwendete Browser Verbindung zu den Servern von Google aufnehmen. Hierdurch erlangt Google Kenntnis dar&uuml;ber, dass &uuml;ber Ihre IP-Adresse diese Website aufgerufen wurde. Die Nutzung von Google WebFonts erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO. Der Websitebetreiber hat ein berechtigtes Interesse an der einheitlichen Darstellung des Schriftbildes auf seiner Website. Sofern eine entsprechende Einwilligung abgefragt wurde (z.&nbsp;B. eine Einwilligung zur Speicherung von Cookies), erfolgt die Verarbeitung ausschlie&szlig;lich auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO; die Einwilligung ist jederzeit widerrufbar.</p>
<p>Wenn Ihr Browser Web Fonts nicht unterst&uuml;tzt, wird eine Standardschrift von Ihrem Computer genutzt.</p>
<p>Weitere Informationen zu Google Web Fonts finden Sie unter&nbsp;<a href="https://developers.google.com/fonts/faq">https://developers.google.com/fonts/faq</a>&nbsp;und in der Datenschutzerkl&auml;rung von Google:&nbsp;<a href="https://policies.google.com/privacy?hl=de">https://policies.google.com/privacy?hl=de</a>.</p>

<div class="FAIRsepdown"></div>







<div id="datenschutz_durchfuehrung_impf" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der Anmeldung und Terminkoordination von <b>Corona Impfungen</b></h3>
<p>Gemäß den Vorgaben der Art. 13 ff der Datenschutz-Grundverordnung (DSGVO) informieren wir Sie hiermit über die Verarbeitung der über Sie erhobenen und verarbeiteten personenbezogenen Daten im Rahmen Ihrer Anmeldung und der Durchführung von Corona Schnelltests in den Einrichtungen des Deutschen Roten Kreuz Kreisverband e.V. </p>
<p>Die datenschutzrechtliche Verantwortlichkeit im Rahmen der Durchführung der Impfungen liegt bei den Landkreisen und kreisfreien Städten. Diese sind gem. § 2 Abs. 1 Hessisches Gesetz über den öffentlichen Gesundheitsdienst (HGöGD) Träger des öffentlichen Gesundheitsdienstes und haben im Rahmen dieser Aufgabe über Mittel und Zwecke der Verarbeitung personenbezogener Daten im Sinne des Art. 4 Nr. 7 Datenschutz-Grundverordnung (DS-GVO) bei Maßnahmen im Rahmen der Impfmaßnahmen zu entscheiden. Die entsprechende Datenschutzinformation des Odenwaldkreises finden Sie auf folgendem Link: <a href="https://corona.odenwaldkreis.de/wp-content/uploads/2021/10/Datenschutz-Info-Stand-15.10.2021.pdf">https://corona.odenwaldkreis.de/wp-content/uploads/2021/10/Datenschutz-Info-Stand-15.10.2021.pdf</a> Dieser Link kann sich ggf. in der Zukunft ändern.</p>
<p>Das Deutsche Rote Kreuz Kreisverband Odenwaldkreis e.V. ist ausschließlich für die Verarbeitung personenbezogener Daten im Rahmen der Anmeldung und der Terminkoordination verantwortlich.
Nur diese Verarbeitung personenbezogener Daten wird in der vorliegenden Datenschutzinformation beschrieben.</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Verantwortlich im Sinne der DSGVO Art. 4 Nr. 7</strong></p>
<p>Name: Frank Sauer</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: info@drk-odenwaldkreis.de</p>
<p>Website: http://www.drk-odenwaldkreis.de </p>
<p>Unternehmensbezeichnung: Deutsches Rotes Kreuz Kreisverband Odenwaldkreis e.V.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kontaktdaten unseres Datenschutzbeauftragten</strong></p>
<p>Name: Herr Kai Schwardt</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: datenschutz@drk-odenwaldkreis.de</p>
<p>Unternehmensbezeichnung: Extern benannter Datenschutzbeauftragter</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Zweck und Rechtsgrundlage der Verarbeitung</strong></p>
<p>Wir verarbeiten Ihre personenbezogenen Daten im Einklang mit den Bestimmungen der europäischen Datenschutz-Grundverordnung (EU-DSGVO), soweit dies für die Anmeldung und Terminkoordination in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. erforderlich ist. Rechtsgrundlage ist dabei Art. 6 lit. a DSGVO (Ihre Einwilligung).</p>
<p>Weiterhin können wir personenbezogene Daten von Ihnen verarbeiten, sofern dies zur Abwehr
von geltend gemachten Rechtsansprüchen gegen uns erforderlich ist. Rechtsgrundlage ist dabei Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse ist beispielsweise eine Beweispflicht im Rahmen rechtlicher Verfahren. Erteilen Sie uns eine ausdrückliche Einwilligung zur Verarbeitung von personenbezogenen Daten, ist die Rechtmäßigkeit dieser Verarbeitung auf Basis Ihrer Einwilligung nach Art. 6 Abs. 1 lit. a DSGVO gegeben. Eine erteilte Einwilligung kann jederzeit, mit Wirkung für die Zukunft, widerrufen werden (siehe Ziffer 9 dieser Datenschutzinformation).
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kategorie personenbezogener Daten</strong></p>
<p>Wir verarbeiten nur solche Daten, die im Zusammenhang mit der Anmeldung und der Terminkoordination bei der Durchführung von Corona Impfungen in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. stehen. Dies sind im Einzelnen:</p>
<p>Vorname, Nachname, Namenszusätze, Kontaktdaten (etwa private Anschrift, (Mobil-)Telefonnummer, E-Mail-Adresse), gewählter Impfstoff, Status grippeähnliche Symptome.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Quellen der Daten</strong></p>
<p>Die Erhebung Ihrer Daten findet grundsätzlich bei Ihnen selbst in Form Ihrer Anmeldung auf unserer Webseite https://www.impfzentrum-odw.de oder vor Ort in einer unserer Einrichtungen statt. Die Verarbeitung der von Ihnen überlassenen personenbezogenen Daten ist zur Durchführung der Anmeldung und der Terminkoordination notwendig. 
</p>
<p>Die Bereitstellung Ihrer personenbezogenen Daten ist notwendig. Sollten die erforderlichen personen-bezogenen Daten nicht von Ihnen bereitgestellt werden, kann keine Anmeldung zur Impfung durchgeführt werden.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Empfänger der Daten</strong></p>
<p>Wir geben Ihre personenbezogenen Daten innerhalb unserer Organisation ausschließlich an die Personen weiter, die diese Daten zur Anmeldung und der Terminkoordination der Corona Impfungen benötigen.</p>
<p>Ihre personenbezogenen Daten werden in unserem Auftrag ggf. auf Basis von Auftragsverarbeitungs-verträgen nach Art. 28 DSGVO verarbeitet. In diesen Fällen stellen wir sicher, dass die Verarbeitung von personenbezogenen Daten im Einklang mit den Bestimmungen der DSGVO erfolgt. Die Kategorien von Empfängern sind in diesem Fall z.B. Anbieter von Internetdienstanbieter sowie Anbieter von Software zur Administration der Terminverwaltung.</p>
<p>Eine Datenweitergabe an Empfänger außerhalb unserer Organisation erfolgt ansonsten nur, soweit gesetzliche Bestimmungen dies erlauben oder gebieten.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Übermittlung in ein Drittland</strong></p>
<p>Eine Übermittlung der Daten in ein Drittland erfolgt nicht. Jegliche Verarbeitung personenbezogener Daten erfolgt innerhalb der der Europäischen Union, oder des Europäischen Wirtschaftsraumes. </p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Dauer der Speicherung</strong></p>
<p>Ihre personenbezogenen Daten werden nach dem Grundsatz der Speicherbegrenzung (Art. 5 Abs. 1 lit. e DS-GVO) nur so lange gespeichert, wie es die jeweiligen Zwecke der Datenverarbeitung erfordern. Ihre personenbezogenen Daten werden daher grundsätzlich frühestmöglich gelöscht bzw. vernichtet. Gemäß aktueller Erforderlichkeit erfolgt die Löschung Ihrer personenbezogenen Daten am Folgetag des Impftermins. 
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Ihre Rechte</strong></p>
<p>Jede betroffene Person hat das Recht auf Auskunft nach Art. 15 DSGVO, das Recht auf
Berichtigung nach Art. 16 DSGVO, das Recht auf Löschung nach Art. 17 DSGVO, das Recht auf
Einschränkung der Verarbeitung nach Art. 18 DSGVO, das Recht auf Mitteilung nach
Art. 19 DSGVO sowie das Recht auf Datenübertragbarkeit nach Art. 20 DSGVO.
</p>
<p>Darüber hinaus besteht ein Beschwerderecht bei einer Datenschutzaufsichtsbehörde nach
Art. 77 DSGVO, wenn Sie der Ansicht sind, dass die Verarbeitung Ihrer personenbezogenen
Daten nicht rechtmäßig erfolgt. Das Beschwerderecht besteht unbeschadet eines
anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs.
</p>
<p>Die zuständige Datenschutzaufsichtsbehörde erreichen Sie unter folgenden Kontaktdaten:</p>
<p>&nbsp;</p>
<p>Der Hessische Beauftragte für Datenschutz und Informationsfreiheit</p>
<p>Postfach 3163</p>
<p>65021 Wiesbaden</p>
<p>Telefon: +49 611 1408 – 0</p>
<p>Telefax: +49 611 1408 – 900</p>
<p>Mail: poststelle@datenschutz.hessen.de</p>
<p>https://datenschutz.hessen.de</p>
<p>&nbsp;</p>
<p>Sofern die Verarbeitung von Daten auf Grundlage Ihrer Einwilligung erfolgt, sind Sie nach Art. 7 DSGVO berechtigt, die Einwilligung in die Verwendung Ihrer personenbezogenen Daten jederzeit zu widerrufen. Bitte beachten Sie, dass der Widerruf erst für die Zukunft wirkt. Verarbeitungen, die vor dem Widerruf erfolgt sind, sind davon nicht betroffen. Bitte beachten Sie zudem, dass wir bestimmte Daten für die Erfüllung gesetzlicher Vorgaben ggf. für einen bestimmten Zeitraum aufbewahren müssen (s. Ziffer 8 dieser Datenschutzinformation).</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerspruchsrecht</strong></p>
<p>Soweit die Verarbeitung Ihre personenbezogenen Daten nach Art. 6 Abs 1 lit. f DSGVO zur Wahrung berechtigter Interessen erfolgt, haben Sie gemäß Art. 21 DSGVO das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit Widerspruch gegen die Verarbeitung dieser Daten einzulegen. Wir verarbeiten diese personenbezogenen Daten dann nicht mehr, es sei denn, wir können zwingende schutzwürdige Gründe für die Verarbeitung nachweisen. Diese müssen Ihre Interessen, Rechte und Freiheiten überwiegen, oder die Verarbeitung muss der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen dienen.</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Automatisierte Entscheidungsfindung</strong></p>
<p>Es findet keine automatisierte Entscheidung im Einzelfall im Sinne des Art. 22 DSGVO statt.
</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Sonstiges</strong></p>
<p>Sie haben zudem das Recht, sich jederzeit an unseren Datenschutzbeauftragten zu wenden, der bezüglich Ihrer Anfrage zur Verschwiegenheit verpflichtet ist. Die Kontaktdaten finden Sie auf Seite 1 unter Punkt 2.
</p>
<div class="FAIRsepdown"></div>









<div id="datenschutz_durchfuehrung_agtest" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der Durchführung von <b>Corona SARS-COV-2 Schnelltests / PCR-Tests</b></h3>
<p>Gemäß den Vorgaben der Art. 13 ff der Datenschutz-Grundverordnung (DSGVO) informieren wir Sie hiermit über die Verarbeitung der über Sie erhobenen und verarbeiteten personenbezogenen Daten im Rahmen Ihrer Anmeldung und der Durchführung von Corona SARS-CoV-2 Schnelltests / PCR-Tests in den Einrichtungen des Deutschen Roten Kreuz Kreisverband e.V. </p>
<p>Die datenschutzrechtliche Verantwortlichkeit bei der Nutzung der Corona Warn App (CWA) liegt beim Robert-Koch-Institut (RKI). Die entsprechende Datenschutzinformation des Robert-Koch-Instituts (RKI) finden Sie auf folgendem Link: <a href="https://www.coronawarn.app/assets/documents/cwa-privacy-notice-de.pdf">https://www.coronawarn.app/assets/documents/cwa-privacy-notice-de.pdf</a>.
Dieser Link kann sich ggf. in der Zukunft ändern.</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Verantwortlich im Sinne der DSGVO Art. 4 Nr. 7</strong></p>
<p>Name: Frank Sauer</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: info@drk-odenwaldkreis.de</p>
<p>Website: http://www.drk-odenwaldkreis.de </p>
<p>Unternehmensbezeichnung: Deutsches Rotes Kreuz Kreisverband Odenwaldkreis e.V.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kontaktdaten unseres Datenschutzbeauftragten</strong></p>
<p>Name:Herr Kai Schwardt</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: datenschutz@drk-odenwaldkreis.de</p>
<p>Unternehmensbezeichnung: Extern benannter Datenschutzbeauftragter</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Zweck und Rechtsgrundlage der Verarbeitung</strong></p>
<p>Wir verarbeiten Ihre personenbezogenen Daten im Einklang mit den Bestimmungen der europäischen Datenschutz-Grundverordnung (EU-DSGVO), soweit dies für die Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-schnelltest in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. erforderlich ist. Rechtsgrundlage ist dabei Art. 6 lit. a DSGVO (Einwilligung), Art. 6 lit. b DSGVO (Vertragserfüllung – nur bei Selbstzahlern), Art 9 Abs. 2 lit.i DSGVO  sowie ggf. Art. 6 lit. c DSGVO (rechtliche Verpflichtung). Weitere Rechtgrundlagen sind Infektionsschutzgesetz – IfSG sowie die Coronavirus-Testverordnung – TestV.</p>
<p>Weiterhin können wir personenbezogene Daten von Ihnen verarbeiten, sofern dies zur Abwehr
von geltend gemachten Rechtsansprüchen gegen uns erforderlich ist. Rechtsgrundlage ist dabei Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse ist beispielsweise eine Beweispflicht im Rahmen rechtlicher Verfahren. Erteilen Sie uns eine ausdrückliche Einwilligung zur Verarbeitung von personenbezogenen Daten, ist die Rechtmäßigkeit dieser Verarbeitung auf Basis Ihrer Einwilligung nach Art. 6 Abs. 1 lit. a DSGVO gegeben. Eine erteilte Einwilligung kann jederzeit, mit Wirkung für die Zukunft, widerrufen werden (siehe Ziffer 9 dieser Datenschutzinformation).
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kategorie personenbezogener Daten</strong></p>
<p>Wir verarbeiten nur solche Daten, die im Zusammenhang mit der Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-Schnelltest in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. stehen. Dies sind im Einzelnen:</p>
<p>Vorname, Nachname, Namenszusätze, Kontaktdaten (etwa private Anschrift, (Mobil-)Telefonnummer, E-Mail-Adresse), Geburtsdatum, ggf. Geschlecht, ggf. Testgrund, Datum und Uhrzeit der Testdurchführung  sowie das Ergebnis des durchgeführten Covid19- Schnelltests (positiv/negativ), Status grippeähnliche Symptome, Selbstzahler (ja/nein), Zahlungsbeleg (nur bei Selbstzahlern).</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Quellen der Daten</strong></p>
<p>Die Erhebung Ihrer Daten findet grundsätzlich bei Ihnen selbst in Form Ihrer Anmeldung auf unserer Webseite https://www.testzentrum-odw.de oder vor Ort in einer unserer Einrichtungen statt. Die Verarbeitung der von Ihnen überlassenen personenbezogenen Daten ist zur Durchführung der Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-schnelltest notwendig. 
</p>
<p>Die Bereitstellung Ihrer personenbezogenen Daten ist notwendig. Sollten die erforderlichen personenbezogenen Daten nicht von Ihnen bereitgestellt werden, kann kein Test durchgeführt werden.
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Empfänger der Daten</strong></p>
<p>Wir geben Ihre personenbezogenen Daten innerhalb unserer Organisation ausschließlich an
die Personen weiter, die diese Daten zur Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-Schnelltest benötigen.
</p>
<p>Im Falle eines positiven Testergebnisses, geben wir Ihre personenbezogenen Daten gemäß Infektions-schutzgesetz an das zuständige Gesundheitsamt weiter. Die Übermittlung der Daten an das Gesundheits-amt erfolgt auf Grundlage des Art. 9 Abs. 2 lit. i DS-GVO i.V.m. §§ 6 Abs. 1 Nr. 1 lit. t, 8 Abs. 1 IfSG.
</p>
<p>Falls Sie die Corona Warn App des Robert Koch Institutes zu Abruf Ihres Testergebnisses verwenden möchten, geben wir – nach erteilter Einwilligung – Ihre personenbezogenen Daten sowie das Testergebnis  (pseudonymisiert) an das Robert Koch Institut (RKI) weiter.
</p>
<p>Ihre personenbezogenen Daten werden in unserem Auftrag ggf. auf Basis von Auftragsverarbeitungs-verträgen nach Art. 28 DSGVO verarbeitet. In diesen Fällen stellen wir sicher, dass die Verarbeitung von personenbezogenen Daten im Einklang mit den Bestimmungen der DSGVO erfolgt. Die Kategorien von Empfängern sind in diesem Fall z.B. Anbieter von Internetdienstanbieter sowie Anbieter von Software zur Administration der Terminverwaltung.
</p>
<p>Eine Datenweitergabe an Empfänger außerhalb unserer Organisation erfolgt ansonsten nur, soweit gesetzliche Bestimmungen dies erlauben oder gebieten.
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Übermittlung in ein Drittland</strong></p>
<p>Eine Übermittlung der Daten in ein Drittland erfolgt nicht. Jegliche Verarbeitung personenbezogener Daten erfolgt innerhalb der der Europäischen Union, oder des Europäischen Wirtschaftsraumes. </p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Dauer der Speicherung</strong></p>
<p>Ihre personenbezogenen Daten werden nach dem Grundsatz der Speicherbegrenzung (Art. 5 Abs. 1 lit. e DS-GVO) nur so lange gespeichert, wie es die jeweiligen Zwecke der Datenverarbeitung erfordern. Ihre personenbezogenen Daten werden daher grundsätzlich frühestmöglich gelöscht bzw. vernichtet.
</p>
<p>Gemäß § 7 Abs. 5 S. 1 TestV sind wir verpflichtet die für den Nachweis der korrekten Durchführung und Abrechnung notwendige Auftrags- und Leistungsdokumentation bis zum 31. Dezember 2024 unverändert zu speichern / aufzubewahren.
</p>
<p>Bei Selbstzahlern speichern wir die Quittungsbelege gemäß § 147 Abgabenordnung für 10 Jahre.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Ihre Rechte</strong></p>
<p>Jede betroffene Person hat das Recht auf Auskunft nach Art. 15 DSGVO, das Recht auf
Berichtigung nach Art. 16 DSGVO, das Recht auf Löschung nach Art. 17 DSGVO, das Recht auf
Einschränkung der Verarbeitung nach Art. 18 DSGVO, das Recht auf Mitteilung nach
Art. 19 DSGVO sowie das Recht auf Datenübertragbarkeit nach Art. 20 DSGVO.
</p>
<p>Darüber hinaus besteht ein Beschwerderecht bei einer Datenschutzaufsichtsbehörde nach
Art. 77 DSGVO, wenn Sie der Ansicht sind, dass die Verarbeitung Ihrer personenbezogenen
Daten nicht rechtmäßig erfolgt. Das Beschwerderecht besteht unbeschadet eines
anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs.
</p>
<p>Die zuständige Datenschutzaufsichtsbehörde erreichen Sie unter folgenden Kontaktdaten:</p>
<p>&nbsp;</p>
<p>Der Hessische Beauftragte für Datenschutz und Informationsfreiheit</p>
<p>Postfach 3163</p>
<p>65021 Wiesbaden</p>
<p>Telefon: +49 611 1408 – 0</p>
<p>Telefax: +49 611 1408 – 900</p>
<p>Mail: poststelle@datenschutz.hessen.de</p>
<p>https://datenschutz.hessen.de</p>
<p>&nbsp;</p>
<p>Sofern die Verarbeitung von Daten auf Grundlage Ihrer Einwilligung erfolgt, sind Sie nach Art. 7 DSGVO berechtigt, die Einwilligung in die Verwendung Ihrer personenbezogenen Daten jederzeit zu widerrufen. Bitte beachten Sie, dass der Widerruf erst für die Zukunft wirkt. Verarbeitungen, die vor dem Widerruf erfolgt sind, sind davon nicht betroffen. Bitte beachten Sie zudem, dass wir bestimmte Daten für die Erfüllung gesetzlicher Vorgaben ggf. für einen bestimmten Zeitraum aufbewahren müssen (s. Ziffer 8 dieser Datenschutzinformation).</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerspruchsrecht</strong></p>
<p>Soweit die Verarbeitung Ihre personenbezogenen Daten nach Art. 6 Abs 1 lit. f DSGVO zur Wahrung berechtigter Interessen erfolgt, haben Sie gemäß Art. 21 DSGVO das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit Widerspruch gegen die Verarbeitung dieser Daten einzulegen. Wir verarbeiten diese personenbezogenen Daten dann nicht mehr, es sei denn, wir können zwingende schutzwürdige Gründe für die Verarbeitung nachweisen. Diese müssen Ihre Interessen, Rechte und Freiheiten überwiegen, oder die Verarbeitung muss der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen dienen.</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Automatisierte Entscheidungsfindung</strong></p>
<p>Es findet keine automatisierte Entscheidung im Einzelfall im Sinne des Art. 22 DSGVO statt.
</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Sonstiges</strong></p>
<p>Sie haben zudem das Recht, sich jederzeit an unseren Datenschutzbeauftragten zu wenden, der bezüglich Ihrer Anfrage zur Verschwiegenheit verpflichtet ist. Die Kontaktdaten finden Sie auf Seite 1 unter Punkt 2.
</p>

<div id="datenschutz_cwa" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzhinweis bei Verwendung der Corona-Warn-App (CWA)</h3>

<p>
„Hinweise zum Datenschutz: Sie* möchten die Corona Warn App („App“) des Robert Koch Instituts („RKI“) zum Abruf Ihres Testergebnisses eines Antigentests verwenden. Um Ihr Testergebnis über die App abrufen zu können ist es notwendig, dass Ihr Testergebnis von der Teststelle an das Serversystem des RKI übermittelt wird.
</p><p>
Verkürzt dargestellt erfolgt dies, indem die Teststelle Ihr Testergebnis, verknüpft mit einem maschinenlesbaren Code, auf einem hierfür bestimmten Server des RKI ablegt. Der Code ist Ihr Pseudonym, weitere Angaben zu Ihrer Person sind für die Anzeige des Testergebnisses in der App nicht erforderlich. Sie können die Anzeige des Testergebnisses jedoch für sich durch Angabe Ihres Namens, Vornamens und Geburtsdatums personalisieren lassen.
</p><p>
Der Code wird aus dem vorgesehenen Zeitpunkt des Tests und einer Zufallszahl gebildet. Die Bildung des Codes erfolgt, indem die vorgenannten Daten so miteinander verrechnet werden, dass ein Zurückrechnen der Daten aus dem Code nicht mehr möglich ist.
</p><p>
Sie erhalten eine Kopie des Codes in der Darstellung eines QR Codes, der durch die Kamerafunktion Ihres Smartphones in die App eingelesen werden kann. Alternativ können Sie den pseudonymen Code auch als Internetverweis erhalten („App Link“), der von der App geöffnet und verarbeitet werden kann. Nur hierdurch ist eine Verknüpfung des Testergebnisses mit Ihrer App möglich. Mit Ihrer Einwilligung können Sie dann Ihr Testergebnis mit Hilfe der App abrufen. Ihr Testergebnis wird automatisch nach 21 Tagen auf dem Server gelöscht.
</p><p>
Wenn Sie mit der Übermittlung Ihres pseudonymen Testergebnisses mittels des Codes an die App Infrastruktur zum Zweck des Testabrufs einverstanden sind, bestätigen Sie dies bitte gegenüber den Mitarbeitern der Teststelle. Sie können Ihre Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen. Bitte beachten Sie jedoch, dass aufgrund der vorhandenen Pseudonymisierung eine Zuordnung zu Ihrer Person nicht erfolgen kann und daher eine Löschung Ihrer Daten erst mit Ablauf der 21 tägigen Speicherfrist automatisiert erfolgt.
</p><p>
Einzelheiten hierzu finden Sie zudem in den »Datenschutzhinweisen« der Corona Warn App des RKI.“
</p><p>
*Wenn Sie jünger als 16 Jahre alt sind, besprechen Sie die Nutzung der App bitte mit Ihren Eltern oder Ihrer sorgeberechtigten Person.
</p>

<div class="FAIRsepdown"></div>








<div id="datenschutz_durchfuehrung_antikoerpertest" class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<h3 class="imprint">Datenschutzinformationen nach Art. 13 und 14 DSGVO im Rahmen der Durchführung von <b>Corona-Antikörper-Tests</b></h3>
<p>Gemäß den Vorgaben der Art. 13 ff der Datenschutz-Grundverordnung (DSGVO) informieren wir Sie hiermit über die Verarbeitung der über Sie erhobenen und verarbeiteten personenbezogenen Daten im Rahmen Ihrer Anmeldung und der Durchführung von Corona-Antikörper-Schnelltests in den Einrichtungen des Deutschen Roten Kreuz Kreisverband e.V. </p>

<p><strong>&nbsp;</strong></p>
<p><strong>Verantwortlich im Sinne der DSGVO Art. 4 Nr. 7</strong></p>
<p>Name: Frank Sauer</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: info@drk-odenwaldkreis.de</p>
<p>Website: http://www.drk-odenwaldkreis.de </p>
<p>Unternehmensbezeichnung: Deutsches Rotes Kreuz Kreisverband Odenwaldkreis e.V.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kontaktdaten unseres Datenschutzbeauftragten</strong></p>
<p>Name: Herr Kai Schwardt</p>
<p>Telefonnummer: 06062 6070</p>
<p>E-Mail Adresse: datenschutz@drk-odenwaldkreis.de</p>
<p>Unternehmensbezeichnung: Extern benannter Datenschutzbeauftragter</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Zweck und Rechtsgrundlage der Verarbeitung</strong></p>
<p>Wir verarbeiten Ihre personenbezogenen Daten im Einklang mit den Bestimmungen der europäischen Datenschutz-Grundverordnung (EU-DSGVO), soweit dies für die Anmeldung, Terminkoordination, Durchführung und Abrechnung eines Corona Antikörper Schnelltest in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. erforderlich ist. Rechtsgrundlage ist dabei Art. 6 lit. a DSGVO (Einwilligung) sowie Art. 6 lit. b DSGVO (Vertragserfüllung).</p>
<p>Weiterhin können wir personenbezogene Daten von Ihnen verarbeiten, sofern dies zur Abwehr
von geltend gemachten Rechtsansprüchen gegen uns erforderlich ist. Rechtsgrundlage ist dabei Art. 6 Abs. 1 lit. f DSGVO. Das berechtigte Interesse ist beispielsweise eine Beweispflicht im Rahmen rechtlicher Verfahren. Erteilen Sie uns eine ausdrückliche Einwilligung zur Verarbeitung von personenbezogenen Daten, ist die Rechtmäßigkeit dieser Verarbeitung auf Basis Ihrer Einwilligung nach Art. 6 Abs. 1 lit. a DSGVO gegeben. Eine erteilte Einwilligung kann jederzeit, mit Wirkung für die Zukunft, widerrufen werden (siehe Ziffer 9 dieser Datenschutzinformation).
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Kategorie personenbezogener Daten</strong></p>
<p>Wir verarbeiten nur solche Daten, die im Zusammenhang mit der Anmeldung, Terminkoordination, Durchführung und Abrechnung eines Corona-Antikörper Schnelltest in einer Einrichtung des Deutschen Roten Kreuz Kreisverband e.V. stehen. Dies sind im Einzelnen:</p>
<p>Vorname, Nachname, Namenszusätze, ggf. Kontaktdaten (etwa private Anschrift, (Mobil-)Telefonnummer, E-Mail-Adresse), Geburtsdatum, ggf. Geschlecht, Datum und Uhrzeit der Testdurchführung  sowie das Ergebnis des durchgeführten Antikörper Schnelltests, Status „grippeähnliche Symptome“, Zahlungsbeleg.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Quellen der Daten</strong></p>
<p>Die Erhebung Ihrer Daten findet grundsätzlich bei Ihnen selbst in Form Ihrer Anmeldung auf unserer Webseite https://www.impfzentrum-odw.de/antikoerper oder vor Ort in einer unserer Einrichtungen statt. Die Verarbeitung der von Ihnen überlassenen personenbezogenen Daten ist zur Durchführung der Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-Antikörper-Schnelltests notwendig. 
</p>
<p>Die Bereitstellung Ihrer personenbezogenen Daten ist notwendig. Sollten die erforderlichen personenbezogenen Daten nicht von Ihnen bereitgestellt werden, kann kein Test durchgeführt werden.
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Empfänger der Daten</strong></p>
<p>Wir geben Ihre personenbezogenen Daten innerhalb unserer Organisation ausschließlich an
die Personen weiter, die diese Daten zur Anmeldung, Terminkoordination, Durchführung und Nachweisführung eines Corona-Antikörper-Schnelltest benötigen.
</p>
<p>Ihre personenbezogenen Daten werden in unserem Auftrag ggf. auf Basis von Auftragsverarbeitungs-verträgen nach Art. 28 DSGVO verarbeitet. In diesen Fällen stellen wir sicher, dass die Verarbeitung von personenbezogenen Daten im Einklang mit den Bestimmungen der DSGVO erfolgt. Die Kategorien von Empfängern sind in diesem Fall z.B. Anbieter von Internetdienstanbieter sowie Anbieter von Software zur Administration der Terminverwaltung.
</p>
<p>Eine Datenweitergabe an Empfänger außerhalb unserer Organisation erfolgt ansonsten nur, soweit gesetzliche Bestimmungen dies erlauben oder gebieten.
</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Übermittlung in ein Drittland</strong></p>
<p>Eine Übermittlung der Daten in ein Drittland erfolgt nicht. Jegliche Verarbeitung personenbezogener Daten erfolgt innerhalb der der Europäischen Union, oder des Europäischen Wirtschaftsraumes. </p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Dauer der Speicherung</strong></p>
<p>Ihre personenbezogenen Daten werden nach dem Grundsatz der Speicherbegrenzung (Art. 5 Abs. 1 lit. e DS-GVO) nur so lange gespeichert, wie es die jeweiligen Zwecke der Datenverarbeitung erfordern. Ihre personenbezogenen Daten werden daher grundsätzlich frühestmöglich gelöscht bzw. vernichtet.</p>
<p>Das Testergebnis des Corona Antikörper Schnelltests selbst, wird nicht gespeichert. Ihre im Rahmen der Erstellung von Quittungen speichern wir gemäß § 147 Abgabenordnung für 10 Jahre. Alle weiteren Daten werden am Folgetag des Corona antikörper-Schnelltests gelöscht.</p>
<p>&nbsp;</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Ihre Rechte</strong></p>
<p>Jede betroffene Person hat das Recht auf Auskunft nach Art. 15 DSGVO, das Recht auf
Berichtigung nach Art. 16 DSGVO, das Recht auf Löschung nach Art. 17 DSGVO, das Recht auf
Einschränkung der Verarbeitung nach Art. 18 DSGVO, das Recht auf Mitteilung nach
Art. 19 DSGVO sowie das Recht auf Datenübertragbarkeit nach Art. 20 DSGVO.
</p>
<p>Darüber hinaus besteht ein Beschwerderecht bei einer Datenschutzaufsichtsbehörde nach
Art. 77 DSGVO, wenn Sie der Ansicht sind, dass die Verarbeitung Ihrer personenbezogenen
Daten nicht rechtmäßig erfolgt. Das Beschwerderecht besteht unbeschadet eines
anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs.
</p>
<p>Die zuständige Datenschutzaufsichtsbehörde erreichen Sie unter folgenden Kontaktdaten:</p>
<p>&nbsp;</p>
<p>Der Hessische Beauftragte für Datenschutz und Informationsfreiheit</p>
<p>Postfach 3163</p>
<p>65021 Wiesbaden</p>
<p>Telefon: +49 611 1408 – 0</p>
<p>Telefax: +49 611 1408 – 900</p>
<p>Mail: poststelle@datenschutz.hessen.de</p>
<p>https://datenschutz.hessen.de</p>
<p>&nbsp;</p>
<p>Sofern die Verarbeitung von Daten auf Grundlage Ihrer Einwilligung erfolgt, sind Sie nach Art. 7 DSGVO berechtigt, die Einwilligung in die Verwendung Ihrer personenbezogenen Daten jederzeit zu widerrufen. Bitte beachten Sie, dass der Widerruf erst für die Zukunft wirkt. Verarbeitungen, die vor dem Widerruf erfolgt sind, sind davon nicht betroffen. Bitte beachten Sie zudem, dass wir bestimmte Daten für die Erfüllung gesetzlicher Vorgaben ggf. für einen bestimmten Zeitraum aufbewahren müssen (s. Ziffer 8 dieser Datenschutzinformation).</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Widerspruchsrecht</strong></p>
<p>Soweit die Verarbeitung Ihre personenbezogenen Daten nach Art. 6 Abs 1 lit. f DSGVO zur Wahrung berechtigter Interessen erfolgt, haben Sie gemäß Art. 21 DSGVO das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit Widerspruch gegen die Verarbeitung dieser Daten einzulegen. Wir verarbeiten diese personenbezogenen Daten dann nicht mehr, es sei denn, wir können zwingende schutzwürdige Gründe für die Verarbeitung nachweisen. Diese müssen Ihre Interessen, Rechte und Freiheiten überwiegen, oder die Verarbeitung muss der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen dienen.</p>

<p><strong>&nbsp;</strong></p>
<p><strong>Automatisierte Entscheidungsfindung</strong></p>
<p>Es findet keine automatisierte Entscheidung im Einzelfall im Sinne des Art. 22 DSGVO statt.
</p>
<p>&nbsp;</p>
<p><strong>&nbsp;</strong></p>
<p><strong>Sonstiges</strong></p>
<p>Sie haben zudem das Recht, sich jederzeit an unseren Datenschutzbeauftragten zu wenden, der bezüglich Ihrer Anfrage zur Verschwiegenheit verpflichtet ist. Die Kontaktdaten finden Sie auf Seite 1 unter Punkt 2.
</p>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>
Erbach, Im Oktober 2021
<p></p>
Deutsches Rotes Kreuz Kreisverband Odenwaldkreis e.V.
<div class="FAIRsepdown"></div>
<div class="FAIRsepdown"></div>




';



// Print html content part C
echo $GLOBALS['G_html_main_right_c'];
// Print html footer
echo $GLOBALS['G_html_footer'];






?>
