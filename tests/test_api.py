import os
import sys
import unittest
from types import SimpleNamespace
from unittest.mock import patch
from fastapi.testclient import TestClient

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))
from backend.app import main

class FakeDB:
    def __init__(self):
        self.resources = []
    def add_or_update_resource(self, data):
        self.resources.append(data)
    def get_all_resources(self):
        return self.resources

class FastApiTestCase(unittest.TestCase):
    def setUp(self):
        main.db_interface = FakeDB()
        self.client = TestClient(main.app)

    def test_resource_post_and_get(self):
        res = self.client.post('/api/resource', json={'url': 'http://example.com'})
        self.assertEqual(res.status_code, 201)
        self.assertEqual(res.json(), {'success': True})

        res = self.client.get('/api/resource')
        self.assertEqual(res.status_code, 200)
        data = res.json()
        self.assertIsInstance(data, list)
        self.assertEqual(data[0]['url'], 'http://example.com')

    def test_resource_post_missing_url(self):
        res = self.client.post('/api/resource', json={'foo': 'bar'})
        self.assertEqual(res.status_code, 400)
        self.assertIn('detail', res.json())

    @patch('backend.app.main.subprocess.run')
    def test_chat_success(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=0, stdout='Hola', stderr='')
        res = self.client.post('/api/chat', json={'prompt': 'hola'})
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.json(), {'response': 'Hola'})

    def test_chat_missing_prompt(self):
        res = self.client.post('/api/chat', json={})
        self.assertEqual(res.status_code, 400)
        self.assertIn('detail', res.json())

    @patch('backend.app.main.subprocess.run')
    def test_chat_php_error(self, mock_run):
        mock_run.return_value = SimpleNamespace(returncode=1, stdout='', stderr='fail')
        res = self.client.post('/api/chat', json={'prompt': 'hola'})
        self.assertEqual(res.status_code, 500)
        self.assertIn('detail', res.json())

if __name__ == '__main__':
    unittest.main()
