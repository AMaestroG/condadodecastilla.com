import subprocess
import unittest

class CliHelpTestCase(unittest.TestCase):
    def test_help_includes_commands(self):
        result = subprocess.run([
            'python', 'cli.py', '--help'
        ], capture_output=True, text=True)
        self.assertEqual(result.returncode, 0)
        output = result.stdout
        self.assertIn('add_url', output)
        self.assertIn('run_consistency', output)

if __name__ == '__main__':
    unittest.main()
