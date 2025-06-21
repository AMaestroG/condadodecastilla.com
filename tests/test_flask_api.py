import unittest
from types import SimpleNamespace
from unittest.mock import patch
import flask_app

class FakeDB:
    def __init__(self):
        self.resources = []
        self.links = []
    def add_or_update_resource(self, data):
        self.resources.append(data)
    def get_all_resources(self):
        return self.resources
    def get_all_links(self):
        return self.links

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

    def test_graph_endpoint_basic(self):
        flask_app.db.resources = [
            {'url': 'http://a.com', 'last_crawled_at': '2020-01-01T00:00:00'}
        ]
        flask_app.db.links = [
            {
                'source_url': 'http://a.com',
                'target_url': 'http://b.com',
                'created_at': '2020-01-01T00:00:00'
            }
        ]

        res = self.client.get('/api/graph')
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertIn('nodes', data)
        self.assertIn('links', data)
        self.assertEqual(len(data['nodes']), 1)

    def test_graph_endpoint_limit_and_start_date(self):
        flask_app.db.resources = [
            {'url': 'http://a.com', 'last_crawled_at': '2021-01-01T00:00:00'},
            {'url': 'http://b.com', 'last_crawled_at': '2022-01-01T00:00:00'}
        ]
        flask_app.db.links = [
            {
                'source_url': 'http://a.com',
                'target_url': 'http://b.com',
                'created_at': '2022-01-01T00:00:00'
            }
        ]

        res = self.client.get('/api/graph?limit=1&start_date=2022-01-01T00:00:00')
        self.assertEqual(res.status_code, 200)
        data = res.get_json()
        self.assertEqual(len(data['nodes']), 1)
        # after filtering, no links should remain because b.com node removed
        self.assertEqual(len(data['links']), 0)

if __name__ == '__main__':
    unittest.main()
