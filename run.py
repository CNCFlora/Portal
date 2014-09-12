# -*- coding: utf-8 -*-

from flask import Flask, render_template, request, flash
import jinja2
import json
import logging
import requests
import urllib2
from flask_marrowmailer import Mailer

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
@app.route('/')
def Home():
    return  render_template('index.html')


@app.route('/<language>/quem-somos', methods=['GET'])
def WhoWeAre(language):
    json_data = open('json/quemsomos.json').read()
    response = json.loads(json_data)
    app.logger.debug(response)
    data = response[language]
    menu = data['menu']
    return  render_template('quem-somos.html', menu=menu, language=language)


@app.route('/<language>/equipe/<members>', methods=['GET'])
def Equipe(language, members):
    json_data = open('json/quemsomos.json').read()
    response = json.loads(json_data)
    app.logger.debug(response)

    data = response[language]
    menu = data['menu']
    submenu = data['menu-equipe']
    return  render_template(
        'equipe.html',
        menu=menu,
        language=language,
        submenu=submenu,
        members=members)


@app.route('/<language>/contato', methods=['GET', 'POST'])
def Contact(language):
    if request.method == 'POST':
      if request.form['email'] != "" and request.form['message'] != "":
          msg = mailer.new()
          msg.author = request.form['email']
          msg.to = ['max@diogok.net']
          msg.subject = request.form['subject']
          msg.plain = request.form['message']
          msg.rich = '<p> <b>' + request.form['message'] + '</b></p>'
          mailer.send(msg)

          flash( 'email enviado!', 'success')
      else:
          flash('informação imcompleta!', 'error')


    json_data = open('json/quemsomos.json').read()
    response = json.loads(json_data)
    app.logger.debug(response)
    data = response[language]
    menu = data['menu']
    form_contato= data['form-contato']

    return  render_template(
          'contato.html',
          menu=menu,
          language=language,
          form_contato=form_contato)


@app.route('/<language>/conservacao/<area>', methods=['GET'])
def Conservacao(language, area):
    json_data = open('json/conservasao.json').read()
    response = json.loads(json_data)
    app.logger.debug(response)

    data = response[language]
    menu = data['menu']
    return  render_template(
            'conservacao.html',
            menu=menu,
            language=language,
            area=area)

@app.route('/<language>/legislacao', methods=['GET'])
def Legislacao(language):
    return  render_template(
            'legislacao.html',
            language=language)





if __name__ == "__main__":
    app.run(port=8888, host='0.0.0.0', debug=True)
