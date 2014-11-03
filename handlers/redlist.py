from flask.views import MethodView
from flask import Flask, render_template, request, flash
import jinja2

class RedlistHandler(MethodView):

    def get(self):
        json_data = requests.get('http://cncflora.jbrj.gov.br/services/assessments/families')
        # json_data = requests.get('http://cncflora.jbrj.gov.br/services/assessments/family/ARALIACEAE')
        response = json.loads(json_data.text)
        # app.logger.debug(response)
        families = response
        return render_template(
                'redlisting.html',
                language=language,
                base_url=base_url,
                families=families)
