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
from utils.slot import get_slot_time


FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'
Logo = '../utils/logo.png'
Logo2 = '../utils/logo2.png'

class PDFgenerator(FPDF):

	
	def create_page(self):
		self.add_page()
		self.add_font('GNU', '', FreeSans, uni=True)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 30)
		self.cell(10, 30, '', ln=1)
		self.cell(200, 5, 'Termin-Ticket für einen', ln=1, align='C')
		self.cell(200, 30, 'SARS-CoV-2-Schnelltest(PoC)', ln=1, align='C')
		self.set_font('GNU', '', 20)
		self.cell(200,15, 'Name: ' + self.nachname + ', ' + self.vorname, ln=1)
		self.cell(200,15, 'Datum: ' + self.date.strftime("%d.%m.%Y"), ln=1)
		self.cell(200,15, 'Uhrzeit: ' + str(self.appointment), ln=1)
		self.cell(200,15, 'Ort:', ln=1)
		self.multi_cell(0,15, str(self.location), 0)
		self.qrcode = pyqrcode.create(str(self.code), error='Q')
		self.qrcode.png('tmp/'+str(self.code) + '.png', scale=6,quiet_zone=4)
		self.image('tmp/'+ str(self.code) + '.png', y=85,x=140)
		self.cell(10, 10, '', ln=1)
		self.cell(200, 10, '%s' % (self.code), ln=1, align='C')
		self.cell(10, 15, '', ln=1)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 12)
		self.multi_cell(195, 5, 'Bitte halten Sie sich an die geltenden Abstandsregeln. Erscheinen Sie bitte nur, wenn Sie sich gesund fühlen und frei von Symptomen sind.',0, align='C')
		self.multi_cell(195, 10, 'Bringen Sie zum Test bitte einen gültigen Lichtbildausweis mit.',0, align='C')
		os.remove('tmp/'+str(self.code) + '.png')

	def creatPDF(self,content, location):
		self.code = content[6]
		self.slot = content[3]
		self.stunde = content[4]
		self.vorname = content[0]
		self.nachname = content[1]
		self.date = content[5]
		self.location = location
		self.appointment = get_slot_time(self.slot, self.stunde)
		self.time = datetime.date.today().strftime("%d.%m.%Y")
		self.create_page()
		self.filename = "../../Tickets/Ticket_" + str(self.code) + "_" + str(self.date) + ".pdf"
		self.output(self.filename)
		return self.filename
	

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)



	def footer(self):
		self.set_y(-80)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 12)
		self.cell(210, 10, 'Einwilligungserklärung:', ln=1)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 12)
		self.multi_cell(195, 5, 'Ich haben mich soeben zu einer Teilnahme an einem SARS-CoV-2-Schnelltest(Corona-PoC-Test) angemeldet. Ich weiß, dass der Test durch unterwiesenes, ggf. nichtmedizinisches Hilfspersonal gemäß dem Drittem Gesetz zum Schutz der Bevölkerung bei einer epidemischen Lage von nationaler Tragweite vom 18.11.2020 durchgeführt wird. Mit der Verarbeitung meiner persönlichen Daten sowie dem Testergebnis durch das DRK bin ich einverstanden. Sofern der Test positiv ist, werden die Daten aufgrund einer gesetzlichen Meldepflicht an das Gesundheitsamt weitergegeben.',0)
		self.ln(5)
		self.image(Logo2,x=60,w=110, h=24)


