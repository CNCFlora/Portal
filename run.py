from flask import Flask, render_template, request
import jinja2
import json
import logging
import requests
import urllib2

app = Flask(__name__)
file_handler = logging.FileHandler('app.log')
app.logger.addHandler(file_handler)
app.logger.setLevel(logging.DEBUG)

@app.route('/')
def Home():
    return  render_template('index.html')


@app.route('/<language>/quem-somos',methods=['GET'])
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
    return  render_template('equipe.html', menu=menu, language=language, submenu=submenu, members=members)


if __name__ == "__main__":
    app.run(port=8888, host='0.0.0.0', debug=True)
