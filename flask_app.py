from flask import Flask, request, jsonify
from flask_caching import Cache
from graph_db_interface import GraphDBInterface
import os
import subprocess
import sqlite3

app = Flask(__name__)
cache = Cache(app, config={
    'CACHE_TYPE': 'SimpleCache',
    'CACHE_DEFAULT_TIMEOUT': 60
})

db = GraphDBInterface()


def get_forum_comments_from_db():
    """Return forum comments grouped by agent as a dict."""
    db_path = os.getenv('TEST_SQLITE_PATH') or os.path.join(os.path.dirname(__file__), 'forum.db')
    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row
    conn.execute(
        "CREATE TABLE IF NOT EXISTS forum_comments (id INTEGER PRIMARY KEY AUTOINCREMENT, agent TEXT NOT NULL, comment TEXT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
    )
    rows = conn.execute('SELECT agent, comment, created_at FROM forum_comments ORDER BY created_at DESC').fetchall()
    comments = {}
    for row in rows:
        comments.setdefault(row['agent'], []).append({'comment': row['comment'], 'created_at': row['created_at']})
    conn.close()
    return comments

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


@app.route('/api/forum/comments', methods=['GET', 'POST'])
def forum_comments_handler():
    if request.method == 'POST':
        data = request.get_json(silent=True) or {}
        agent = str(data.get('agent', '')).strip()
        comment = str(data.get('comment', '')).strip()
        if not agent or not comment:
            return jsonify({'error': "'agent' and 'comment' are required"}), 400
        script_path = os.path.join(os.path.dirname(__file__), 'ajax_actions', 'save_comment.php')
        try:
            result = subprocess.run(
                ['php', script_path, agent, comment],
                capture_output=True,
                text=True,
                check=False
            )
            if result.returncode != 0:
                error_msg = result.stderr.strip() or result.stdout.strip() or 'Unknown error'
                return jsonify({'error': f'PHP error: {error_msg}'}), 500
            return jsonify({'success': True})
        except Exception as exc:
            return jsonify({'error': str(exc)}), 500
    else:
        try:
            comments = get_forum_comments_from_db()
            return jsonify(comments)
        except Exception as exc:
            return jsonify({'error': str(exc)}), 500

if __name__ == '__main__':
    debug_env = os.getenv('FLASK_DEBUG')
    debug_mode = str(debug_env).lower() in ('1', 'true')
    app.run(debug=debug_mode)
