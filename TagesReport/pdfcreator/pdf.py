#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This file is part of DRK Testzentrum.

import sys
from fpdf import FPDF
import time
import os
import os.path
import datetime
sys.path.append("..")

Logo = '../utils/logo.png'

FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'

class MyPDF(FPDF):


	time='zeit'


	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.cell(40, 10, 'Testzentrum Odenwaldkreis:', ln=1)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)


	def footer(self):
		self.set_y(-15)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)

		page= 'Seite %s/ {nb}' % self.page_no()

		self.cell(0, 10, page, align='R')


class PDFgenerator:

	def __init__(self, content, date):
		self.content=content
		self.date=date
		self.totalSeconds=0
		self.station = self.content[0]
		self.tests = self.content[1]
		self.positiv = self.content[2]
		self.negativ = self.content[3]
		self.unklar = self.content[4]

		self.rate = self.positiv/self.tests


	def generate(self):

		pdf=MyPDF()
		#pdf.time=self.date
		# pdf.name=self.name
		pdf.alias_nb_pages()
		pdf.add_page()
		pdf.set_auto_page_break(True, 25)
		pdf.add_font('GNU', '', FreeSans, uni=True)
		pdf.add_font('GNU', 'B', FreeSansBold, uni=True)

		pdf.set_font('GNU', 'B', 14)
		pdf.cell(20, 10, 'Tagesprotokoll für das Testzentrum %s vom %s' % (self.station[1],self.date), ln=1)

		pdf.set_font('GNU', '', 14)

		pdf.cell(20, 10, 'Erstellt: {}'.format(datetime.datetime.now().strftime("%Y-%m-%d um %H:%M:%S"), ln=1))
		pdf.set_text_color(255,0,0)
		pdf.cell(0,10, 'Rote Einträge prüfen', align='R', ln=1)
		pdf.set_text_color(0,0,0)
		pdf.set_font('GNU', 'B' , 20)
		pdf.ln(15)
		pdf.set_font('GNU', 'B', 14)
		pdf.cell(35, 10, 'Testzentrum-Nr.', 0, 0)
		pdf.cell(35, 10, 'Ort', 0, 1)


		current_x =pdf.get_x()
		current_y =pdf.get_y()

		pdf.line(current_x, current_y, current_x+190, current_y)

		pdf.set_font('GNU', '', 14)
		self.filename = "../../Reports/Tagesreport_Testzentrum-%s" + str(self.station) + "_"+str(self.date) + ".pdf"
		pdf.output(self.filename)
		return self.filename.replace('../../Reports/','')

aux=FPDF('P', 'mm', 'A4')
