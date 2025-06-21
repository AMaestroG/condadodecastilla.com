import os
import sys
import unittest

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))
from nuevaweb import flask_app as nueva_app


class NuevawebFlaskApiTestCase(unittest.TestCase):
    def setUp(self):
        self.client = nueva_app.app.test_client()

    def test_hello_endpoint(self):
        res = self.client.get('/api/hello')
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.get_json(), {'message': 'Hola desde nuevaweb'})


if __name__ == '__main__':
    unittest.main()
