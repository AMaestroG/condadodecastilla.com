"""Minimal Flask API for the nuevaweb demo.

This service only exposes ``/api/hello`` and complements ``flask_app.py`` in the
repository root. It is meant for quick demos and does not replace the main API.
"""

from flask import Flask, jsonify

app = Flask(__name__)

@app.route('/api/hello')
def hello():
    return jsonify(message='Hola desde nuevaweb')

if __name__ == '__main__':
    app.run(debug=True)
