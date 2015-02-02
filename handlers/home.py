import jinja2
import json
import requests
import urllib
from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

class HomeHandler(MethodView, BaseHandler):

    def get(self):
          return  render_template('home.html',
          base_url=self.base_url,
          static_url= self.static_url)
