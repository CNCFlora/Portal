from flask.views import MethodView
from flask import Flask, render_template, request, flash
import jinja2
import json

class BaseHandler():

    base_url= 'http://localhost:8889'
    static_url= 'http://localhost:8889/static'
