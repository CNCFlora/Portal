from flask.views import MethodView
from flask import Flask, render_template, request, flash
import jinja2
import json
import os

class BaseHandler():

    base_url= os.environ.get('BASE','')
    static_url= base_url+'/static'
