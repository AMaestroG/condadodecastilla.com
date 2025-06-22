import unittest
from types import SimpleNamespace
from unittest.mock import patch
import flask_app

class FakeDB:
    def __init__(self):
        self.resources = []
    def add_or_update_resource(self, data):
        self.resources.append(data)
    def get_all_resources(self):
        return self.resources

class FlaskApiTestCase(unittest.TestCase):
    def setUp(self):
        flask_app.db = FakeDB()
        self.client = flask_app.app.test_client()

    def test_resource_post_and_get(self):
        res = self.client.post('/api/resource', json={'url': 'http://example.com'})
        self.assertEqual(res.status_code, 201)
        self.assertEqual(res.get_json(), {'success': True})

        res = self.client.get('/api/resource')
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertIsInstance(data, list)
        self.assertEqual(data[0]['url'], 'http://example.com')

    def test_resource_post_missing_url(self):
        res = self.client.post('/api/resource', json={'foo': 'bar'})
        self.assertEqual(res.status_code, 400)
        self.assertIn('error', res.get_json())

    @patch('flask_app.subprocess.run')
    def test_chat_success(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=0, stdout='Hola', stderr='')
        res = self.client.post('/api/chat', json={'prompt': 'hola'})
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.get_json(), {'response': 'Hola'})

    def test_chat_missing_prompt(self):
        res = self.client.post('/api/chat', json={})
        self.assertEqual(res.status_code, 400)
        self.assertIn('error', res.get_json())

    @patch('flask_app.subprocess.run')
    def test_chat_php_error(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=1, stdout='', stderr='fail')
        res = self.client.post('/api/chat', json={'prompt': 'hola'})
        self.assertEqual(res.status_code, 500)
        self.assertIn('error', res.get_json())

    @patch('flask_app.get_forum_comments_from_db')
    def test_forum_comments_get(self, mock_get):
        mock_get.return_value = {'hist': [{'comment': 'ok', 'created_at': 'now'}]}
        res = self.client.get('/api/forum/comments')
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.get_json(), {'hist': [{'comment': 'ok', 'created_at': 'now'}]})

    @patch('flask_app.subprocess.run')
    def test_forum_comments_post_success(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=0, stdout='success', stderr='')
        res = self.client.post('/api/forum/comments', json={'agent': 'hist', 'comment': 'Hi'})
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.get_json(), {'success': True})

    def test_forum_comments_post_missing_fields(self):
        res = self.client.post('/api/forum/comments', json={'agent': 'hist'})
        self.assertEqual(res.status_code, 400)
        self.assertIn('error', res.get_json())

    @patch('flask_app.subprocess.run')
    def test_forum_comments_post_error(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=1, stdout='', stderr='fail')
        res = self.client.post('/api/forum/comments', json={'agent': 'hist', 'comment': 'Hi'})
        self.assertEqual(res.status_code, 500)
        self.assertIn('error', res.get_json())

    def test_forum_agents_endpoint(self):
        res = self.client.get('/api/forum/agents')
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertIsInstance(data, dict)
        self.assertGreaterEqual(len(data), 5)

if __name__ == '__main__':
    unittest.main()
