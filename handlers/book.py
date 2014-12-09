from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

import requests
import jinja2

class BookHandler(MethodView, BaseHandler):

    def get(self, language):

        return render_template(
                'book.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                )
