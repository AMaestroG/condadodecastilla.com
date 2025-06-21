from flask import Flask, request, jsonify
from flask_caching import Cache
from graph_db_interface import GraphDBInterface
from dataclasses import asdict
from datetime import datetime
import os
import subprocess

app = Flask(__name__)
cache = Cache(app, config={
    'CACHE_TYPE': 'SimpleCache',
    'CACHE_DEFAULT_TIMEOUT': 60
})

db = GraphDBInterface()


@app.route('/api/graph', methods=['GET'])
def graph_handler():
    """Return resources and links from the graph database."""
    start_date_str = request.args.get('start_date')
    limit = request.args.get('limit', type=int)

    resources = [asdict(r) if not isinstance(r, dict) else r for r in db.get_all_resources()]
    links = [asdict(l) if not isinstance(l, dict) else l for l in db.get_all_links()]

    if start_date_str:
        try:
            start_dt = datetime.fromisoformat(start_date_str)
            resources = [r for r in resources if 'last_crawled_at' in r and datetime.fromisoformat(r['last_crawled_at']) >= start_dt]
            links = [l for l in links if 'created_at' in l and datetime.fromisoformat(l['created_at']) >= start_dt]
        except ValueError:
            return jsonify({'error': 'Invalid start_date format'}), 400

    if limit is not None and limit >= 0:
        resources = resources[:limit]
        node_urls = {r['url'] for r in resources}
        links = [l for l in links if l.get('source_url') in node_urls and l.get('target_url') in node_urls]

    return jsonify({'nodes': resources, 'links': links})

@app.route('/api/resource', methods=['GET', 'POST'])
def resource_collection():
    if request.method == 'POST':
        data = request.get_json(silent=True) or {}
        if not data.get('url'):
            return jsonify({'error': "'url' field is required"}), 400
        db.add_or_update_resource(data)
        cache.delete('all_resources')
        return jsonify({'success': True}), 201
    else:  # GET
        resources = cache.get('all_resources')
        if resources is None:
            resources = db.get_all_resources()
            cache.set('all_resources', resources)
        return jsonify(resources)


@app.route('/api/chat', methods=['POST'])
def chat_handler():
    data = request.get_json(silent=True) or {}
    prompt = str(data.get('prompt', '')).strip()
    if not prompt:
        return jsonify({'error': "'prompt' field is required"}), 400

    script_path = os.path.join(os.path.dirname(__file__), 'scripts', 'chat_cli.php')
    try:
        result = subprocess.run(
            ['php', script_path, prompt],
            capture_output=True,
            text=True,
            check=False
        )
        if result.returncode != 0:
            error_msg = result.stderr.strip() or 'Unknown error'
            return jsonify({'error': f'PHP error: {error_msg}'}), 500
        return jsonify({'response': result.stdout.strip()})
    except Exception as exc:
        return jsonify({'error': str(exc)}), 500

if __name__ == '__main__':
    debug_env = os.getenv('FLASK_DEBUG')
    debug_mode = str(debug_env).lower() in ('1', 'true')
    app.run(debug=debug_mode)
