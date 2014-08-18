from flask import Flask, render_template
import jinja2
app = Flask(__name__)

@app.route('/')
def Home():
    return  render_template('index.html')


@app.route('/quem-somos')
def WhoWeAre():
    return  render_template('quem-somos.html')


if __name__ == "__main__":
    app.run(port=8888, host='0.0.0.0', debug=True)
