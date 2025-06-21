import unittest
import importlib.util
import pathlib

class NuevaWebApiTestCase(unittest.TestCase):
    def setUp(self):
        # Dynamically load nuevaweb/flask_app.py as a module
        base_dir = pathlib.Path(__file__).resolve().parents[1]
        flask_app_path = base_dir / 'nuevaweb' / 'flask_app.py'
        spec = importlib.util.spec_from_file_location('nuevaweb_flask_app', flask_app_path)
        self.module = importlib.util.module_from_spec(spec)
        spec.loader.exec_module(self.module)
        self.client = self.module.app.test_client()

    def test_hello_endpoint(self):
        res = self.client.get('/api/hello')
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.get_json(), {'message': 'Hola desde nuevaweb'})

if __name__ == '__main__':
    unittest.main()
