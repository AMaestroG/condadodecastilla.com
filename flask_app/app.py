from flask import Flask, request, jsonify

app = Flask(__name__)

# In-memory message store
messages = []

@app.route('/api/foro/messages', methods=['GET', 'POST'])
def forum_messages():
    if request.method == 'POST':
        data = request.get_json(force=True)
        if not data or 'author' not in data or 'content' not in data:
            return jsonify({'error': 'Invalid payload'}), 400
        message = {
            'id': len(messages) + 1,
            'author': data['author'],
            'content': data['content']
        }
        messages.append(message)
        return jsonify(message), 201

    # GET request
    return jsonify(messages)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
