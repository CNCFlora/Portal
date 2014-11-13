from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

import requests
import jinja2
import json

class ProjectHandler(MethodView, BaseHandler):

    def get(self, language, project):
        return render_template(
                'project.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                project=project,
                )
