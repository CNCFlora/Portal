from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

import requests
import jinja2
import json

class DocumentHandler(MethodView, BaseHandler):

    def get(self, language):

        return render_template(
                'document.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url
                )
