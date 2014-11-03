# -*- coding: utf-8 -*-

from flask import Flask, render_template, request, flash
import jinja2
import json
import logging
import requests
import urllib2
from flask_marrowmailer import Mailer
from handlers.home import HomeHandler
from handlers.redlist import RedlistHandler


app = Flask(__name__)
file_handler = logging.FileHandler('app.log')
app.logger.addHandler(file_handler)
app.logger.setLevel(logging.DEBUG)
app.secret_key = 'some_secret'
app.config['MARROWMAILER_CONFIG'] = {
    'manager.use': 'futures',
    'transport.use': 'smtp',
    'transport.host': '',
}
mailer = Mailer(app)


app.add_url_rule('/', view_func=HomeHandler.as_view('home'))
app.add_url_rule('/<language>/listavermelha/',
  view_func=RedlistHandler.as_view('redlist'))

if __name__ == "__main__":
    app.run(port=8889, host='0.0.0.0', debug=True)
