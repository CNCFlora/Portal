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
from handlers.document import DocumentHandler
from handlers.profile import ProfileHandler
from handlers.project import ProjectHandler
from handlers.equipe import EquipeHandler

application = app = Flask(__name__)
file_handler = logging.FileHandler('app.log')
app.logger.addHandler(file_handler)
app.logger.setLevel(logging.DEBUG)
app.secret_key = 'some_secret'
app.config['MARROWMAILER_CONFIG'] = {
    'manager.use': 'futures',
    'transport.use': 'smtp',
    'transport.host': '',
}
app.url_map.strict_slashes = False
mailer = Mailer(app)


app.add_url_rule('/', view_func=HomeHandler.as_view('home'))
app.add_url_rule('/<language>/listavermelha',defaults={'family': None},
  view_func=RedlistHandler.as_view('redlist'))
app.add_url_rule('/<language>/listavermelha/<family>',
  view_func=RedlistHandler.as_view('redlistfamily'))
app.add_url_rule('/<language>/publicacoes',
  view_func=DocumentHandler.as_view('documents'))
app.add_url_rule('/<language>/profile/<name>',
  view_func=ProfileHandler.as_view('profile'))
app.add_url_rule('/<language>/projeto/<project>',
  view_func=ProjectHandler.as_view('project'))
app.add_url_rule('/<language>/equipe',
  view_func=EquipeHandler.as_view('equipe'))

if __name__ == "__main__":
    app.run(port=8889, host='0.0.0.0', debug=True)
