from flask import Flask, request, jsonify
from graph_db_interface import GraphDBInterface

app = Flask(__name__)

db = GraphDBInterface()

@app.route('/api/resource', methods=['GET', 'POST'])
def resource_collection():
    if request.method == 'POST':
        data = request.get_json(silent=True) or {}
        if not data.get('url'):
            return jsonify({'error': "'url' field is required"}), 400
        db.add_or_update_resource(data)
        return jsonify({'success': True}), 201
    else:  # GET
        resources = db.get_all_resources()
        return jsonify(resources)

if __name__ == '__main__':
    app.run(debug=True)
