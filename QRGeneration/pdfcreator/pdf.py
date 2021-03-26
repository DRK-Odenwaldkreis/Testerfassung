#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This file is part of DRK Testerfassung.


import sys
import pyqrcode
import random
import png
from fpdf import FPDF
import time
import os
import os.path
import datetime
sys.path.append("..")


FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'
Logo = '../utils/logo.png'


class PDFgenerator(FPDF):

	
	def create_page(self,code):
		self.add_page()
		self.code = code
		self.add_font('GNU', '', FreeSans, uni=True)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 30)
		self.cell(10, 30, '', ln=1)
		self.cell(200, 5, 'Test-Ticket für einen', ln=1, align='C')
		self.cell(200, 20, 'SARS-CoV-2-Schnelltest(PoC)', ln=1, align='C')
		self.set_font('GNU', '', 20)
		self.qrcode = pyqrcode.create('K' + str(self.code), error='L')
		self.qrcode.png('tmp/'+str(code) + '.png', scale=5,quiet_zone=2)
		self.image('tmp/'+str(code) + '.png', x=85)
		#self.cell(10, 0, '', ln=1)
		self.current_x=self.get_x
		self.cell(190, 10, '#%s' % (self.code), ln=1, align='C')
		os.remove('tmp/'+str(code) + '.png')

	def add_codes(self,codes):
		self.codes = codes

	def creatPDF(self):
		self.time = datetime.date.today().strftime("%d.%m.%Y")
		for i in self.codes:
			self.create_page(i)
		self.filename = "../../Testkarten/Testkartensatz_" + \
			str(self.codes[0]) + "-" + str(self.codes[-1]) + ".pdf"
		self.output(self.filename)
	

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)


	def footer(self):
		self.set_y(-160)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 12)
		self.cell(210, 10, 'Einwilligungserklärung:', ln=1)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 12)
		self.multi_cell(195, 5, 'Ich haben mich soeben zu einer Teilnahme an einem SARS-CoV-2-Schnelltest(Corona-PoC-Test) angemeldet. Ich fühle mich gesund und bin frei von Symptomen. Ich weiß, dass der Test durch unterwiesenes, ggf. nichtmedizinisches Hilfspersonal gemäß dem Drittem Gesetz zum Schutz der Bevölkerung bei einer epidemischen Lage von nationaler Tragweite vom 18.11.2020 durchgeführt wird. Mit der Verarbeitung meiner persönlichen Daten sowie dem Testergebnis durch das DRK bin ich einverstanden. Sofern der Test positiv ist, werden die Daten aufgrund einer gesetzlichen Meldepflicht an das Gesundheitsamt weitergegeben.',0)
		self.ln(5)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 14)
		self.set_text_color(255,0,0)
		self.multi_cell(195, 5, 'Mit der Weitergabe dieses Tickets erkläre ich mein Einverständnis an der Teilnahme am Testverfahren.', 0,align='C')
		self.ln(5)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 12)
		self.set_text_color(0, 0, 0)
		self.multi_cell(195, 5, 'Sollten Sie doch nicht teilnehmen wollen und eine sofortige Löschung ihrer Daten wünschen, wenden Sie sich an das Personal des Testzentrums.', 0)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 9)
		self.cell(210, 10, 'Datenschutzinformationen:', ln=1)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 9)
		self.multi_cell(195, 5, 'Im Rahmen des bei Ihnen durchgeführten Corona-Schnelltests(PoC-Antigentest auf SARS-CoV-2) erhebt der DRK-Kreisverband Odenwaldkreis e.V., Illigstr. 11, 64711 Erbach, personenbezogene Daten von Ihnen. Wir verarbeiten Ihren Namen, Anschrift, Geburtsdatum, Telefonnummer und E-Mail-Adresse, um im Falle eines positiven Testergebnisses das zuständige Gesundheitsamt darüber zu informieren und diesem ihre persönlichen Daten nach § 8 Abs. 1 Nr. 5 IfSG weiterzugeben. Rechtsgrundlage der Datenverarbeitung ist Art. 9 Abs. 2 lit. i DSGVO i.V.m. § 9 Abs. 1 IfSG. Eine Löschung Ihrer Daten erfolgt im Fall einer positiven Testung nach 4 Wochen. Um die unverzügliche Kontaktaufnahme des Gesundheitsamtes mit Ihnen zu gewährleisten, erheben wir die Telefonnummer und E-Mail-Adresse nach Art. 6 Abs. 1 lit. c DSGVO i.V.m. § 9 Abs. 1 IfSG. Die Löschung Ihrer Daten im Fall einer negativen Testung erfolgt unverzüglich nach Ergebnismitteilung. Die Bereitstellung Ihrer Daten ist grundsätzlich freiwillig. Ohne diese Daten können wir den Test jedoch nicht durchführen. Als betroffene Person haben Sie das Recht auf Auskunft über die Sie betreffenden personenbezogenen Daten und auf Berichtigung unrichtiger Daten sowie auf Löschung, sofern einer der in Art. 17 DSGVO genannten Gründe vorliegt. Sie haben zudem das Recht auf Datenübertragbarkeit sowie auf Einschränkung der Datenverarbeitung. Ferner haben Sie das Recht, sich bei einer Aufsichtsbehörde zu beschweren. Bei Fragen können Sie sich jederzeit an unseren Datenschutzbeauftragten wenden. Weitere  Informationen zum Datenschutz  sind auf der Webseite des DRK unter https: // drk-odenwaldkreis.de/datenschutz / jederzeit nachzulesen und können bei Bedarf herunterladen bzw. ausdrucken werden.', 0)



